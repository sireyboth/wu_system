<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LecturerRequest;
use App\Http\Resources\LecturerResource;
use App\Models\Lecturer;
use App\Models\Person;
use Illuminate\Http\Request;

class LecturerController extends Controller
{

    public function __construct()
    {
        $this->name          = 'Lecturer';
        $this->model         = Lecturer::class;
        $this->resource      = LecturerResource::class;
        $this->relationships = ['person', 'major', 'major.faculty', 'person.addresses'];
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
    public function store(LecturerRequest $request)
    {
        return execute(function () use ($request) {
            $data   = $request->validated();
            $person = Person::create($data);

            $lecturer = $person->lecturer()->create($data);

            foreach ($data['addresses'] as $address) {
                $person->addresses()->create($address);
            }

            return new LecturerResource($lecturer->load($this->relationships));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Lecturer $lecturer)
    {
        return new LecturerResource($lecturer->load($this->relationships));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LecturerRequest $request, Lecturer $lecturer)
    {
        return execute(function () use ($request, $lecturer) {
            $data = $request->validated();
            $lecturer->update($data);
            $lecturer->person->update($data);

            foreach ($data['addresses'] as $address) {
                $lecturer->person->addresses()->updateOrCreate([], $address);
            }

            return new LecturerResource($lecturer->load($this->relationships));
        });
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Lecturer $lecturer)
    {
        return execute(function () use ($lecturer) {
            $person = $lecturer->person;

            $person?->addresses()->forceDelete();
            $person?->forceDelete();
            $lecturer->forceDelete();

            return has_data(null, 'Permanently deleted.');
        });
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Lecturer $lecturer)
    {
        return execute(function () use ($lecturer) {
            $lecturer->restore();
            $lecturer->person()->withTrashed()->first()?->restore();
            $lecturer->person()->withTrashed()->first()?->addresses()
                ->withTrashed()->first()?->restore();

            return new LecturerResource($lecturer->load($this->relationships));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function trash(Lecturer $lecturer)
    {
        return execute(function () use ($lecturer) {
            $person    = $lecturer->person;
            $addresses = $person?->addresses();
            $addresses?->delete();
            $person?->delete();
            $lecturer->delete();

            return has_data(null, 'Moved to trash.');
        });
    }
}
