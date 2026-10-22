<?php
namespace App\Http\Requests;

class RoomRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            DEFAULT_VALIDATE,
            check_exist('campus_id', 'campuses'),
            [
                'code'     => 'nullable|string|max:50',
                'capacity' => 'nullable|integer|min:1',
            ]
        );
    }
}
