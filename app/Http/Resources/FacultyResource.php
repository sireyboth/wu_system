<?php
namespace App\Http\Resources;

class FacultyResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'shortcut' => $this->shortcut,
            'majors'   => MajorResource::collection($this->whenLoaded('majors')),
        ]);
    }
}
