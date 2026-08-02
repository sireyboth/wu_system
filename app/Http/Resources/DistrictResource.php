<?php
namespace App\Http\Resources;

class DistrictResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, ['province_id' => $this->province_id], is_extra: false);
    }
}
