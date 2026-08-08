<?php
namespace App\Http\Resources;

class ExamStateResource extends IResource
{
    public function toList(): array
    {
        return [
            'id'          => $this->id,
            'no'          => $this->no ?? 0,
            'floor_order' => $this->floor_order ?? 0,
            'floor'       => $this->floor,
            'room'        => $this->room,
            'shift'       => $this->shift,
            'major'       => $this->major,
            'students'    => $this->students ?? 0,
            'degree'      => $this->degree,
            'majors'      => $this->majors,
            'sort_order'  => $this->sort_order ?? 0,
            'exam_date'   => $this->exam_date?->format('Y-m-d'),
            'remark'      => $this->remark,
            'created_at'  => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
