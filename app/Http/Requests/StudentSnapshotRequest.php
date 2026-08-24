<?php
namespace App\Http\Requests;

class StudentSnapshotRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            check_exist('student_id', 'students'),
            check_exist('batch_id', 'batches'),
            check_exist('campus_id', 'campuses'),
            check_exist('major_id', 'majors'),
            check_exist('group_id', 'groups'),
            check_exist('shift_id', 'shifts'),
            check_exist('status_id', 'statuses'),
            [
                'effective_date' => 'nullable|date',
                'is_current'     => 'nullable|boolean',
                'remark'         => 'nullable|string|max:500',
            ]
        );
    }
}
