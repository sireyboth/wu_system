<?php
namespace App\Http\Resources;

class TranscriptResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, [
            'status'                    => $this->status,
            'is_draft'                  => $this->isDraft(),
            'doc_type'                  => $this->doc_type,
            'doc_type_label'            => $this->doc_type_label,
            'issue_no'                  => $this->issue_no,
            'name'                      => $this->name,
            'surname'                   => $this->surname,
            'name_kh'                   => $this->name_kh,
            'dob'                       => $this->dob?->format('Y-m-d'),
            'pob'                       => $this->pob,
            'pob_kh'                    => $this->pob_kh,
            'address'                   => $this->address,
            'student_code'              => $this->student_code,
            'sex'                       => $this->sex,
            'nationality'               => $this->nationality,
            'degree'                    => $this->degree,
            'degree_label'              => $this->degree_label,
            'major_id'                  => $this->major_id,
            'major'                     => $this->whenLoaded('major', fn () => $this->major ? [
                'id'      => $this->major->id,
                'name_en' => $this->major->name_en,
                'name_kh' => $this->major->name_kh,
            ] : null),
            'academic_year'             => $this->academic_year,
            'exam_date'                 => $this->exam_date?->format('Y-m-d'),
            'date_kh'                   => $this->date_kh,
            'issue_date'                => $this->issue_date?->format('Y-m-d'),
            'person_in_charge'          => $this->person_in_charge,
            'person_role'               => $this->person_role,
            'person_in_charge_kh'       => $this->person_in_charge_kh,
            'person_role_kh'            => $this->person_role_kh,
            'entrance_exam'             => $this->entrance_exam,
            'entrance_exam_description' => $this->entrance_exam_description,
            'total_credits'             => $this->total_credits,
            'total_gpa'                 => $this->total_gpa,
            'can_print'                 => $this->canPrint(),
            'print_url'                 => route('transcript.print', $this->id),
            'subjects'                  => $this->whenLoaded('subjects', fn () => $this->subjects->map(fn ($s) => [
                'id'          => $s->id,
                'subject_id'  => $s->subject_id,
                'year'        => $s->year,
                'semester'    => $s->semester,
                'code'        => $s->code,
                'title'       => $s->title,
                'credit'      => $s->credit,
                'score'       => $s->score,
                'grade_point' => $s->grade_point,
                'grade'       => $s->grade,
            ])),
        ], false);
    }
}
