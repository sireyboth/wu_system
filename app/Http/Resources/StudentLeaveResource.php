<?php
namespace App\Http\Resources;

class StudentLeaveResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'starts_on'   => $this->starts_on?->format('Y-m-d'),
            'ends_on'     => $this->ends_on?->format('Y-m-d'),
            'status'      => $this->status,
            'student'     => new StudentResource($this->whenLoaded('student')),
            'approved_by' => $this->approver?->name,
        ], false);
    }
}
