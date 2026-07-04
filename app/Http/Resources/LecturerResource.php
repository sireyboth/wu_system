<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LecturerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return to_list($this, [
            'code'     => $this->code,
            'hired_at' => $this->hired_at?->format('Y-m-d'),
            'major'    => new MajorResource($this->whenLoaded('major')),
            'person'   => new PersonResource($this->whenLoaded('person')),
        ], named: false);
    }
}
