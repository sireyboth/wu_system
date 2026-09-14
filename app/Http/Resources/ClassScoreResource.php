<?php
namespace App\Http\Resources;

class ClassScoreResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'component'          => $this->component,
            'points'             => (float) $this->points,
            'course_enrollment'  => new CourseEnrollmentResource($this->whenLoaded('courseEnrollment')),
            'recorded_by'        => $this->recorder?->name,
        ], false);
    }
}
