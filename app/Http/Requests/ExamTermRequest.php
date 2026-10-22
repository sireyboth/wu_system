<?php
namespace App\Http\Requests;

class ExamTermRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            check_exist('exam_category_id', 'exam_categories'),
            check_exist('campus_id', 'campuses'),
            [
                'title'          => 'required|string|max:150',
                'exam_date'      => 'nullable|date',
                'time_slots'     => 'nullable|array',
                'time_slots.*'   => 'required|string|max:100',
                'is_active'      => 'nullable|boolean',
                'remark'         => 'nullable|string|max:500',
            ]
        );
    }
}
