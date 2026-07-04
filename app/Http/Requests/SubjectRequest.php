<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...DEFAULT_VALIDATE,
            'major_id'  => 'required|exists:majors,id|integer',
            'code'     => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('subjects', 'code')
                    ->ignore($this->route('subject'))
                    ->withoutTrashed(),
            ],
            'year_level'     => 'nullable|string|max:50',
            'semester' => 'nullable|string|max:50',
            'credit'   => 'nullable|integer',
        ];
    }
}
