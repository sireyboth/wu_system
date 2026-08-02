<?php
namespace App\Http\Resources;

class ProvinceResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, is_extra: false);
    }
}
