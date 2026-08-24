<?php
namespace App\Http\Resources;

class CommuneResource extends IResource
{
    public function toList(): array
    {
        return [
            'id'          => $this->id,
            'district_id' => $this->district_id,
            'name'        => $this->full_name,
            'name_en'     => $this->name_en,
            'name_kh'     => $this->name_kh,
        ];
    }
}
