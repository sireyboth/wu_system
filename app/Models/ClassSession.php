<?php
namespace App\Models;

class ClassSession extends IModel
{
    protected $fillable = ['class_id', 'session_date', 'session_number', 'started_at', 'ended_at', 'locked_at', 'status', 'remark'];

    protected $casts = [
        'session_date' => 'date:Y-m-d',
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
        'locked_at'    => 'datetime',
    ];

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class, 'class_id');
    }

    public function sessionRosters()
    {
        return $this->hasMany(SessionRoster::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function qrTokens()
    {
        return $this->hasMany(QrToken::class);
    }

    /**
     * Shared between AttendanceHistoryExport and AttendanceHistoryImport so
     * the two always agree on what a column header means — export writes
     * it, import reads it back to resolve which real session a column is.
     */
    public static function historyColumnLabel(string $date, int $sessionNumber): string
    {
        return $sessionNumber > 1 ? "{$date} (S{$sessionNumber})" : $date;
    }

    /**
     * The students-by-dates grid for one class: every session date it has
     * ever held, crossed with every enrolled student's status that day.
     * Built for the "which week did I forget to take attendance" question,
     * which a per-student running total can't answer — a missing date in
     * `sessions` here IS a missed week, not a zero-attendance day.
     */
    public static function attendanceHistoryFor(int $classId): array
    {
        $sessions = static::where('class_id', $classId)
            ->orderBy('session_date')
            ->orderBy('session_number')
            ->get(['id', 'session_date', 'session_number', 'status']);
        $sessionIds = $sessions->pluck('id');

        $enrollments = CourseEnrollment::where('class_id', $classId)
            ->with('studentAcademicHistory.student.person')
            ->get();

        // session_roster_id included alongside status — the lecturer's own
        // History grid uses it to re-mark a student on a PAST date
        // directly (see LecturerPortalController/markManual), which the
        // live Attendance modal can't do since it only ever shows today.
        // Sourced from session_rosters, not attendance_records, so a cell
        // that was never marked at all still carries an id to write to.
        $rosters = SessionRoster::whereIn('class_session_id', $sessionIds)
            ->get(['id', 'class_session_id', 'course_enrollment_id'])
            ->groupBy('course_enrollment_id');

        $records = AttendanceRecord::whereIn('class_session_id', $sessionIds)
            ->get(['class_session_id', 'student_id', 'status'])
            ->groupBy('student_id');

        $students = $enrollments->map(function (CourseEnrollment $enrollment) use ($records, $rosters) {
            $student = $enrollment->studentAcademicHistory?->student;
            $person  = $student?->person;
            $nameKh  = trim(($person?->first_name_kh ?? '') . ' ' . ($person?->last_name_kh ?? ''));
            $nameEn  = trim(($person?->first_name ?? '') . ' ' . ($person?->last_name ?? ''));

            $rosterRows = $rosters->get($enrollment->id, collect())->keyBy('class_session_id');
            $recordRows = $records->get($student?->id, collect())->keyBy('class_session_id');

            $statuses = $rosterRows->map(fn ($roster, $sessionId) => [
                'session_roster_id' => $roster->id,
                'status'            => $recordRows->get($sessionId)?->status,
            ]);

            return [
                'course_enrollment_id' => $enrollment->id,
                'code'                 => $student?->code,
                'name'                 => $nameKh ?: ($nameEn ?: '—'),
                'statuses'             => $statuses,
            ];
        })->values();

        return [
            'sessions' => $sessions->map(fn (self $s) => [
                'id'             => $s->id,
                'date'           => $s->session_date->format('Y-m-d'),
                'session_number' => $s->session_number,
                'status'         => $s->status,
            ])->values(),
            'students' => $students,
        ];
    }
}
