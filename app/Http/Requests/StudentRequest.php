<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
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
        // Prepare base rules and prefixed guardian rules
        $rules = [];

        if (is_array(PERSON_VALIDATE)) {
            foreach (PERSON_VALIDATE as $k => $v) {
                $rules[$k] = $v;
            }
        }

        if (is_array(ADDRESS_VALIDATE)) {
            foreach (ADDRESS_VALIDATE as $k => $v) {
                $rules[$k] = $v;
            }
        }

        $rules = array_merge($rules, [
            'code'                     => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('students', 'code')->ignore($this->route('student'))->withoutTrashed(),
            ],
            'major_id'                 => 'required|exists:majors,id|integer',
            'batch_id'                 => 'required|exists:batches,id|integer',
            'admission_at'             => 'required|date',
            'status'                   => 'nullable|string|max:50',
            'bacc_2_code'              => 'nullable|string|max:50',
            'entrance_exam'            => 'nullable|string|max:50',
            'exit_exam'                => 'nullable|string|max:50',

            'guardians'                => 'required|array|min:1',
            'guardians.*.relationship' => 'required|string|max:50',
            'guardians.*.is_primary'   => 'required|boolean',
            'guardians.*.occupation'   => 'nullable|string|max:100',
            'guardians.*.remark'       => 'nullable|string|max:500',
            'guardians.*.phones'       => 'nullable|array',
            'guardians.*.addresses'    => 'nullable|array',
        ]);

        // Add guardian-prefixed person/address rules
        // if (is_array(PERSON_VALIDATE)) {
        //     foreach (PERSON_VALIDATE as $k => $v) {
        //         $rules['guardians.*.' . $k] = $v;
        //     }
        // }

        // if (is_array(ADDRESS_VALIDATE)) {
        //     foreach (ADDRESS_VALIDATE as $k => $v) {
        //         $rules['guardians.*.' . $k] = $v;
        //     }
        // }

        return $rules;
    }
}
