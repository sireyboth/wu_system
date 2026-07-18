<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Person;
use App\Models\Student;
use Illuminate\Http\Request;

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
            'group',
            'status',
            'guardians',
            ...array_map(fn($r) => "person.{$r}", [
                'nationality',
                'addresses',
                'addresses.province',
                'addresses.district',
                'addresses.commune',
                'addresses.village',
            ]),
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

            <<  << <<< HEAD
            foreach ($data['addresses'] as $address) {
                $person->addresses()->create($address);
            }

            foreach ($data['guardians'] as $guardian) {
                $guardian_person = Person::create($guardian);
                $response        = $guardian_person->guardian()->create([
                    'occupation' => $guardian['occupation'] ?? null,
                ]);

                // foreach ($guardian['addresses'] as $address) {
                //     $guardian_person->addresses()->create($address);
                // }

                $student->guardians()->attach($response->id, Arr::only($guardian, ['relationship', 'is_primary']));
            }
=======
            $person->addresses()->createMany($data['addresses']);
            $student->guardians()->createMany($data['guardians']);
>>>>>>> 81f415e (done student info)

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
            $student->person->addresses()->forceDelete(); // if addresses relation is via person, adjust accordingly
            $student->guardians()->forceDelete();
            $student->person->forceDelete();
            $student->forceDelete();

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

            return new StudentResource($student->load($this->relationships));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function trash(Student $student)
    {
        return execute(function () use ($student) {
            $student->delete();
            $student->person->delete();

            return has_data(null, 'Moved to trash.');
        });
    }
}
