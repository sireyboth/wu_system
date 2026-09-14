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
            check_exist('campus_id', 'campuses', required: false),
            check_exist('shift_id', 'shifts', required: false),
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
                'capacity' => 'nullable|integer|min:1',
                'remark'   => 'nullable|string|max:500',
            ]
        );
    }
}
