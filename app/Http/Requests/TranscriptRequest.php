<?php
namespace App\Http\Requests;

use App\Helpers\Degree;
use App\Models\Transcript;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TranscriptRequest extends IRequest
{
    /** Course rows allowed per year + semester block on the printed page. */
    public const MAX_PER_SEMESTER = 5;
    public const MAX_YEAR         = 4;

    protected function formData(): array
    {
        // A draft saves whatever is filled in so far; the final Save requires it all.
        $required = $this->boolean('draft') ? 'nullable' : 'required';

        return array_merge(
            check_unique('transcripts', 'issue_no', ! $this->boolean('draft'), 100),
            [
                'draft'                     => 'sometimes|boolean',
                // What the student asked for — sets which print rules apply (Transcript::printBlockers).
                'request_type'              => ['required', Rule::in(array_keys(Transcript::REQUEST_TYPES))],
                'name'                      => "{$required}|string|max:100",
                'surname'                   => "{$required}|string|max:100",
                // Any record can be printed as the provisional certificate, which prints the Khmer name.
                'name_kh'                   => "{$required}|string|max:150",
                'dob'                       => 'nullable|date',
                'pob'                       => 'nullable|string|max:150',
                'pob_kh'                    => 'nullable|string|max:150',
                'address'                   => 'nullable|string|max:255',
                'address_kh'                => 'nullable|string|max:255',
                'student_code'              => 'nullable|string|max:50',
                'sex'                       => 'nullable|in:Male,Female',
                'nationality'               => 'nullable|string|max:100',
                'degree'                    => ['required', Rule::enum(Degree::class)],
                'major_id'                  => 'nullable|integer|exists:majors,id',
                'academic_year'             => 'nullable|string|max:50',
                'current_year'              => 'nullable|string|max:20',
                'current_semester'          => 'nullable|string|max:20',
                'study_status'              => 'nullable|in:studying,graduated',
                'exam_date'                 => 'nullable|date',
                'comprehensive_exam'        => 'sometimes|boolean',
                'date_kh'                   => 'nullable|string|max:255',
                'issue_date'                => 'nullable|date',
                'person_in_charge'          => 'nullable|string|max:100',
                'person_role'               => 'nullable|string|max:100',
                'person_in_charge_kh'       => 'nullable|string|max:100',
                'person_role_kh'            => 'nullable|string|max:100',
                'entrance_exam'             => 'required|boolean',
                'entrance_exam_description' => 'nullable|string|max:2000',
                'remark'                    => 'nullable|string|max:2000',

                // Course rows. Totals and grades are derived server-side
                // (see TranscriptController::syncSubjects), never accepted.
                'subjects'              => 'sometimes|array',
                'subjects.*.subject_id' => "{$required}|integer|exists:subjects,id",
                'subjects.*.year'       => 'required|integer|between:1,' . self::MAX_YEAR,
                'subjects.*.semester'   => 'required|integer|between:1,2',
                'subjects.*.credit'     => "{$required}|integer|between:0,30",
                'subjects.*.score'      => 'nullable|numeric|between:0,100',

                // Semesters checked as paid; the rest stay unpaid and block printing.
                'payments'              => 'sometimes|array',
                'payments.*.year'       => 'required|integer|between:1,' . self::MAX_YEAR,
                'payments.*.semester'   => 'required|integer|between:1,2',
            ]
        );
    }

    /**
     * Each year/semester block holds at most MAX_PER_SEMESTER courses, and
     * neither courses nor payments may go past the degree's length.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $years = Degree::tryFrom((string) $this->input('degree'))?->years() ?? self::MAX_YEAR;
                foreach (['subjects' => 'Courses', 'payments' => 'Payments'] as $key => $label) {
                    if (collect($this->input($key, []))->contains(fn ($row) => (int) ($row['year'] ?? 0) > $years)) {
                        $validator->errors()->add($key, "{$label} go past year {$years} — this degree only has {$years} years.");
                    }
                }
            },
            function (Validator $validator) {
                $counts = collect($this->input('subjects', []))
                    ->countBy(fn ($row) => ($row['year'] ?? '?') . '-' . ($row['semester'] ?? '?'));

                foreach ($counts as $block => $count) {
                    if ($count > self::MAX_PER_SEMESTER) {
                        [$year, $semester] = explode('-', $block);
                        $validator->errors()->add(
                            'subjects',
                            "Year {$year} semester {$semester} has {$count} courses — the limit is " . self::MAX_PER_SEMESTER . '.'
                        );
                    }
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'issue_no'              => 'N° Issue',
            'name_kh'               => 'name (Khmer)',
            'pob_kh'                => 'place of birth (Khmer)',
            'exam_date'             => 'exam date',
            'student_code'          => 'ID',
            'dob'                   => 'date of birth',
            'pob'                   => 'place of birth',
            'address_kh'            => 'address (Khmer)',
            'current_year'          => 'current year',
            'current_semester'      => 'current semester',
            'major_id'              => 'major',
            'subjects.*.subject_id' => 'subject',
            'subjects.*.credit'     => 'credit',
            'subjects.*.score'      => 'score',
        ];
    }
}
