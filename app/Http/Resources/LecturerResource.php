<?php
namespace App\Http\Resources;

class LecturerResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, ['code' => $this->code]);
    }
}
