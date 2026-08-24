<?php
namespace App\Http\Requests;

class BatchRequest extends IRequest
class BatchRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            DEFAULT_VALIDATE,
            check_unique('shortcut', 'batches'),
            ['academic_year' => 'nullable|string']
        );
    }
}
