<?php
namespace App\Http\Requests;

class ClassScheduleRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            check_exist('class_id', 'classes'),
            check_exist('room_id', 'rooms', required: false),
            [
                'day_of_week' => 'required|integer|min:0|max:6',
                'starts_at'   => 'required|date_format:H:i',
                'ends_at'     => 'required|date_format:H:i|after:starts_at',
                'remark'      => 'nullable|string|max:500',
            ]
        );
    }
}
