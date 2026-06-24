<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacultyRequest;
use App\Http\Resources\FacultyResource;
use App\Models\Faculty;
use function App\Helpers\handle;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function __construct()
    {
        $this->name = 'Faculty';
    }

    /**
     * Display a listing of faculties.
     */
    public function index(Request $request)
    {
        $per_page = $request->integer('per_page', 10);
        $result   = Faculty::query()
            ->when($request->filled('search'), fn($q) => $q->search($request->search))
            ->when($request->filled('with'), fn($q) => $q->with(explode(',', $request->with)))
            ->paginate($per_page);

        return FacultyResource::collection($result);
    }

    /**
     * Store a newly created faculty.
     */
    public function store(FacultyRequest $request)
    {
        return handle(function () use ($request) {
            $result = Faculty::create($request->validated());

            return new FacultyResource($result->fresh());
        }, "{$this->name} created successfully", 201);
    }

    /**
     * Display the specified faculty.
     */
    public function show(string $id)
    {
        $result = Faculty::find($id);
        if (! $result) {
            return $this->not_found($id);
        }

        return new FacultyResource($result);
    }

    /**
     * Update the specified faculty.
     */
    public function update(FacultyRequest $request, string $id)
    {
        $result = Faculty::find($id);
        if (! $result) {
            return $this->not_found($id, 'not matched');
        }

        return handle(function () use ($request, $result) {
            $result->update($request->validated());

            return new FacultyResource($result);
        }, "{$this->name} updated successfully.");
    }

    /**
     * Soft delete the specified faculty.
     */
    public function destroy(string $id)
    {
        return handle(function () use ($id) {
            $result = Faculty::withTrashed()->findOrFail($id);
            $result->delete();

            return null;
        }, "{$this->name} deleted successfully.");
    }

    /**
     * Restore a soft-deleted faculty.
     */
    public function restore(string $id)
    {
        return handle(function () use ($id) {
            $result = Faculty::withTrashed()->findOrFail($id);
            $result->restore();

            return new FacultyResource($result->fresh());
        }, "{$this->name} restored successfully.");
    }

    /**
     * Force delete a faculty permanently.
     */
    public function force_destroy(string $id)
    {
        return handle(function () use ($id) {
            $result = Faculty::withTrashed()->findOrFail($id);
            $result->forceDelete();

            return null;
        }, "{$this->name} permanently deleted.");
    }
}
