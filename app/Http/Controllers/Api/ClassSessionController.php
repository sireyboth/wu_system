<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\ClassSection;
use App\Models\ClassSession;
use App\Models\QrToken;
use App\Models\SessionRoster;
use App\Models\TeacherAssignment;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Lecturer-side of the attendance flow — starting a session, rotating the
 * QR token, watching who's scanned in, and locking the session at the
 * end. Ownership is checked the same way as LecturerPortalController
 * (via TeacherAssignment, Admin bypasses via Gate::before), not by the
 * registrar's `class.*` permission — this is the lecturer's own class.
 */
class ClassSessionController extends Controller
{
    // These are deliberately different numbers. ROTATION_INTERVAL is how
    // often the *displayed* QR image changes — short, so a screenshotted
    // or shared QR goes stale quickly. TOKEN_VALIDITY_SECONDS is how long
    // each individual token stays scannable *after* it's issued — long
    // enough to cover the real human flow (open camera, scan, switch to
    // the browser, type a student code, submit), which routinely takes
    // well over 8 seconds. Because old tokens keep working until their
    // own validity window ends, several tokens are "alive" at once — a
    // sliding window, not a hard cutover — so a student who scanned right
    // before the image rotated still isn't punished for it.
    private const ROTATION_INTERVAL_SECONDS = 8;
    private const TOKEN_VALIDITY_SECONDS    = 45;

    /** A real teaching day has at most this many separate attendance checks (e.g. one before a break, one after). */
    private const MAX_SESSIONS_PER_DAY = 2;

    private function assertOwnsClass(ClassSection $class): void
    {
        if (auth()->user()->hasRole('Admin')) {
            return;
        }

        $lecturerId = auth()->user()->lecturer?->id;
        $owns = $lecturerId && TeacherAssignment::where('class_id', $class->id)
            ->where('lecturer_id', $lecturerId)
            ->exists();

        if (! $owns) {
            abort(403, "You aren't assigned to this class.");
        }
    }

    private function assertOwnsSession(ClassSession $session): void
    {
        $this->assertOwnsClass($session->classSection);
    }

    /**
     * Starts (or resumes) today's session for this class. A new session
     * takes its roster from whoever is enrolled at that moment; resuming a
     * still-OPEN session tops the roster up with anyone enrolled since
     * (see syncMissingRosterRows) — otherwise opening Attendance once
     * before enrolling, or adding a late student, leaves that day's
     * session permanently short. A locked (submitted) session is never
     * touched.
     */
    public function start(Request $request, ClassSection $class)
    {
        $this->assertOwnsClass($class);

        $validated = $request->validate([
            'session_date' => 'nullable|date',
            // Explicit opt-in only — a class can meet more than once on the
            // same date (e.g. one 3-hour block split by a break into two
            // attendance checks). Just reopening the Attendance modal must
            // never silently spawn a new session on top of a locked one;
            // the lecturer has to deliberately ask for the next one.
            'new_session'  => 'nullable|boolean',
        ]);
        $date     = $validated['session_date'] ?? now()->toDateString();
        $forceNew = $validated['new_session'] ?? false;

        return execute(function () use ($class, $date, $forceNew) {
            // Explicit find-then-create rather than firstOrCreate() — this
            // is hit twice in a row from the UI (open modal, then poll),
            // and a plain firstOrCreate() intermittently missed the
            // just-created row under SQLite's date-cast serialization,
            // tripping the unique constraint.
            $latest = ClassSession::where('class_id', $class->id)
                ->whereDate('session_date', $date)
                ->orderByDesc('session_number')
                ->first();

            if ($forceNew && $latest && $latest->session_number >= self::MAX_SESSIONS_PER_DAY) {
                return no_data('This class already has ' . self::MAX_SESSIONS_PER_DAY . ' sessions today — that\'s the most a single day allows.', 422);
            }

            // Only actually start a new one if there's none yet today, or
            // the lecturer explicitly asked for the next one AND the
            // current latest is locked (asking again on an already-open
            // session just resumes it, same as before).
            $shouldCreateNew = ! $latest || ($forceNew && $latest->status === 'locked');

            if ($shouldCreateNew) {
                $session = ClassSession::create([
                    'class_id'       => $class->id,
                    'session_date'   => $date,
                    'session_number' => $latest ? $latest->session_number + 1 : 1,
                    'started_at'     => now(),
                    'status'         => 'open',
                ]);

                $enrollmentIds = $class->courseEnrollments()->where('status', 'enrolled')->pluck('id');
                foreach ($enrollmentIds as $enrollmentId) {
                    SessionRoster::create([
                        'class_session_id'     => $session->id,
                        'course_enrollment_id' => $enrollmentId,
                    ]);
                }

                $message = $latest ? "Session {$session->session_number} started." : 'Session started.';
            } else {
                $session = $latest;
                if ($session->status === 'open') {
                    $this->syncMissingRosterRows($session, $class);
                }
                $message = 'Session resumed.';
            }

            return has_data($this->sessionPayload($session->fresh()), $message);
        });
    }

