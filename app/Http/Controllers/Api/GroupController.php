<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->name     = 'Group';
        $this->model    = Group::class;
        $this->resource = GroupResource::class;
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
    public function store(GroupRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        return $this->view($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GroupRequest $request, Group $group)
    {
        return $this->release($request, $group);
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        return $this->disable($group);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Group $group)
    {
        return $this->enable($group);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function force_destroy(Group $group)
    {
        return $this->clear($group);
    }
}
