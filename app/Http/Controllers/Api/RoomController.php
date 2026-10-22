<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Room';
        $this->model         = Room::class;
        $this->resource      = RoomResource::class;
        $this->relationships = ['campus'];
    }

    public function index(Request $request)
    {
        return $this->list($request);
    }

    public function store(RoomRequest $request)
    {
        return $this->save($request);
    }

    public function show(Room $room)
    {
        return $this->view($room);
    }

    public function update(RoomRequest $request, Room $room)
    {
        return $this->release($request, $room);
    }

    public function destroy(Room $room)
    {
        return $this->disable($room);
    }

    public function restore(Room $room)
    {
        return $this->enable($room);
    }

    public function force_destroy(Room $room)
    {
        return $this->clear($room);
    }
}
