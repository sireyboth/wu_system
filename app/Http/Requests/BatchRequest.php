<?php
namespace App\Http\Requests;

class BatchRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            DEFAULT_VALIDATE,
            check_unique('batches'),
            ['academic_year' => 'nullable|string']
        );
    }
}