    /**
     * Add-only: puts anyone currently enrolled but not yet on this open
     * session's roster onto it. Never removes or edits an existing roster
     * row, so scans already recorded are unaffected. The unique index on
     * (class_session_id, course_enrollment_id) makes a concurrent double
     * call (open modal + poll) safe — the loser's duplicate is ignored.
     */
    private function syncMissingRosterRows(ClassSession $session, ClassSection $class): void
    {
        $missing = $class->courseEnrollments()
            ->where('status', 'enrolled')
            ->whereNotIn('id', $session->sessionRosters()->select('course_enrollment_id'))
            ->pluck('id');

        foreach ($missing as $enrollmentId) {
            try {
                SessionRoster::create([
                    'class_session_id'     => $session->id,
                    'course_enrollment_id' => $enrollmentId,
                ]);
            } catch (UniqueConstraintViolationException) {
                // Already added by a concurrent request — nothing to do.
            }
        }
    }

    /** Live monitor view: roster + who's scanned in so far, per student. */
    public function show(ClassSession $session)
    {
        $this->assertOwnsSession($session);
        return has_data($this->sessionPayload($session));
    }

    private function sessionPayload(ClassSession $session): array
    {
        $rosters = $session->sessionRosters()
            ->with([
                'courseEnrollment.studentAcademicHistory.student.person',
                'attendanceRecord',
            ])
            ->get()
            ->map(function (SessionRoster $roster) {
                $student = $roster->courseEnrollment->studentAcademicHistory->student;
                $person  = $student->person;

                return [
                    'session_roster_id'    => $roster->id,
                    'attendance_record_id' => $roster->attendanceRecord?->id,
                    'student_id'           => $student->id,
                    'student_code'         => $student->code,
                    'student_name'         => trim(($person?->first_name_kh ?? '') . ' ' . ($person?->last_name_kh ?? '')) ?: trim(($person?->first_name ?? '') . ' ' . ($person?->last_name ?? '')),
                    'status'               => $roster->attendanceRecord?->status ?? 'pending',
                    'method'               => $roster->attendanceRecord?->method,
                    'marked_at'            => $roster->attendanceRecord?->marked_at?->format('Y-m-d H:i:s'),
                ];
            });

        return [
            'id'             => $session->id,
            'class_id'       => $session->class_id,
            'session_date'   => $session->session_date->format('Y-m-d'),
            'session_number' => $session->session_number,
            'status'         => $session->status,
            'started_at'    => $session->started_at?->format('Y-m-d H:i:s'),
            'locked_at'     => $session->locked_at?->format('Y-m-d H:i:s'),
            'total'         => $rosters->count(),
            'present_count' => $rosters->whereIn('status', ['present', 'late'])->count(),
            'roster'        => $rosters->values(),
        ];
    }

