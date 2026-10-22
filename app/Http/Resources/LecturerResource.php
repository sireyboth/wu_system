<?php
namespace App\Http\Resources;

class LecturerResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, ['code' => $this->code, 'has_account' => (bool) $this->user_id]);
    }
}
