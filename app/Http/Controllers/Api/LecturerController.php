<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LecturerRequest;
use App\Http\Resources\LecturerResource;
use App\Models\Lecturer;
use Illuminate\Http\Request;

class LecturerController extends Controller
{
    public function __construct()
    {
        $this->name     = 'Lecturer';
        $this->model    = Lecturer::class;
        $this->resource = LecturerResource::class;
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
        return $this->save($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lecturer $lecturer)
    {
        return $this->view($lecturer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LecturerRequest $request, Lecturer $lecturer)
    {
        return $this->release($request, $lecturer);
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Lecturer $lecturer)
    {
        return $this->disable($lecturer);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Lecturer $lecturer)
    {
        return $this->enable($lecturer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function force_destroy(Lecturer $lecturer)
    {
        return $this->clear($lecturer);
    }
}
