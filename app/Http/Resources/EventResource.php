<?php
namespace App\Http\Resources;

class EventResource extends IResource
{

    protected function toList(): array
    {
        return to_list($this, [
            'title'           => $this->title,
            'subtitle'        => $this->subtitle,
            'start'           => dated_format($this->start, 'D, d M Y H:i:s'),
            'end'             => dated_format($this->end, 'D, d M Y H:i:s'),
            'color'           => $this->color,
            'repeat_freq'     => $this->repeat_freq,
            'repeat_interval' => $this->repeat_interval,
            'repeat_until'    => $this->repeat_until,
        ], false);
    }
}
