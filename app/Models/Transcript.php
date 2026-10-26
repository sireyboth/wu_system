<?php
namespace App\Models;

use App\Helpers\Degree;

class Transcript extends IModel
{
    public const DRAFT = 'draft';
    public const FINAL = 'final';

    /** Request Docs — the layouts a record can be printed as, picked at print time. */
    public const TRANSCRIPT  = 'transcript';
    public const PROVISIONAL = 'provisional';
    public const STATUS      = 'status';

    /** study_status — how the Student Status Certification words the student's standing. */
    public const STUDYING  = 'studying';
    public const GRADUATED = 'graduated';

    public const DOC_TYPES = [
        self::TRANSCRIPT  => 'Transcript',
        self::PROVISIONAL => 'Provisional Certificate',
        self::STATUS      => 'Student Status Certification',
    ];

    /** request_type — what the student asked for; picks the print rules. Status shares the transcript's. */
    public const REQUEST_TYPES = [
        self::TRANSCRIPT  => 'Transcript & Student Status',
        self::PROVISIONAL => 'Provisional Certificate',
    ];

    protected $fillable = [
        'status', 'request_type',
        'issue_no', 'name', 'surname', 'name_kh', 'dob', 'pob', 'pob_kh', 'address', 'address_kh', 'student_code', 'sex',
        'nationality', 'degree', 'major_id', 'academic_year', 'current_year', 'current_semester', 'study_status',
        'exam_date', 'comprehensive_exam', 'date_kh', 'issue_date',
        'person_in_charge', 'person_role', 'person_in_charge_kh', 'person_role_kh',
        'entrance_exam', 'entrance_exam_description', 'remark',
    ];

    protected array $searchable = ['issue_no', 'name', 'surname', 'name_kh', 'student_code', 'academic_year'];

    protected $casts = [
        'dob'           => 'date',
        'issue_date'    => 'date',
        'exam_date'     => 'date',
        'entrance_exam'      => 'boolean',
        'comprehensive_exam' => 'boolean',
        'total_gpa'          => 'decimal:2',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function subjects()
    {
        return $this->hasMany(TranscriptSubject::class)
            ->orderBy('year')->orderBy('semester')->orderBy('position');
    }

    public function payments()
    {
        return $this->hasMany(TranscriptPayment::class)->orderBy('year')->orderBy('semester');
    }

    public function prints()
    {
        return $this->hasMany(TranscriptPrint::class)->latest('id');
    }

    /** No name_en/name_kh on this table, so IModel's to_name() accessor doesn't apply. */
    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? null;
    }

    /** "Bachelor", "Master", … as printed on the transcript. */
    public function getDegreeLabelAttribute(): string
    {
        return Degree::tryFrom((string) $this->degree)?->name ?? ucfirst((string) $this->degree);
    }

    public function isDraft(): bool
    {
        return $this->status === self::DRAFT;
    }

    /** Years of study the degree covers — payment boxes and course years stop here. */
    public function getProgramYearsAttribute(): int
    {
        return Degree::tryFrom((string) $this->degree)?->years() ?? 4;
    }

    /** ["1-1" => "Y1 S1", …] — every semester the degree covers. */
    public function programSemesters(): array
    {
        $blocks = [];
        for ($year = 1; $year <= $this->program_years; $year++) {
            foreach ([1, 2] as $semester) {
                $blocks["{$year}-{$semester}"] = "Y{$year} S{$semester}";
            }
        }

        return $blocks;
    }

    /** ["1-1" => "Y1 S1", …] — only the semesters that have courses entered. */
    public function enteredSemesters(): array
    {
        $entered = $this->subjects->map(fn ($s) => "{$s->year}-{$s->semester}")->unique()->all();

        return array_intersect_key($this->programSemesters(), array_flip($entered));
    }

    /**
     * Semesters whose scores aren't complete: no course entered yet, or a
     * course still IP (no score) or failed (F). With $all every semester
     * of the degree has to be filled; otherwise only the entered ones count.
     */
    public function incompleteScoreSemesters(bool $all = true): array
    {
        $rows = $this->subjects->groupBy(fn ($s) => "{$s->year}-{$s->semester}");

        $missing = [];
        $failed  = [];
        foreach ($all ? $this->programSemesters() : $this->enteredSemesters() as $block => $label) {
            $courses = $rows->get($block);
            if (! $courses) {
                $missing[] = $label;
            } elseif ($courses->contains(fn ($s) => $s->isInProgress() || $s->grade === 'F')) {
                $failed[] = $label;
            }
        }

        return [$missing, $failed];
    }

    /** "Y1 S2"-style labels of the semesters not yet checked as paid — every semester, or only the entered ones. */
    public function unpaidSemesters(bool $all = true): array
    {
        $paid = $this->payments->map(fn ($p) => "{$p->year}-{$p->semester}")->all();

        return array_values(array_diff_key($all ? $this->programSemesters() : $this->enteredSemesters(), array_flip($paid)));
    }

    /**
     * Why this record can't be printed as $docType (default: the type the
     * student requested) — empty when it can. Needs subjects + payments loaded.
     *   Transcript / Status: entrance exam, and the semesters entered must be passed and paid.
     *   Provisional: entrance exam, every semester of the degree passed and paid,
     *   and the comprehensive exam dated and passed.
     */
    public function printBlockers(?string $docType = null): array
    {
        if ($this->isDraft()) {
            return ['Draft — finish it and Save before printing'];
        }

        $docType ??= $this->request_type ?: self::TRANSCRIPT;
        $all = $docType === self::PROVISIONAL;

        $blockers = [];
        if (! $this->entrance_exam) {
            $blockers[] = 'Entrance exam not passed';
        }

        [$missing, $failed] = $this->incompleteScoreSemesters($all);
        if ($missing) {
            $blockers[] = 'No courses: ' . implode(', ', $missing);
        }
        if ($docType === self::TRANSCRIPT && $this->subjects->isEmpty()) {
            $blockers[] = 'No courses entered';
        }
        if ($failed) {
            $blockers[] = 'Failed (F) or no score (IP): ' . implode(', ', $failed);
        }
        if ($unpaid = $this->unpaidSemesters($all)) {
            $blockers[] = 'Unpaid: ' . implode(', ', $unpaid);
        }

        if ($all) {
            if (! $this->exam_date) {
                $blockers[] = 'Comprehensive exam date missing';
            }
            if (! $this->comprehensive_exam) {
                $blockers[] = 'Comprehensive exam not passed';
            }
        }

        return $blockers;
    }

    /** [doc_type => blockers] for every printable layout. */
    public function printBlockersByType(): array
    {
        return collect(array_keys(self::DOC_TYPES))
            ->mapWithKeys(fn ($type) => [$type => $this->printBlockers($type)])
            ->all();
    }

    public function canPrint(?string $docType = null): bool
    {
        return $this->printBlockers($docType) === [];
    }
}
