<?php
namespace App\Http\Requests;

use App\Models\ClassScoreConfig;
use Illuminate\Contracts\Validation\Validator;

/**
 * Validates one lecturer's point split for their own class. The one hard
 * rule from the user's own words: "make sure they are not bigger than
 * 100" — checked as a sum across all six components, not per-field, since
 * each lecturer is free to weight their own class however they like as
 * long as the total never exceeds 100.
 */
class ClassScoreConfigRequest extends IRequest
{
    protected function formData(): array
    {
        return [
            'homework_max'   => 'required|integer|min:0|max:100',
            'quiz_max'       => 'required|integer|min:0|max:100',
            'assignment_max' => 'required|integer|min:0|max:100',
            'midterm_max'    => 'required|integer|min:0|max:100',
            'final_max'      => 'required|integer|min:0|max:100',
            'attendance_max' => 'required|integer|min:0|max:100',
            'total_weeks'       => 'nullable|integer|min:1|max:52',
            'sessions_per_week' => 'nullable|integer|min:1|max:14',
            'remark'         => 'nullable|string|max:500',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $total = collect(ClassScoreConfig::COMPONENT_FIELDS)
                ->sum(fn($field) => (int) $this->input($field, 0));

            if ($total > 100) {
                $validator->errors()->add('total', "The six components add up to {$total}, which is over the 100-point cap for this class.");
            }
        });
    }
}
