<?php
namespace App\Http\Resources;

class NationalityResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, ['code' => $this->code], is_extra: false);
    }
}
