<?php
namespace App\Http\Resources;

class ExamStateResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, [
            'no'            => $this->no ?? 0,
            'room'          => $this->room,
            'floor'         => $this->floor ?? 0,
            'floor_label'   => $this->floor_label,
            'shift'         => $this->shift,
            'major'         => $this->major,
            'student_total' => $this->student_total ?? 0,
            'degree'        => $this->degree,
            'majors'        => $this->majors ?? [],
            'absences'      => $this->absences ?? [],
            'invigilators'  => $this->invigilators ?? [],
            'exam_date'     => dated_format($this->exam_date),
        ], false);
    }
}
