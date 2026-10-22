<?php
namespace App\Http\Requests;

class TermRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            check_unique('terms', 'code', true),
            [
                'year'       => 'required|integer|min:2000|max:2100',
                'semester'   => 'required|integer|min:1|max:2',
                'name'       => 'required|string|max:100',
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after:start_date',
                'is_active'  => 'nullable|boolean',
                'remark'     => 'nullable|string|max:500',
            ]
        );
    }
}
