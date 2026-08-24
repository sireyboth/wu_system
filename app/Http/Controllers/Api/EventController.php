<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {
        $this->model    = Event::class;
        $this->resource = EventResource::class;
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
    public function store(EventRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return $this->view($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventRequest $request, Event $event)
    {
        return $this->save($request, $event);
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        return $this->disable($event);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Event $event)
    {
        return $this->enable($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function empty(Event $event)
    {
        return $this->clear($event);
    }
}
