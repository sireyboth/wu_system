<?php
namespace App\Http\Requests;

class TeacherAssignmentRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            check_exist('lecturer_id', 'lecturers'),
            check_exist('class_id', 'classes'),
            [
                'role'          => 'required|in:primary,substitute',
                'assigned_from' => 'required|date',
                'assigned_to'   => 'nullable|date|after_or_equal:assigned_from',
                'remark'        => 'nullable|string|max:500',
            ]
        );
    }
}
