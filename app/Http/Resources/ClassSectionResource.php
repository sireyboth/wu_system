<?php
namespace App\Http\Resources;

class ClassSectionResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'code'         => $this->code,
            'capacity'     => $this->capacity,
            'subject'      => new SubjectResource($this->whenLoaded('subject')),
            'term'         => new TermResource($this->whenLoaded('term')),
            'campus'       => new CampusResource($this->whenLoaded('campus')),
            'shift'        => new ShiftResource($this->whenLoaded('shift')),
            'score_config' => new ClassScoreConfigResource($this->whenLoaded('scoreConfig')),
            'enrolled_count' => $this->whenCounted('courseEnrollments'),
            'teacher_assignments' => TeacherAssignmentResource::collection($this->whenLoaded('teacherAssignments')),
        ], false);
    }
}
