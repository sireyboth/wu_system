<?php
namespace App\Http\Resources;

class CommuneResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, ['district_id' => $this->district_id], is_extra: false);
    }
}
