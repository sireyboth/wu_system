<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MajorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return to_list($this, [
            'shortcut' => $this->shortcut,
            'faculty_id'=> $this->faculty_id,
            'faculty'  => new FacultyResource($this->whenLoaded('faculty')),
        ]);
    }
}
