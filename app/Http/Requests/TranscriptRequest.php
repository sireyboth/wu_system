<?php
namespace App\Http\Requests;

use App\Helpers\Degree;
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
                'name'                      => "{$required}|string|max:100",
                'surname'                   => "{$required}|string|max:100",
                'dob'                       => 'nullable|date',
                'pob'                       => 'nullable|string|max:150',
                'address'                   => 'nullable|string|max:255',
                'student_code'              => 'nullable|string|max:50',
                'sex'                       => 'nullable|in:Male,Female',
                'nationality'               => 'nullable|string|max:100',
                'degree'                    => ['required', Rule::enum(Degree::class)],
                'major_id'                  => 'nullable|integer|exists:majors,id',
                'academic_year'             => 'nullable|string|max:50',
                'date_kh'                   => 'nullable|string|max:255',
                'issue_date'                => 'nullable|date',
                'person_in_charge'          => 'nullable|string|max:100',
                'person_role'               => 'nullable|string|max:100',
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
            ]
        );
    }

    /** Each year/semester block holds at most MAX_PER_SEMESTER courses. */
    public function after(): array
    {
        return [
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
            'student_code'          => 'ID',
            'dob'                   => 'date of birth',
            'pob'                   => 'place of birth',
            'major_id'              => 'major',
            'subjects.*.subject_id' => 'subject',
            'subjects.*.credit'     => 'credit',
            'subjects.*.score'      => 'score',
        ];
    }
}
