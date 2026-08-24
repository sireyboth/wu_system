<?php
namespace App\Http\Resources;

class LecturerResource extends IResource
class LecturerResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, ['code' => $this->code]);
    }
}
