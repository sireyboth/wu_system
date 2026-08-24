<?php
namespace App\Http\Resources;

class GroupResource extends IResource
class GroupResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, ['shortcut' => $this->shortcut]);
    }
}
