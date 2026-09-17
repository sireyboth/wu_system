<?php
namespace App\Http\Requests;

class ExamCategoryRequest extends IRequest
{
    protected function formData(): array
    {
        return DEFAULT_VALIDATE;
    }
}
