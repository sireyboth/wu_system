<?php
namespace App\Http\Requests;

class CourseEnrollmentRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            check_exist('student_academic_history_id', 'student_academic_histories'),
            check_exist('class_id', 'classes'),
            [
                'status' => 'nullable|in:enrolled,dropped',
                'remark' => 'nullable|string|max:500',
            ]
        );
    }
}
