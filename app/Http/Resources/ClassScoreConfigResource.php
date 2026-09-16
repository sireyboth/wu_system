<?php
namespace App\Http\Resources;

class ClassScoreConfigResource extends IResource
{
    protected function toList(): array
    {
        if ($this->resource === null) {
            return [];
        }

        return to_list($this, [
            'class_id'       => $this->class_id,
            'homework_max'   => $this->homework_max,
            'quiz_max'       => $this->quiz_max,
            'assignment_max' => $this->assignment_max,
            'midterm_max'    => $this->midterm_max,
            'final_max'      => $this->final_max,
            'attendance_max' => $this->attendance_max,
            'total_weeks'       => $this->total_weeks,
            'sessions_per_week' => $this->sessions_per_week,
            'total_max'      => $this->totalMax(),
            'set_by'         => $this->setter?->name,
        ], false);
    }
}
