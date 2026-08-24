<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentSnapshotRequest;
use App\Http\Resources\StudentSnapshotResource;
use App\Models\StudentSnapshot;
use Illuminate\Http\Request;

class StudentSnapshotController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Student Snapshot';
        $this->model         = StudentSnapshot::class;
        $this->resource      = StudentSnapshotResource::class;
        $this->relationships = array_merge(
            ['batch', 'campus', 'major', 'group', 'shift', 'status'],
            $this->withStudent()
        );
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
    public function store(StudentSnapshotRequest $request)
    {
        return $this->save($request);
    }

/**
 * Display the specified resource.
 */
    public function show(StudentSnapshot $studentSnapshot)
    {
        return $this->view($studentSnapshot);
    }

/**
 * Update the specified resource in storage.
 */
    public function update(StudentSnapshotRequest $request, StudentSnapshot $studentSnapshot)
    {
        return $this->release($request, $studentSnapshot);
    }

/**
 * Disable the specified resource from storage.
 */
    public function destroy(StudentSnapshot $studentSnapshot)
    {
        return $this->disable($studentSnapshot);
    }

/**
 * Restore a soft-deleted of the resource.
 */
    public function restore(StudentSnapshot $studentSnapshot)
    {
        return $this->enable($studentSnapshot);
    }

/**
 * Remove the specified resource from storage.
 */
    public function force_destroy(StudentSnapshot $studentSnapshot)
    {
        return $this->clear($studentSnapshot);
    }
}
