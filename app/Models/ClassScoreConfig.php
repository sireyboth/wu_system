<?php
namespace App\Models;

/**
 * One row per class — the point allocation that class's lecturer chose.
 * class_id is UNIQUE, which is what isolates one lecturer's own class
 * from every other class, including another section of the same subject.
 */
class ClassScoreConfig extends IModel
{
    protected $fillable = [
        'class_id', 'homework_max', 'quiz_max', 'assignment_max',
        'midterm_max', 'final_max', 'attendance_max', 'total_weeks',
        'sessions_per_week', 'set_by', 'remark',
    ];

    public const COMPONENT_FIELDS = [
        'homework_max', 'quiz_max', 'assignment_max', 'midterm_max', 'final_max', 'attendance_max',
    ];

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class, 'class_id');
    }

    public function setter()
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    public function totalMax(): int
    {
        return collect(self::COMPONENT_FIELDS)->sum(fn($field) => (int) $this->{$field});
    }

    /**
     * Total score = (Present + Excused) / (Total Weeks × Sessions/Week) ×
     * attendance_max — computed live off attendance_records, never
     * stored, so it's always in sync with the actual ledger (see
     * attendance schema doc §4/§8). The expected-session count comes from
     * this config, not a count of class_sessions rows, because a
     * lecturer forgetting to start attendance one week must not silently
     * shrink the denominator and inflate everyone's score.
     */
    public function attendanceScore(int $presentCount, int $excusedCount): float
    {
        $expectedSessions = $this->total_weeks * $this->sessions_per_week;
        if ($expectedSessions <= 0) {
            return 0;
        }

        $ratio = ($presentCount + $excusedCount) / $expectedSessions;
        return round(min(1, $ratio) * $this->attendance_max, 2);
    }

    /**
     * Attendance score for every student in a class, in one query each
     * for present/excused counts rather than one query per roster row.
     * Returns [student_id => score]; empty if the class has no config
     * yet (nothing to compute against).
     */
    public static function attendanceScoresFor(int $classId, \Illuminate\Support\Collection $studentIds): array
    {
        $config = static::where('class_id', $classId)->first();
        if (! $config || $studentIds->isEmpty()) {
            return [];
        }

        $counts = \App\Models\AttendanceRecord::query()
            ->whereHas('classSession', fn($q) => $q->where('class_id', $classId))
            ->whereIn('student_id', $studentIds)
            ->whereIn('status', ['present', 'excused'])
            ->selectRaw('student_id, status, count(*) as total')
            ->groupBy('student_id', 'status')
            ->get()
            ->groupBy('student_id');

        return $studentIds->mapWithKeys(function ($studentId) use ($counts, $config) {
            $rows     = $counts->get($studentId, collect());
            $present  = (int) ($rows->firstWhere('status', 'present')->total ?? 0);
            $excused  = (int) ($rows->firstWhere('status', 'excused')->total ?? 0);

            return [$studentId => $config->attendanceScore($present, $excused)];
        })->all();
    }
}
