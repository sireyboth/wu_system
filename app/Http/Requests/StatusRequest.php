<?php
namespace App\Http\Requests;

class StatusRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(DEFAULT_VALIDATE, ['shortcut' => 'required|string|max:50']);
    }
}
