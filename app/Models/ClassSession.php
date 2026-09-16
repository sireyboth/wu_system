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

        $enrollments = CourseEnrollment::where('class_id', $classId)
            ->with('studentAcademicHistory.student.person')
            ->get();

        $records = AttendanceRecord::whereIn('class_session_id', $sessions->pluck('id'))
            ->get(['class_session_id', 'student_id', 'status'])
            ->groupBy('student_id');

        $students = $enrollments->map(function (CourseEnrollment $enrollment) use ($records) {
            $student = $enrollment->studentAcademicHistory?->student;
            $person  = $student?->person;
            $nameKh  = trim(($person?->first_name_kh ?? '') . ' ' . ($person?->last_name_kh ?? ''));
            $nameEn  = trim(($person?->first_name ?? '') . ' ' . ($person?->last_name ?? ''));

            $statuses = $records->get($student?->id, collect())
                ->pluck('status', 'class_session_id');

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
