<?php
namespace App\Http\Resources;

class CampusResource extends IResource
{

    protected function toList(): array
    {
        return to_list($this, ['shortcut' => $this->shortcut]);
    }
}
