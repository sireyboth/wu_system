<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return to_list($this, [
            'code'          => $this->code,
            'admission_at'  => $this->admission_at?->format('Y-m-d'),
            'bacc_2_code'   => $this->bacc_2_code,
            'status'        => $this->status,
            'entrance_exam' => $this->entrance_exam,
            'exit_exam'     => $this->exit_exam,
            'batch'         => new BatchResource($this->whenLoaded('batch')),
            'shift'         => new ShiftResource($this->whenLoaded('shift')),
            'major'         => new MajorResource($this->whenLoaded('major')),
            'person'        => new PersonResource($this->whenLoaded('person')),
            'guardians'     => GuardianResource::collection($this->whenLoaded('guardians')),
        ], named: false);
    }
}
