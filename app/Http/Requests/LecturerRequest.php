<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LecturerRequest extends FormRequest
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
            ...PERSON_VALIDATE,
            ...ADDRESS_VALIDATE,
            'major_id'  => 'required|exists:majors,id|integer',
            'hired_at'  => 'required|date',
            'code'      => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('lecturers', 'code')
                    ->ignore($this->route('lecturer'))
                    ->withoutTrashed(),
            ],
        ];
    }
}


