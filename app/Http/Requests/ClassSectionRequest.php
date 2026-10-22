<?php
namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ClassSectionRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            check_exist('subject_id', 'subjects'),
            check_exist('term_id', 'terms'),
            check_exist('campus_id', 'campuses'),
            check_exist('shift_id', 'shifts'),
            check_exist('batch_id', 'batches'),
            check_exist('lecturer_id', 'lecturers', required: false),
            [
                // Unique per term, not globally — the same code (e.g.
                // "IT101-A") can be reused once its term is over.
                'code' => [
                    'required', 'string', 'max:50',
                    Rule::unique('classes', 'code')
                        ->where('term_id', $this->input('term_id'))
                        ->ignore($this->route('class'))
                        ->withoutTrashed(),
                ],
                'capacity'    => 'nullable|integer|min:1',
                'room_number' => 'nullable|string|max:50',
                'time_slot'   => 'nullable|string|in:8:00-11:10,2:00-5:00,5:30-8:30',
                'remark'      => 'nullable|string|max:500',
                // Multi-select — a class can be tagged with several majors
                // at once (see class_major pivot); label only, doesn't
                // restrict who can actually enroll.
                'majors'      => 'nullable|array',
                'majors.*'    => 'integer|exists:majors,id',
            ]
        );
    }
}
