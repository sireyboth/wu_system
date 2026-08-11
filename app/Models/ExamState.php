<?php
namespace App\Models;

class ExamState extends IModel
{
    protected $fillable = [
        'no', 'room', 'major', 'student_total', 'shift', 'invigilators',
        'degree', 'majors', 'remark', 'absences', 'exam_date',
    ];

    protected $casts = [
        'majors'       => 'array',
        'absences'     => 'array',
        'invigilators' => 'array',
        'exam_date'    => 'date:Y-m-d',
    ];

    protected $appends = ['floor'];

    public function getFloorAttribute(): ?int
    {
        if (! preg_match('/(\d+)/', $this->room, $matches)) {
            return null; // no digits at all — shouldn't happen, but fail safe
        }

        $digits = (int) $matches[1];

        // 3-digit room numbers: first digit is the floor (104 -> 1, 501 -> 5)
        return intdiv($digits, 100);
    }

    public function getFloorLabelAttribute(): string
    {
        return $this->floor ? "{$this->floor} Floor" : 'Unassigned';
    }
}
