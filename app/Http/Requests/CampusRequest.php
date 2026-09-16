<?php
namespace App\Http\Requests;

class CampusRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            DEFAULT_VALIDATE,
            check_unique('campuses', 'shortcut', true),
            [
                'latitude'                  => 'nullable|numeric|between:-90,90',
                'longitude'                 => 'nullable|numeric|between:-180,180',
                'attendance_radius_meters'  => 'nullable|integer|min:10|max:5000',
            ]
        );
    }
}
