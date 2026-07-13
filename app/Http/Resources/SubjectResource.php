<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return to_list($this, [
            'code'       => $this->code,
            'major'      => new MajorResource($this->whenLoaded('major')),
            'year_level' => $this->year_level,
            'semester'   => $this->semester,
            'credit'     => $this->credit,
        ]);
    }
}
