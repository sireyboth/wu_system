<?php
namespace App\Http\Resources;

class VillageResource extends IResource
{
    public function toList(): array
    {
        return [
            'id'         => $this->id,
            'commune_id' => $this->commune_id,
            'name'       => $this->name,
            'name_en'    => $this->name_en,
            'name_kh'    => $this->name_kh,
        ];
    }
}
