<?php
namespace App\Http\Resources;

class AddressResource extends IResource
{
    protected function toList(): array
    {
        return [
            'type'     => $this->type,
            'street'   => $this->street,
            'house_no' => $this->house_no,
            'province' => new ProvinceResource($this->whenLoaded('province')),
            'district' => new DistrictResource($this->whenLoaded('district')),
            'commune'  => new CommuneResource($this->whenLoaded('commune')),
            'village'  => new VillageResource($this->whenLoaded('village')),
        ];
    }
}
