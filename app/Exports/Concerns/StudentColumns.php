<?php
namespace App\Exports\Concerns;

use App\Models\Student;

/**
 * The full student block shared by the payment exports — every
 * students/people column, with FK ids resolved to readable names.
 * Callers must eager-load studentRelationships() to avoid N+1 queries.
 */
trait StudentColumns
{
    protected function studentRelationships(string $prefix = 'student'): array
    {
        return array_map(
            fn($r) => "{$prefix}.{$r}",
            ['person', 'batch', 'major', 'group', 'shift', 'status', 'campus']
        );
    }

    protected function studentHeadings(): array
    {
        return [
            'Student ID', 'Student Code', 'First Name', 'Last Name', 'Full Name',
            'First Name (KH)', 'Last Name (KH)', 'Sex', 'Date of Birth', 'Phones', 'Email',
            'Campus', 'Major', 'Batch', 'Group', 'Shift', 'Student Status', 'Degree',
            'Year Level', 'Semester', 'Intake', 'Admission Date', 'Is Restudy',
            'Payment As', 'Scholarship', 'From School', 'BacII Code',
            'Entrance Exam', 'Exit Exam', 'Student Remark',
        ];
    }

    protected function studentColumns(?Student $student): array
    {
        $person = $student?->person;
        $phones = $person?->phones;

        return [
            $student?->id,
            $student?->code,
            $person?->first_name,
            $person?->last_name,
            trim(($person?->first_name ?? '') . ' ' . ($person?->last_name ?? '')),
            $person?->first_name_kh,
            $person?->last_name_kh,
            $person?->sex,
            $this->dateTime($person?->dob, 'Y-m-d'),
            is_array($phones) ? implode(', ', array_filter($phones, 'is_scalar')) : $phones,
            $person?->email,
            $student?->campus?->name_en,
            $student?->major?->name_en,
            $student?->batch?->name_en,
            $student?->group?->name_en,
            $student?->shift?->name_en,
            $student?->status?->name_en,
            $student?->degree_type?->labelEn(),
            $student?->year_level,
            $student?->semester,
            $student?->intake,
            $this->dateTime($student?->admission_date, 'Y-m-d'),
            $this->yesNo($student?->is_restudy),
            $student?->payment_as,
            $student?->scholarship,
            $student?->from_school,
            $student?->bacc_2_code,
            $student?->entrance_exam,
            $student?->exit_exam,
            $student?->remark,
        ];
    }

    protected function yesNo(?bool $value): ?string
    {
        return $value === null ? null : ($value ? 'Yes' : 'No');
    }

    /** Accepts a Carbon (cast column) or a raw string (uncast column). */
    protected function dateTime(mixed $value, string $format = 'Y-m-d H:i:s'): ?string
    {
        if (blank($value)) {
            return null;
        }

        return $value instanceof \DateTimeInterface ? $value->format($format) : (string) $value;
    }
}
