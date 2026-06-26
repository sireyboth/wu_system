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
    public function show(string $id)
    {
        return $this->view($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CampusRequest $request, string $id)
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
