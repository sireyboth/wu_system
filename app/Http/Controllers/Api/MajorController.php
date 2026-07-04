<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MajorRequest;
use App\Http\Resources\MajorResource;
use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
   public function __construct()
    {
        $this->name     = 'Major';
        $this->model    = Major::class;
        $this->resource = MajorResource::class;
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
    public function store(MajorRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Major $major)
    {
        return $this->view($major);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MajorRequest $request, Major $major)
    {
        return $this->release($request, $major);
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Major $major)
    {
        return $this->disable($major);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Major $major)
    {
        return $this->enable($major);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function force_destroy(Major $major)
    {
        return $this->clear($major);
    }
}
