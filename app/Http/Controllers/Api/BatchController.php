<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BatchRequest;
use App\Http\Resources\BatchResource;
use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function __construct()
    {
        $this->name     = 'Batch';
        $this->model    = Batch::class;
        $this->resource = BatchResource::class;
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
    public function store(BatchRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Batch $batch)
    {
        return $this->view($batch);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BatchRequest $request, Batch $batch)
    {
        return $this->release($request, $batch);
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Batch $batch)
    {
        return $this->disable($batch);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Batch $batch)
    {
        return $this->enable($batch);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function force_destroy(Batch $batch)
    {
        return $this->clear($batch);
    }
}
