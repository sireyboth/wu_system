<?php
namespace App\Exports;

use App\Models\Student;

class StudentsExport extends IExport
{
    public function __construct()
    {
        $this->model         = Student::class;
        $this->relationships = [
            'person',
            'batch',
            'major',
            'shift',
            'group',
            'status',
            'guardians',
            ...array_map(fn($r) => "person.{$r}", ['nationality', 'addresses']),
        ];

        $this->headings = [
            "#",
            'Code',
            'Name',
            'Name Khmer',
            'Nationality',
            'Nationality Code',
            "Gender",
            'Tel',

            'Batch',
            'Batch Shortcut',

            'Status',
            'Status Shortcut',

            'Group',
            'Group Shortcut',

            'Shift',
            'Shift Shortcut',

            'Major',
            'Major Shortcut',

            "Address",
            'Degree Type',
            'Admission Date',
            'From School',

            'Guardian',
        ];
    }

    public function map(mixed $data): array
    {
        $person      = $data->person;
        $nationality = $person->nationality;
        $addresses   = $person->addresses;
        $guardians   = $data->guardians;

        return [
            ++$this->numRow,
            $data->code,
            "{$person->first_name} {$person->last_name}",
            "{$person->first_name_kh} {$person->last_name_kh}",

            ...grouped($nationality, ['name', 'code']),

            $person->sex,
            imploded($person['phones']),

            ...grouped($data->batch, ['name', 'shortcut']),
            ...grouped($data->status, ['name', 'shortcut']),
            ...grouped($data->group, ['name', 'shortcut']),
            ...grouped($data->shift, ['name', 'shortcut']),
            ...grouped($data->major, ['name', 'shortcut']),

            imploded($addresses, 'full_address', ";"),
            $data->degree_type,
            $data->admission_date?->format('Y-m-d'),
            $data->from_school,
            imploded($guardians, 'full_guardian', ";"),
        ];
    }
}
