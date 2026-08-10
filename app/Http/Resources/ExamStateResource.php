<?php
namespace App\Http\Resources;

class ExamStateResource extends IResource
{
    public function toList(): array
    {
        return [
            'id'            => $this->id,
            'no'            => $this->no ?? 0,
            'room'          => $this->room,
            'floor'         => $this->floor,
            'floor_label'   => $this->floor_label,
            'shift'         => $this->shift,
            'major'         => $this->major,
            'student_total' => $this->student_total ?? 0,
            'degree'        => $this->degree,
            'majors'        => $this->majors,
            'absences'      => $this->absences,
            'exam_date'     => $this->exam_date?->format('Y-m-d'),

            'remark'        => $this->remark,
            'created_at'    => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'    => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
