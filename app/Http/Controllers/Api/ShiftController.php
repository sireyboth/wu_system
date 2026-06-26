<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShiftRequest;
use App\Http\Resources\ShiftResource;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->name     = 'Shift';
        $this->model    = Shift::class;
        $this->resource = ShiftResource::class;
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
    public function store(ShiftRequest $request)
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
    public function update(ShiftRequest $request, string $id)
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
