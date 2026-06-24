<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MajorRequest;
use App\Http\Resources\MajorResource;
use App\Models\Major;
use function App\Helpers\handle;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function __construct()
    {
        $this->name = 'Major';
    }

    /**
     * Display a listing of majors.
     */
    public function index(Request $request)
    {
        $per_page = $request->integer('per_page', 10);
        $result   = Major::query()
            ->when($request->filled('search'), fn($q) => $q->search($request->search))
            ->when($request->filled('with'), fn($q) => $q->with(explode(',', $request->with)))
            ->paginate($per_page);

        return MajorResource::collection($result);
    }

    /**
     * Store a newly created major.
     */
    public function store(MajorRequest $request)
    {
        return handle(function () use ($request) {
            $result = Major::create($request->validated());

            return new MajorResource($result->fresh());
        }, "{$this->name} created successfully", 201);
    }

    /**
     * Display the specified major.
     */
    public function show(string $id)
    {
        $result = Major::find($id);
        if (! $result) {
            return $this->not_found($id);
        }

        return new MajorResource($result);
    }

    /**
     * Update the specified major.
     */
    public function update(MajorRequest $request, string $id)
    {
        $result = Major::find($id);
        if (! $result) {
            return $this->not_found($id, 'not matched');
        }

        return handle(function () use ($request, $result) {
            $result->update($request->validated());

            return new MajorResource($result);
        }, "{$this->name} updated successfully.");
    }

    /**
     * Soft delete the specified major.
     */
    public function destroy(string $id)
    {
        return handle(function () use ($id) {
            $result = Major::withTrashed()->findOrFail($id);
            $result->delete();

            return null;
        }, "{$this->name} deleted successfully.");
    }

    /**
     * Restore a soft-deleted major.
     */
    public function restore(string $id)
    {
        return handle(function () use ($id) {
            $result = Major::withTrashed()->findOrFail($id);
            $result->restore();

            return new MajorResource($result->fresh());
        }, "{$this->name} restored successfully.");
    }

    /**
     * Force delete a major permanently.
     */
    public function force_destroy(string $id)
    {
        return handle(function () use ($id) {
            $result = Major::withTrashed()->findOrFail($id);
            $result->forceDelete();

            return null;
        }, "{$this->name} permanently deleted.");
    }
}
