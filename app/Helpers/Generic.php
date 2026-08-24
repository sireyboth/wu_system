<?php
namespace App\Helpers;

use App\Models\Person;
use App\Models\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;

trait Generic
{
    protected function sync_guardians(Student $student, array $guardians): void
    {
        $incoming_ids = collect($guardians)->pluck('id')->filter();

        $student->guardians()->whereNotIn('id', $incoming_ids)->delete();
        foreach ($guardians as $guardian) {
            $student->guardians()->updateOrCreate(
                ['id' => $guardian['id'] ?? null],
                Arr::except($guardian, ['id'])
            );
        }
    }

    protected function sync_addresses(Person $person, array $addresses): void
    {
        $incoming_types = collect($addresses)->pluck('type')->filter();

        // remove address types no longer present in the payload
        $person->addresses()->whereNotIn('type', $incoming_types)->delete();
        foreach ($addresses as $address) {
            $person->addresses()->updateOrCreate(
                ['type' => $address['type']],         // match key: one row per type
                Arr::except($address, ['id', 'type']) // don't overwrite the match key itself
            );
        }
    }

    protected function withStudent(string $key = 'student')
    {
        return $this->relations($key, array_merge(['person', 'guardians'], $this->withPerson()));
    }

    protected function withPerson(string $key = 'person', string $otherKey = 'addresses')
    {
        return $this->relations($key, array_merge(
            ['nationality', 'addresses'],
            $this->relations($otherKey, ['province', 'district', 'commune', 'village'])
        ));
    }

    protected function export(object $data, string $file_name = 'student')
    {
        $time = now()->format('Y_m_d_His');
        return Excel::download($data, "{$file_name}_{$time}.xlsx");
    }

    protected function import(object $data, UploadedFile $file): array
    {
        Excel::import($data, $file);
        return [
            'imported' => true,
            'failures' => $data->failures(), // rows that failed validation
        ];
    }

    protected function relations(string $key, array $values): array
    {
        return array_map(fn($cnx) => "{$key}.{$cnx}", $values);
    }
}
