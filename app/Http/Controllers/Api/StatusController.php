<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use App\Http\Resources\StatusResource;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function __construct()
    {
        $this->name     = 'Status';
        $this->model    = Status::class;
        $this->resource = StatusResource::class;
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
    public function store(StatusRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Status $status)
    {
        return $this->view($status);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StatusRequest $request, Status $status)
    {
        return $this->release($request, $status);
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Status $status)
    {
        return $this->disable($status);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Status $status)
    {
        return $this->enable($status);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function force_destroy(Status $status)
    {
        return $this->clear($status);
    }
}
