<?php
namespace App\Http\Requests;

class StudentLeaveRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            check_exist('student_id', 'students'),
            [
                'starts_on'   => 'required|date',
                'ends_on'     => 'required|date|after_or_equal:starts_on',
                'status'      => 'nullable|in:pending,approved,rejected',
                'approved_by' => 'nullable|integer|exists:users,id',
                'remark'      => 'nullable|string|max:500',
            ]
        );
    }
}
