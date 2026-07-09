<?php
namespace App\Helpers;

use App\Models\Guardian;
use App\Models\Person;
use App\Models\Student;
use Illuminate\Support\Arr;

trait Generic
{
    protected function sync_guardians(Student $student, array $guardians): void
    {
        $pivot = [];
        foreach ($guardians as $guardian_data) {
            $job = ['occupation' => $guardian_data['occupation'] ?? null];

            if (! empty($guardian_data['id'])) {
                $guardian = Guardian::findOrFail($guardian_data['id']);
                $guardian->person->update($guardian_data);
                $guardian->update($job);
            } else {
                $person   = Person::create($guardian_data);
                $guardian = $person->guardian()->create($job);
            }

            // $this->sync_addresses($guardian->person, $guardian_data['addresses'] ?? []);
            $pivot[$guardian->id] = Arr::only($guardian_data, ['relationship', 'is_primary']);
        }

        $student->guardians()->sync($pivot);
    }

    protected function sync_addresses(Person $person, array $addresses): void
    {
        $types = [];
        foreach ($addresses as $address) {
            $types[] = $address['type'];
            $person->addresses()->updateOrCreate(
                [
                    'person_id' => $person->id,
                    'type'      => $address['type'],
                ],
                $address
            );
        }

        // Remove address types that were omitted from the request
        $person->addresses()->whereNotIn('type', $types)->delete();
    }
}
