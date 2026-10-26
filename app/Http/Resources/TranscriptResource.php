<?php
namespace App\Http\Resources;

class TranscriptResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, [
            'status'                    => $this->status,
            'request_type'              => $this->request_type,
            'is_draft'                  => $this->isDraft(),
            'issue_no'                  => $this->issue_no,
            'name'                      => $this->name,
            'surname'                   => $this->surname,
            'name_kh'                   => $this->name_kh,
            'dob'                       => $this->dob?->format('Y-m-d'),
            'pob'                       => $this->pob,
            'pob_kh'                    => $this->pob_kh,
            'address'                   => $this->address,
            'address_kh'                => $this->address_kh,
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
            'current_year'              => $this->current_year,
            'current_semester'          => $this->current_semester,
            'study_status'              => $this->study_status,
            'exam_date'                 => $this->exam_date?->format('Y-m-d'),
            'comprehensive_exam'        => $this->comprehensive_exam,
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
            'program_years'             => $this->program_years,
            'can_print'                 => $this->canPrint(),
            'print_blockers'            => $this->printBlockers(),
            // Every layout can be picked at print time; each is checked against its own rules.
            'print_blockers_by_type'    => $this->printBlockersByType(),
            // Print page without ?type; the picker's Preview adds type + preview=1 (not logged).
            'preview_url'               => route('transcript.print', $this->id),
            // Times printed per layout + the latest print, for the reprint warning.
            'print_counts'              => $this->whenLoaded('prints', fn () => $this->prints->countBy('doc_type')),
            'last_print'                => $this->whenLoaded('prints', fn () => ($last = $this->prints->first()) ? [
                'doc_type'   => $last->doc_type,
                'printed_by' => $last->printer?->name,
                'printed_at' => $last->created_at?->format('Y-m-d H:i'),
            ] : null),
            'payments'                  => $this->whenLoaded('payments', fn () => $this->payments->map(fn ($p) => [
                'year'        => $p->year,
                'semester'    => $p->semester,
                'verified_by' => $p->verifier?->name,
                'verified_at' => $p->verified_at?->format('Y-m-d H:i'),
            ])),
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
