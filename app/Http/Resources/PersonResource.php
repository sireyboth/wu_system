<?php
namespace App\Http\Resources;

class PersonResource extends IResource
{
    public function toList(): array
    {
        return [
            'id'            => $this->id,
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'first_name_kh' => $this->first_name_kh,
            'last_name_kh'  => $this->last_name_kh,
            'nationality'   => new NationalityResource($this->nationality),
            'dob'           => $this->dob?->format('Y-m-d'),
            'sex'           => $this->sex,
            'email'         => $this->email ?? null,
            'phones'        => $this->phones ?? null,
            'addresses'     => AddressResource::collection($this->whenLoaded('addresses')),
        ];
    }
}
