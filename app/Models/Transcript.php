<?php
namespace App\Models;

use App\Helpers\Degree;

class Transcript extends IModel
{
    public const DRAFT = 'draft';
    public const FINAL = 'final';

    protected $fillable = [
        'status',
        'issue_no', 'name', 'surname', 'dob', 'pob', 'address', 'student_code', 'sex',
        'nationality', 'degree', 'major_id', 'academic_year', 'date_kh', 'issue_date',
        'person_in_charge', 'person_role', 'entrance_exam', 'entrance_exam_description', 'remark',
    ];

    protected array $searchable = ['issue_no', 'name', 'surname', 'student_code', 'academic_year'];

    protected $casts = [
        'dob'           => 'date',
        'issue_date'    => 'date',
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
