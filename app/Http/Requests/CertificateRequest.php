<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CertificateRequest extends FormRequest
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
             ...check_exist('student_id', 'students'),
            'issue_date'    => 'required|date',
            'full_date_kh'  => 'nullable|string|max:255',
            'short_date_kh' => 'nullable|string|max:255',
            'status'        => 'nullable|string|max:50',
            'type'          => 'nullable|string|max:50',
            'remark'        => 'nullable|string|max:500',
        ];
    }
}
