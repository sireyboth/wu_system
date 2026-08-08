<?php
namespace App\Http\Requests;

class SubjectRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            DEFAULT_VALIDATE,
            check_exist('major_id', 'majors'),
            check_unique('code', 'subjects'),
            [
                'year_level' => 'nullable|string|max:50',
                'semester'   => 'nullable|string|max:50',
                'credit'     => 'nullable|integer',
            ]
        );
    }
}
