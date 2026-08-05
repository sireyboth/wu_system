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
    public function show(Shift $shift)
    {
        return $this->view($shift);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShiftRequest $request, Shift $shift)
    {
        return $this->release($request, $shift);
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        return $this->disable($shift);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Shift $shift)
    {
        return $this->enable($shift);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function force_destroy(Shift $shift)
    {
        return $this->clear($shift);
    }
}
