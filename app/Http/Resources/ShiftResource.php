<?php
namespace App\Http\Resources;

class ShiftResource extends IResource
class ShiftResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, ['shortcut' => $this->shortcut]);
    }
}
