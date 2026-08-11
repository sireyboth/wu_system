<?php
namespace App\Http\Requests;

use Illuminate\Validation\Validator;

class ExamStateRequest extends IRequest
{
    protected function formData(): array
    {
        return [
            'no'             => 'nullable|integer',
            'room'           => 'required|string|max:50',
            'shift'          => 'nullable|string|max:50',
            'major'          => 'required|string|max:100',
            'student_total'  => 'nullable|integer',
            'degree'         => 'required|string|max:50',

            'majors'         => 'nullable|array|min:0',
            "majors.*"       => "nullable",

            'absences'       => 'nullable|array|min:0',
            'absences.*'     => 'nullable',

            'invigilators'   => 'nullable|array|min:0',
            'invigilators.*' => 'nullable',

            'exam_date'      => 'nullable|date',
            'remark'         => 'nullable|string',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $studentTotal = (int) $this->input('student_total', 0);

            foreach ((array) $this->input('absences', []) as $index => $absence) {
                $absentTotal = (int) ($absence['total'] ?? 0);

                if ($absentTotal > $studentTotal) {
                    $validator->errors()->add(
                        "absences.$index.total",
                        'ចំនួនអវត្តមានមិនអាចលើសពីចំនួននិស្សិតសរុបបានទេ។ (Absent count cannot exceed the student total.)'
                    );
                }
            }
        });
    }
}
