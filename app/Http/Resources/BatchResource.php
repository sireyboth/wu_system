<?php
namespace App\Http\Resources;

class BatchResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'shortcut'      => $this->shortcut,
            'academic_year' => $this->academic_year,
        ]);
    }
}
