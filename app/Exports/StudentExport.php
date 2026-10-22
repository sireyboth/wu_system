<?php
namespace App\Exports;

use App\Models\Student;

/**
 * Columns 1-27 (No..Remark) are exactly what StudentImport expects — export
 * the current list, edit/append rows, re-import round-trips cleanly (see
 * that class's docblock). Everything after Remark is export-only — every
 * other field captured on the create/edit student form (entrance/exit exam,
 * guardian details, current + birth address) but not part of the flat
 * import round-trip. StudentImport reads columns by heading key and simply
 * ignores ones it doesn't recognize, so appending here is safe.
 */
class StudentExport extends IExport
{
    protected string $model = Student::class;

    protected array $relationships = [
        'person.nationality', 'batch', 'major', 'group', 'shift', 'campus', 'status',
        'guardians', 'person.addresses.province', 'person.addresses.district',
        'person.addresses.commune', 'person.addresses.village',
    ];

    protected array $headings = [
        'No', 'Code', 'First Name', 'Last Name', 'First Name Kh', 'Last Name Kh',
        'Sex', 'Dob', 'Nationality', 'Email', 'Phone',
        'Batch', 'Major', 'Group', 'Shift', 'Campus', 'Status',
        'Year Level', 'Semester', 'Payment As', 'Admission Date', 'From School',
        'Degree Type', 'Intake', 'Scholarship', 'Bacc 2 Code', 'Remark',
        'Entrance Exam', 'Exit Exam',
        'Guardian Name', 'Guardian Relationship', 'Guardian Job', 'Guardian Phone', 'Guardian Address',
        'Current Address', 'Birth Address',
    ];

    public function __construct(protected array $filters = [])
    {
    }

    public function query()
    {
        $query = Student::query()->with($this->relationships);

        if ($search = $this->filters['search'] ?? null) {
            $query->search($search);
        }
        if ($payment = $this->filters['payment'] ?? null) {
            if ($payment === Student::YEARLY) {
                $query->yearly();
            } elseif ($payment === Student::SEMESTER) {
                $query->semester();
            }
        }

        return $query;
    }

    public function map(mixed $data): array
    {
        $this->numRow++;

        $person = $data->person;
        $phones = is_array($person?->phones) ? implode(', ', $person->phones) : null;
        $degreeType = $data->degree_type;

        $guardian = $data->guardians->first();
        $guardianPhones = $data->guardians
            ->flatMap(fn($g) => is_array($g->phones) ? $g->phones : [])
            ->filter()
            ->unique()
            ->implode(', ');
        $guardianAddress = is_array($guardian?->addresses) ? implode(', ', array_filter($guardian->addresses)) : null;

        $formatAddress = fn($address) => $address ? implode(', ', array_filter([
            $address->house_no,
            $address->street,
            $address->village?->name_en,
            $address->commune?->name_en,
            $address->district?->name_en,
            $address->province?->name_en,
        ])) : null;
        $currentAddress = $formatAddress($person?->addresses?->firstWhere('type', 'current'));
        $birthAddress   = $formatAddress($person?->addresses?->firstWhere('type', 'birth'));

        return [
            $this->numRow,
            $data->code,
            $person?->first_name,
            $person?->last_name,
            $person?->first_name_kh,
            $person?->last_name_kh,
            $person?->sex,
            $person?->dob?->format('Y-m-d'),
            $person?->nationality?->name_en,
            $person?->email,
            $phones,
            $data->batch?->shortcut,
            $data->major?->name_en,
            $data->group?->name_en,
            $data->shift?->name_en,
            $data->campus?->name_en,
            $data->status?->name_en,
            $data->year_level,
            $data->semester,
            $data->payment_as,
            $data->admission_date?->format('Y-m-d'),
            $data->from_school,
            $degreeType?->value ?? $degreeType,
            $data->intake,
            $data->scholarship,
            $data->bacc_2_code,
            $data->remark,
            $data->entrance_exam,
            $data->exit_exam,
            $guardian?->name_en ?: $guardian?->name_kh,
            $guardian?->relationship,
            $guardian?->job,
            $guardianPhones ?: null,
            $guardianAddress,
            $currentAddress,
            $birthAddress,
        ];
    }
}
