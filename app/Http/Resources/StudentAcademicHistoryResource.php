<?php
namespace App\Http\Resources;

class StudentAcademicHistoryResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'year_level'     => $this->year_level,
            'semester'       => $this->semester,
            'effective_date' => $this->effective_date?->format('Y-m-d'),
            'is_current'     => (bool) $this->is_current,
            'term'           => new TermResource($this->whenLoaded('term')),
            'batch'          => new BatchResource($this->whenLoaded('batch')),
            'major'          => new MajorResource($this->whenLoaded('major')),
            'group'          => new GroupResource($this->whenLoaded('group')),
            'shift'          => new ShiftResource($this->whenLoaded('shift')),
            'campus'         => new CampusResource($this->whenLoaded('campus')),
            'status'         => new StatusResource($this->whenLoaded('status')),
        ], false);
    }
}
