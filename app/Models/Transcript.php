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

    public const DOC_TYPES = [
        self::TRANSCRIPT  => 'Transcript',
        self::PROVISIONAL => 'Provisional Certificate',
    ];

    protected $fillable = [
        'status',
        'issue_no', 'name', 'surname', 'name_kh', 'dob', 'pob', 'pob_kh', 'address', 'student_code', 'sex',
        'nationality', 'degree', 'major_id', 'academic_year', 'exam_date', 'date_kh', 'issue_date',
        'person_in_charge', 'person_role', 'person_in_charge_kh', 'person_role_kh',
        'entrance_exam', 'entrance_exam_description', 'remark',
    ];

    protected array $searchable = ['issue_no', 'name', 'surname', 'name_kh', 'student_code', 'academic_year'];

    protected $casts = [
        'dob'           => 'date',
        'issue_date'    => 'date',
        'exam_date'     => 'date',
        'entrance_exam' => 'boolean',
        'total_gpa'     => 'decimal:2',
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

    /**
     * Semesters whose scores aren't complete: no course entered yet, or a
     * course still IP (no score) or failed (F). Every semester of the
     * degree has to be filled — one passing course isn't enough.
     */
    public function incompleteScoreSemesters(): array
    {
        $rows = $this->subjects->groupBy(fn ($s) => "{$s->year}-{$s->semester}");

        $missing = [];
        $failed  = [];
        foreach ($this->programSemesters() as $block => $label) {
            $courses = $rows->get($block);
            if (! $courses) {
                $missing[] = $label;
            } elseif ($courses->contains(fn ($s) => $s->isInProgress() || $s->grade === 'F')) {
                $failed[] = $label;
            }
        }

        return [$missing, $failed];
    }

    /** "Y1 S2"-style labels of the semesters not yet checked as paid. */
    public function unpaidSemesters(): array
    {
        $paid = $this->payments->map(fn ($p) => "{$p->year}-{$p->semester}")->all();

        return array_values(array_diff_key($this->programSemesters(), array_flip($paid)));
    }

    /** Why this record can't be printed yet — empty when it can. Needs subjects + payments loaded. */
    public function printBlockers(): array
    {
        if ($this->isDraft()) {
            return ['Draft — finish it and Save before printing'];
        }

        $blockers = [];
        if (! $this->entrance_exam) {
            $blockers[] = 'Entrance exam not passed';
        }

        [$missing, $failed] = $this->incompleteScoreSemesters();
        if ($missing) {
            $blockers[] = 'No courses: ' . implode(', ', $missing);
        }
        if ($failed) {
            $blockers[] = 'Failed (F) or no score (IP): ' . implode(', ', $failed);
        }
        if ($unpaid = $this->unpaidSemesters()) {
            $blockers[] = 'Unpaid: ' . implode(', ', $unpaid);
        }

        return $blockers;
    }

    /** Printing needs a final record, the entrance exam passed, every semester scored and passed, and every semester paid. */
    public function canPrint(): bool
    {
        return $this->printBlockers() === [];
    }
}
