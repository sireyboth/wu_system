<?php
namespace App\Http\Requests;

class EventRequest extends IRequest
{
    protected function formData(): array
    {
        return [
            'title'           => 'required|string|max:255',
            'subtitle'        => 'nullable|string|max:255',
            'start'           => 'required|date',
            'end'             => 'nullable|date|after_or_equal:start',
            'color'           => 'nullable|string|max:7',
            'repeat_freq'     => 'nullable|in:daily,weekly,monthly',
            'repeat_interval' => 'nullable|integer|min:1',
            'repeat_until'    => 'nullable|date|after_or_equal:start',
        ];
    }
}
