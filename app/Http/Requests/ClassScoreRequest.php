<?php
namespace App\Http\Requests;

use App\Models\ClassScoreConfig;
use App\Models\CourseEnrollment;
use Illuminate\Contracts\Validation\Validator;

class ClassScoreRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            check_exist('course_enrollment_id', 'course_enrollments'),
            [
                'component' => 'required|in:homework,quiz,assignment,midterm,final',
                'points'    => 'required|numeric|min:0',
                'remark'    => 'nullable|string|max:500',
            ]
        );
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $enrollment = CourseEnrollment::find($this->input('course_enrollment_id'));
            if (! $enrollment) {
                return;
            }

            $config = ClassScoreConfig::where('class_id', $enrollment->class_id)->first();
            $field  = "{$this->input('component')}_max";
            $max    = $config?->{$field} ?? 0;

            if ((float) $this->input('points', 0) > $max) {
                $validator->errors()->add('points', "This class's {$this->input('component')} is capped at {$max} points.");
            }
        });
    }
}
