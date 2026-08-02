<?php
namespace App\Http\Requests;

class TermRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            check_unique('terms', 'code', true),
            [
                'year'         => 'required|integer',
                'semester'     => 'integer',
                'display_name' => 'required|string',
                'start_date'   => 'required|date',
                'end_date'     => 'required|date',
                'is_active'    => 'boolean',
                'remark'       => 'nullable|max:500',
            ]
        );
    }
}
