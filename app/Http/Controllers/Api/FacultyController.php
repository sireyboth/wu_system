<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacultyRequest;
use App\Http\Resources\FacultyResource;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Faculty';
        $this->model         = Faculty::class;
        $this->resource      = FacultyResource::class;
        $this->relationships = 'majors';
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
    public function store(FacultyRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty)
    {
        return $this->view($faculty);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FacultyRequest $request, Faculty $faculty)
    {
        return $this->release($request, $faculty);
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Faculty $faculty)
    {
        return $this->disable($faculty);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Faculty $faculty)
    {
        return $this->enable($faculty);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function force_destroy(Faculty $faculty)
    {
        return $this->clear($faculty);
    }
}
