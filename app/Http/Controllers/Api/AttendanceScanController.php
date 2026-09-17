<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceVerification;
use App\Models\ClassSession;
use App\Models\QrToken;
use App\Models\SessionRoster;
use App\Models\Student;
use App\Models\StudentDevice;
use Illuminate\Http\Request;

/**
 * Public, no-login scan endpoint — a student opens the QR's link on their
 * own phone and types their own student code. No student accounts exist
 * in this system, so identity here is self-asserted (the student code)
 * rather than authenticated; the rotating token + one-mark-per-session
 * constraint are the actual guardrails, not a login.
 */
class AttendanceScanController extends Controller
{
    /** Two other scans from the same device within this many seconds reads as "one person tapping through codes". */
    private const RAPID_SUCCESSION_SECONDS = 30;

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'token'        => 'required|string',
            'student_code' => 'required|string',
            'device_id'    => 'nullable|string|max:255',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
        ]);

        $qrToken = QrToken::where('token_hash', hash('sha256', $validated['token']))->first();

        if (! $qrToken || $qrToken->expires_at->isPast()) {
            return no_data('This QR code has expired — ask your lecturer to refresh it and scan again.', 422);
        }

        $session = ClassSession::with('classSection.campus')->find($qrToken->class_session_id);
        if (! $session || $session->status !== 'open') {
            return no_data('This attendance session is no longer open.', 422);
        }

        // Geofence — only enforced for a campus that actually has one
        // configured (see Campus::hasGeofence()). A campus with no
        // coordinates set allows scanning from anywhere, same as before
        // this feature existed.
        $campus = $session->classSection?->campus;
        if ($campus && $campus->hasGeofence()) {
            if (! isset($validated['latitude'], $validated['longitude'])) {
                return no_data('This class requires your location to check in. Please allow location access and try again.', 422);
            }

            $distance = $campus->distanceInMetersFrom($validated['latitude'], $validated['longitude']);
            if ($distance > $campus->attendance_radius_meters) {
                return no_data("You're too far from campus to check in for this class. Please make sure you're on campus and try again.", 422);
            }
        }

        $student = Student::where('code', $validated['student_code'])->first();
        if (! $student) {
            return no_data("Student code \"{$validated['student_code']}\" not found. Check the code and try again.", 422);
        }

        // The hard eligibility gate — see attendance schema doc §6.
        if (! $student->canAttend()) {
            return no_data('Go to Registrar Office', 403);
        }

        $roster = SessionRoster::where('class_session_id', $session->id)
            ->whereHas('courseEnrollment.studentAcademicHistory', fn($q) => $q->where('student_id', $student->id))
            ->first();

        if (! $roster) {
            return no_data("You're not enrolled in this class, so this scan can't be recorded.", 422);
        }

        $existing = AttendanceRecord::where('class_session_id', $session->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            return has_data(['status' => $existing->status], "Already marked {$existing->status} for this session.");
        }

        $device = $this->resolveDevice($validated['device_id'] ?? null);

        // Hard block, not just a flag — scoped to THIS session only. A
        // device that already checked someone else in for this exact
        // class right now is the clearest possible "passing a phone down
        // the row" case, so this one gets refused outright rather than
        // silently allowed-and-flagged like the cross-session/day case
        // scoreRisk() below still just flags.
        if ($device) {
            $alreadyCheckedInSomeoneElseHere = AttendanceRecord::where('device_id', $device->id)
                ->where('class_session_id', $session->id)
                ->where('student_id', '!=', $student->id)
                ->exists();

            if ($alreadyCheckedInSomeoneElseHere) {
                return no_data("This device already checked someone else in for this class. You can't scan in on someone else's behalf — please use your own device, or ask your lecturer for help.", 422);
            }
        }

        $record = AttendanceRecord::create([
            'class_session_id'  => $session->id,
            'student_id'        => $student->id,
            'session_roster_id' => $roster->id,
            'status'            => 'present',
            'method'            => 'qr',
            'marked_at'         => now(),
            'device_id'         => $device?->id,
            'ip_address'        => $request->ip(),
        ]);

        $rapidSharedDevice = $device && $this->scoreRisk($record, $device);

        // Every other flag (a device that's shared but not in rapid
        // succession) stays silent — same message either way, so someone
        // legitimately borrowing a phone on a different day never notices
        // anything. Only the strongest signal (two different students
        // checked in from the same device within RAPID_SUCCESSION_SECONDS
        // — a phone being passed down the row right now) gets a warning,
        // since that's specific enough that a false positive is unlikely.
        $message = $rapidSharedDevice
            ? "You're marked present. Note: you're not allowed to check in for someone else — if we find out, we will contact you or look into it."
            : "You're marked present. Welcome!";

        return has_data(['status' => 'present'], $message);
    }

    /**
     * Finds or creates the device row for this browser's self-reported id.
     * The raw id is hashed before storage — same reasoning as qr_tokens'
     * token_hash: this is a lookup key, not something worth keeping in
     * cleartext.
     */
    private function resolveDevice(?string $rawDeviceId): ?StudentDevice
    {
        if (! $rawDeviceId) {
            return null;
        }

        $fingerprint = hash('sha256', $rawDeviceId);
        $device      = StudentDevice::firstWhere('fingerprint', $fingerprint);

        if ($device) {
            $device->update(['last_seen_at' => now()]);
            return $device;
        }

        return StudentDevice::create([
            'fingerprint'   => $fingerprint,
            'first_seen_at' => now(),
            'last_seen_at'  => now(),
        ]);
    }

    /**
     * The one signal this "type your own code" model can actually detect
     * well: the same browser/device scanning in on behalf of more than
     * one student. Flagging never blocks the scan — the student is still
     * marked present, this just queues the record for a registrar to
     * look at. See attendance schema doc §6/§8.
     *
     * Returns whether this specific scan hit the rapid-succession
     * signal — the caller uses that (and only that) to decide whether to
     * warn the student directly; every other flag stays silent.
     */
    private function scoreRisk(AttendanceRecord $record, StudentDevice $device): bool
    {
        $recentOtherScans = AttendanceRecord::where('device_id', $device->id)
            ->where('student_id', '!=', $record->student_id)
            ->where('id', '!=', $record->id)
            ->orderByDesc('marked_at')
            ->get(['id', 'student_id', 'marked_at']);

        if ($recentOtherScans->isEmpty()) {
            return false;
        }

        $riskScore = min(100, 50 + $recentOtherScans->count() * 10);
        $signals   = [
            'device_shared_with_student_ids' => $recentOtherScans->pluck('student_id')->unique()->values(),
            'device_first_seen'              => $device->wasRecentlyCreated,
        ];

        $isRapidSuccession = false;
        $mostRecent        = $recentOtherScans->first();
        if ($mostRecent->marked_at && $record->marked_at->diffInSeconds($mostRecent->marked_at) <= self::RAPID_SUCCESSION_SECONDS) {
            $isRapidSuccession = true;
            $riskScore = min(100, $riskScore + 30);
            $signals['rapid_successive_scan_seconds'] = $record->marked_at->diffInSeconds($mostRecent->marked_at);
        }

        AttendanceVerification::create([
            'attendance_record_id' => $record->id,
            'risk_score'           => $riskScore,
            'signals'              => $signals,
            'flagged'              => true,
        ]);

        return $isRapidSuccession;
    }
}
