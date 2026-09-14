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
        'midterm_max', 'final_max', 'attendance_max', 'set_by', 'remark',
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
}
