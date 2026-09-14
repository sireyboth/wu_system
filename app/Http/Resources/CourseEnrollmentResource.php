<?php
namespace App\Http\Resources;

class CourseEnrollmentResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'status'                  => $this->status,
            'class'                   => new ClassSectionResource($this->whenLoaded('classSection')),
            'student_academic_history' => new StudentAcademicHistoryResource($this->whenLoaded('studentAcademicHistory')),
            'student'                 => new StudentResource($this->studentAcademicHistory?->student),
            'scores'                  => ClassScoreResource::collection($this->whenLoaded('classScores')),
        ], false);
    }
}
