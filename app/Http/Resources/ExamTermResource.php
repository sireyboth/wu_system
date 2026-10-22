<?php
namespace App\Http\Resources;

class ExamTermResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, [
            'category'   => new ExamCategoryResource($this->whenLoaded('category')),
            'campus'     => new CampusResource($this->whenLoaded('campus')),
            'title'      => $this->title,
            'exam_date'  => $this->exam_date?->format('Y-m-d'),
            'time_slots' => $this->time_slots ?? [],
            'is_active'  => (bool) $this->is_active,
        ], false);
    }
}
