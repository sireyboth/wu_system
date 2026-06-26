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
            'name_kh' => 'required|string|min:3|max:255',
            'name_en' => 'required|string|min:3|max:255',
            'code'    => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('lecturers', 'code')
                    ->ignore($this->route('lecturer'))
                    ->withoutTrashed(),
            ],
            'remark'  => 'nullable|string|max:500',
        ];
    }
}
