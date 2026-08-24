<?php
namespace App\Http\Resources;

class MajorResource extends IResource
class MajorResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, [
            'shortcut' => $this->shortcut,
            'faculty'  => new FacultyResource($this->whenLoaded('faculty')),
        ]);
    }
}
