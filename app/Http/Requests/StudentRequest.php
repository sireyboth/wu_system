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
            check_unique('code', 'students'),
            check_exist('batch_id', 'batches'),
            check_exist('major_id', 'majors'),
            check_exist('group_id', 'groups'),
            check_exist('shift_id', 'shifts'),
            check_exist('status_id', 'statuses'),
            [
                'admission_date'           => 'nullable|date',
                'from_school'              => 'nullable|string|max:100',
                'bacc_2_code'              => 'nullable|string|max:50',
                'entrance_exam'            => 'nullable|string',
                'exit_exam'                => 'nullable|string',
                'degree_type'              => 'nullable|string',
                'intake'                   => 'nullable|string',
                'payment_as'               => 'nullable|string',
                'year_level'               => 'nullable|integer',
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
                $rules['guardians.*.' . $k] = $v;
            }
        }

        return $rules;
    }
}
