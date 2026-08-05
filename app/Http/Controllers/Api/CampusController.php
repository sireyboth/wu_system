<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CampusRequest;
use App\Http\Resources\CampusResource;
use App\Models\Campus;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function __construct()
    {
        $this->name     = 'Campus';
        $this->model    = Campus::class;
        $this->resource = CampusResource::class;
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
    public function store(CampusRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Campus $campus)
    {
        return $this->view($campus);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CampusRequest $request, Campus $campus)
    {
        return $this->release($request, $campus);
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Campus $campus)
    {
        return $this->disable($campus);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Campus $campus)
    {
        return $this->enable($campus);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function force_destroy(Campus $campus)
    {
        return $this->clear($campus);
    }
}
