<?php
namespace App\Http\Requests;

class StudentRequest extends IRequest
{
    protected function formData(): array
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

        $rules = array_merge($rules,
            check_unique(key: 'code', table_name: 'students'),
            [
                'admission_date'           => 'nullable|date',
                'from_school'              => 'nullable|string|max:100',
                'bacc_2_code'              => 'nullable|string|max:50',
                'entrance_exam'            => 'nullable|string',
                'year_level'               => 'nullable|integer',
                'exit_exam'                => 'nullable|string',
                'degree_type'              => 'nullable|string',
                'intake'                   => 'nullable|string',
                'scholarship'              => 'nullable|string',

                'guardians'                => 'sometimes|array|min:1',
                'guardians.*.relationship' => 'required|string|max:50',
                'guardians.*.job'          => 'nullable|string|max:100',
                'guardians.*.remark'       => 'nullable|string|max:500',
                'guardians.*.phones'       => 'nullable',
                'guardians.*.addresses'    => 'nullable|array',
            ]);

        if (is_array(DEFAULT_VALIDATE)) {
            foreach (DEFAULT_VALIDATE as $k => $v) {
                $rules["guardians.*.{$k}"] = $v;
            }
        }

        return $rules;
    }
}
