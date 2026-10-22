<?php
namespace App\Models;

use App\Helpers\Degree;

class Transcript extends IModel
{
    public const DRAFT = 'draft';
    public const FINAL = 'final';

    /** Request Docs — which printout the record produces. */
    public const TRANSCRIPT  = 'transcript';
    public const PROVISIONAL = 'provisional';

    public const DOC_TYPES = [
        self::TRANSCRIPT  => 'Transcript',
        self::PROVISIONAL => 'Provisional Certificate',
    ];

    protected $fillable = [
        'status', 'doc_type',
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

    public function getDocTypeLabelAttribute(): string
    {
        return self::DOC_TYPES[$this->doc_type] ?? ucfirst((string) $this->doc_type);
    }

    public function isProvisional(): bool
    {
        return $this->doc_type === self::PROVISIONAL;
    }

    public function isDraft(): bool
    {
        return $this->status === self::DRAFT;
    }

    /** Printing stays blocked for drafts and until the entrance exam is marked as passed. */
    public function canPrint(): bool
    {
        return ! $this->isDraft() && (bool) $this->entrance_exam;
    }
}
