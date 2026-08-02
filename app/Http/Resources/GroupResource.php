<?php
namespace App\Http\Resources;

class GroupResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, ['shortcut' => $this->shortcut]);
    }
}
