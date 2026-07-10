<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
             ...check_unique('code', 'students'),
            ...check_exist('batch_id', 'batches'),
            ...check_exist('major_id', 'majors'),
            ...check_exist('group_id', 'groups'),
            ...check_exist('shift_id', 'shifts'),
            ...check_exist('status_id', 'statuses'),
            'admission_date'           => 'nullable|date',
            'from_school'              => 'nullable|string|max:100',
            'bacc_2_code'              => 'nullable|string|max:50',
            'entrance_exam'            => 'nullable|string',
            'exit_exam'                => 'nullable|string',
            'degree_type'              => 'nullable|string',
            'intake'                   => 'nullable|string',
            'scholarship'              => 'nullable|string',

            'guardians'                => 'sometimes|array|min:1',
            'guardians.*.relationship' => 'required|string|max:50',
            'guardians.*.is_primary'   => 'required|boolean',
            'guardians.*.occupation'   => 'nullable|string|max:100',
        ]);

        // Add guardian-prefixed person/address rules
        if (is_array(PERSON_VALIDATE)) {
            foreach (PERSON_VALIDATE as $k => $v) {
                $rules['guardians.*.' . $k] = $v;
            }
        }

        // if (is_array(ADDRESS_VALIDATE)) {
        //     foreach (ADDRESS_VALIDATE as $k => $v) {
        //         $rules['guardians.*.' . $k] = $v;
        //     }
        // }

        return $rules;
    }
}
