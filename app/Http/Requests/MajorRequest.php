<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MajorRequest extends FormRequest
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
            'name_kh'    => 'required|string|min:3|max:255',
            'name_en'    => 'required|string|min:3|max:255',
            'shortcut'   => 'nullable|string|max:255',
            'remark'     => 'nullable|string|max:500',
            'faculty_id' => 'required|exists:faculties,id|integer',
        ];
    }
}
