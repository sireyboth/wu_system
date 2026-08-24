<?php
namespace App\Http\Resources;

class TermResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, [
            'year'     => $this->year,
            'semester' => $this->semester,
            'code'     => $this->code,
            'name'     => $this->name,
            'display'  => $this->formated_name,
            'start'    => dated_format($this->start),
            'end'      => dated_format($this->end),
            'active'   => $this->active ?? false,
        ], false);
    }
}
