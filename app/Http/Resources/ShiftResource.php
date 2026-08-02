<?php
namespace App\Http\Resources;

class ShiftResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, ['shortcut' => $this->shortcut]);
    }
}
