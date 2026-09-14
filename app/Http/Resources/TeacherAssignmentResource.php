<?php
namespace App\Http\Resources;

class TeacherAssignmentResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'role'          => $this->role,
            'assigned_from' => $this->assigned_from?->format('Y-m-d'),
            'assigned_to'   => $this->assigned_to?->format('Y-m-d'),
            'lecturer'      => new LecturerResource($this->whenLoaded('lecturer')),
            'class'         => new ClassSectionResource($this->whenLoaded('classSection')),
        ], false);
    }
}
