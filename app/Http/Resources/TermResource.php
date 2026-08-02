<?php
namespace App\Http\Resources;

class TermResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'year'       => $this->year,
            'semester'   => $this->semester,
            'code'       => $this->code,
            'display_name'       => $this->display_name,
            'full_name'  => $this->full_name, // accessor called automatically
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date'   => $this->end_date?->format('Y-m-d'),
            'is_active'  => $this->is_active,
        ], false);
    }
}
