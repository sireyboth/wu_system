<?php
namespace App\Helpers;

use App\Models\Person;
use App\Models\Student;
use Illuminate\Support\Arr;

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
}
