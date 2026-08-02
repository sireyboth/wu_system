<?php
namespace App\Http\Resources;

class MajorResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'shortcut' => $this->shortcut,
            'faculty'  => new FacultyResource($this->whenLoaded('faculty')),
        ]);
    }
}
