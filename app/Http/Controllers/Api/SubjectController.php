<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function __construct()
    {
        $this->name     = 'Subject';
        $this->model    = Subject::class;
        $this->resource = SubjectResource::class;
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
    public function store(SubjectRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->view($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubjectRequest $request, string $id)
    {
        return $this->release($request, $id);
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->disable($id);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(string $id)
    {
        return $this->enable($id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function force_destroy(string $id)
    {
        return $this->clear($id);
    }
}