    /**
     * Issues a fresh rotating token if the current one has expired (or
     * none exists yet), otherwise hands back the still-valid one — the
     * lecturer's browser polls this every few seconds to keep the
     * displayed QR current. The raw token is returned here and NEVER
     * stored — only its hash is, in qr_tokens.
     */
    public function qrToken(ClassSession $session)
    {
        $this->assertOwnsSession($session);

        if ($session->status !== 'open') {
            return no_data('This session is no longer open.', 422);
        }

        $latest = QrToken::where('class_session_id', $session->id)->latest('sequence')->first();

        // Only the *displayed image* rotates on this cadence — the token
        // issued 8 seconds ago is still perfectly valid to scan for
        // TOKEN_VALIDITY_SECONDS, it just isn't the one on screen anymore.
        if ($latest && $latest->issued_at->diffInSeconds(now()) < self::ROTATION_INTERVAL_SECONDS) {
            $nextRotationIn = self::ROTATION_INTERVAL_SECONDS - $latest->issued_at->diffInSeconds(now());
            return has_data([
                'token'             => null,
                'seconds_remaining' => max(1, $nextRotationIn),
            ]);
        }

        $rawToken = Str::random(32);
        $token = QrToken::create([
            'class_session_id' => $session->id,
            'token_hash'       => hash('sha256', $rawToken),
            'sequence'         => ($latest?->sequence ?? 0) + 1,
            'issued_at'        => now(),
            'expires_at'       => now()->addSeconds(self::TOKEN_VALIDITY_SECONDS),
        ]);

        return has_data([
            'token'             => $rawToken,
            'expires_at'        => $token->expires_at->format('Y-m-d H:i:s'),
            'seconds_remaining' => self::ROTATION_INTERVAL_SECONDS,
        ]);
    }

    /**
     * Lecturer's manual override — mark one student present/late/absent/
     * excused directly, for whoever didn't or can't scan (phone dead,
     * forgot it, approved leave, etc).
     */
    public function markManual(Request $request, ClassSession $session)
    {
        $this->assertOwnsSession($session);

        $validated = $request->validate([
            'session_roster_id' => 'required|integer|exists:session_rosters,id',
            'status'            => 'required|in:present,late,absent,excused',
        ]);

        return execute(function () use ($validated, $session) {
            $roster = SessionRoster::where('id', $validated['session_roster_id'])
                ->where('class_session_id', $session->id)
                ->firstOrFail();

            $studentId = $roster->courseEnrollment->studentAcademicHistory->student_id;

            AttendanceRecord::updateOrCreate(
                ['class_session_id' => $session->id, 'student_id' => $studentId],
                [
                    'session_roster_id' => $roster->id,
                    'status'            => $validated['status'],
                    'method'            => 'manual',
                    'marked_at'         => now(),
                ]
            );

            return has_data($this->sessionPayload($session->fresh()), 'Marked.');
        });
    }

    /**
     * Ends the session — anyone left in the roster with no mark becomes
     * absent (a real, stored fact, not just "missing"), and the session
     * locks so the QR stops working and no more scans are accepted.
     */
    public function submit(ClassSession $session)
    {
        $this->assertOwnsSession($session);

        if ($session->status !== 'open') {
            return no_data('This session is already closed.', 422);
        }

        return execute(function () use ($session) {
            $unmarked = $session->sessionRosters()->whereDoesntHave('attendanceRecord')->get();

            foreach ($unmarked as $roster) {
                AttendanceRecord::create([
                    'class_session_id'  => $session->id,
                    'student_id'        => $roster->courseEnrollment->studentAcademicHistory->student_id,
                    'session_roster_id' => $roster->id,
                    'status'            => 'absent',
                    'method'            => 'auto_absent',
                    'marked_at'         => now(),
                ]);
            }

            $session->update(['status' => 'locked', 'ended_at' => now(), 'locked_at' => now()]);

            return has_data($this->sessionPayload($session->fresh()), 'Session closed.');
        });
    }

    /**
     * Lecturer's own request to change a record — never applies the
     * change itself, only queues it. attendance_corrections is "the only
     * path to change a locked record" per the attendance schema doc, and
     * a registrar has to approve it (AttendanceReviewController) before
     * the underlying attendance_records row actually moves.
     */
    public function requestCorrection(Request $request, AttendanceRecord $record)
    {
        $this->assertOwnsSession($record->classSession);

        $validated = $request->validate([
            'new_status' => 'required|in:present,late,absent,excused',
            'reason'     => 'required|string|max:500',
        ]);

        if ($validated['new_status'] === $record->status) {
            return no_data("This record is already marked {$record->status}.", 422);
        }

        return execute(function () use ($validated, $record) {
            $correction = \App\Models\AttendanceCorrection::create([
                'attendance_record_id' => $record->id,
                'requested_by'         => auth()->id(),
                'old_status'           => $record->status,
                'new_status'           => $validated['new_status'],
                'reason'               => $validated['reason'],
                'status'               => 'pending',
            ]);

            return has_data(null, 'Correction requested — a registrar will review it.');
        });
    }
}
