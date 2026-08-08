<?php
namespace App\Http\Requests;

class ExamStateRequest extends IRequest
{
    protected function formData(): array
    {
        return [
            'no'          => 'nullable|integer',
            'floor_order' => 'nullable|integer',
            'floor'       => 'nullable|string|max:50',
            'room'        => 'required|string|max:50',
            'shift'       => 'nullable|string|max:50',
            'major'       => 'required|string|max:50',
            'students'    => 'required|integer',
            'degree'      => 'required|string|max:50',
            'majors'      => 'nullable|array',
            'sort_order'  => 'nullable|integer',
            'exam_date'   => 'nullable|date',
            'remark'      => 'nullable|string',
        ];
    }
}
