<?php
namespace App\Http\Resources;

class StudentSnapshotResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'student'        => new StudentResource($this->whenLoaded('student')),
            'batch'          => new BatchResource($this->whenLoaded('batch')),
            'campus'         => new CampusResource($this->whenLoaded('campus')),
            'major'          => new MajorResource($this->whenLoaded('major')),
            'group'          => new GroupResource($this->whenLoaded('group')),
            'shift'          => new ShiftResource($this->whenLoaded('shift')),
            'status'         => new StatusResource($this->whenLoaded('status')),
            'effective_date' => $this->effective_date?->format('Y-m-d'),
            'is_current'     => $this->is_current,
        ], false);
    }
}
