<?php
namespace App\Http\Resources;

class StatusResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, ['shortcut' => $this->shortcut]);
    }
}
