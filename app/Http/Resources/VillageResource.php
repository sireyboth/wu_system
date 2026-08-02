<?php
namespace App\Http\Resources;

class VillageResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, ['commune_id' => $this->commune_id], is_extra: false);
    }
}
