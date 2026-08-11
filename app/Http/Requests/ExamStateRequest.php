<?php
namespace App\Http\Requests;

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
}
