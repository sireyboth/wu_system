<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BatchRequest extends FormRequest
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
            'shortcut'      => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('batches', 'shortcut')
                    ->ignore($this->route('batch'))
                    ->withoutTrashed(),
            ],
            'academic_year' => 'nullable|string',
        ];
    }
}
