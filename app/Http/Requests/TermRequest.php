<?php
namespace App\Http\Requests;

class TermRequest extends IRequest
{

    public function formData(): array
    {
        return array_merge(
            check_unique('terms', 'code'),
            [
                'year'     => 'required|integer',
                'semester' => 'required|integer',
                'name'     => 'required|string|max:255',
                'start'    => 'required|date',
                'end'      => 'required|date|after:start',
                'active'   => 'nullable|boolean',
                'remark'   => 'nullable|string|max:360',
            ]);
    }
}
