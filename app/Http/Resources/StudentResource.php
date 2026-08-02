<?php
namespace App\Http\Resources;

class StudentResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'code'           => $this->code,
            'bacc_2_code'    => $this->bacc_2_code,
            'entrance_exam'  => $this->entrance_exam,
            'exit_exam'      => $this->exit_exam,
            'degree_type'    => $this->degree_type,
            'from_school'    => $this->from_school,
            'intake'         => $this->intake,
            'scholarship'    => $this->scholarship,
            'admission_date' => $this->admission_date?->format('Y-m-d'),
            'person'         => new PersonResource($this->whenLoaded('person')),
            'guardians'      => GuardianResource::collection($this->whenLoaded('guardians')),
        ], false);
    }
}
