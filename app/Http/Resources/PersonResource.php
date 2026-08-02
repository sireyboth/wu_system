<?php
namespace App\Http\Resources;

class PersonResource extends IResource
{
    protected function toList(): array
    {
        return [
            'id'            => $this->id,
            'first_name_kh' => $this->first_name_kh,
            'last_name_kh'  => $this->last_name_kh,
            'full_name_kh'  => $this->full_name_kh,

            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'full_name'     => $this->full_name,
            'nationality'   => new NationalityResource($this->whenLoaded('nationality')),
            'dob'           => $this->dob?->format('Y-m-d'),
            'sex'           => $this->sex,
            'email'         => $this->email ?? null,
            'phones'        => $this->phones ?? null,
            'addresses'     => AddressResource::collection($this->whenLoaded('addresses')),
        ];
    }
}
