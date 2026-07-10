<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Person;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Student';
        $this->model         = Student::class;
        $this->resource      = StudentResource::class;
        $this->relationships = [
            'person',
            'batch',
            'major',
            'shift',
            'major.faculty',

            'person.nationality',
            'person.addresses',
            'person.addresses.province',
            'person.addresses.district',
            'person.addresses.commune',
            'person.addresses.village',

            // 'guardians.person.addresses',
            // 'guardians.person.addresses.province',
            // 'guardians.person.addresses.district',
            // 'guardians.person.addresses.commune',
            // 'guardians.person.addresses.village',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->list($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentRequest $request)
    {
        return execute(function () use ($request) {
            $data    = $request->validated();
            $person  = Person::create($data);
            $student = $person->student()->create($data);

            foreach ($data['addresses'] as $address) {
                $person->addresses()->create($address);
            }

            foreach ($data['guardians'] as $guardian) {
                $guardian_person = Person::create($guardian);
                $response        = $guardian_person->guardian()->create(
                    Arr::only($guardian, ['occupation', 'phones', 'addresses'])
                );

                // foreach ($guardian['addresses'] as $address) {
                //     $guardian_person->addresses()->create($address);
                // }

                $student->guardians()->attach($response->id, Arr::only($guardian, ['relationship', 'is_primary']));
            }

            return new StudentResource($student->load($this->relationships));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return new StudentResource($student->load($this->relationships));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StudentRequest $request, Student $student)
    {
        return execute(function () use ($request, $student) {
            $data   = $request->validated();
            $person = $student->person;

            $person->update($data);
            $student->update($data);

            $this->sync_addresses($person, $data['addresses'] ?? []);
            $this->sync_guardians($student, $data['guardians'] ?? []);

            return new StudentResource($student->load($this->relationships));
        });
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        return execute(function () use ($student) {
            $student->person()->withTrashed()->first()?->addresses()->forceDelete();

            foreach ($student->guardians()->withTrashed()->get() as $guardian) {
                // $guardian->person()->withTrashed()->first()?->addresses()->forceDelete();
                $guardian->forceDelete();
                $guardian->person()->withTrashed()->forceDelete();
            }

            $student->forceDelete();
            $student->person()->withTrashed()->forceDelete();

            return has_data(null, 'Permanently deleted.');
        });
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Student $student)
    {
        return execute(function () use ($student) {
            $student->restore();
            $student->person()->withTrashed()->first()?->restore();
            $student->person()->withTrashed()->first()?->addresses()
                ->withTrashed()->first()?->restore();

            foreach ($student->guardians()->withTrashed()->get() as $guardian) {
                $guardian->restore();
                $guardian->person()->withTrashed()->first()?->restore();
                // $guardian->person()->withTrashed()->first()?->addresses()
                //     ->withTrashed()->first()?->restore();
            }

            return new StudentResource($student->load($this->relationships));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function trash(Student $student)
    {
        return execute(function () use ($student) {
            $student->person->addresses()->delete();
            foreach ($student->guardians as $guardian) {
                // $guardian->person->addresses()->delete();
                $guardian->delete();
                $guardian->person()->delete();
            }

            $student->delete();
            $student->guardians()->detach();
            $student->person()->delete();

            return has_data(null, 'Moved to trash.');
        });
    }
}
