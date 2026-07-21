<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomMGT;

class RoomMGTApiController extends Controller
{
// GET (List/Search)
    public function index(Request $request)
    {
       $query = RoomMGT::query();

    if ($request->has('search') && $request->search != '') {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->where('room_number', 'LIKE', "%{$searchTerm}%")
              ->orWhere('room_type', 'LIKE', "%{$searchTerm}%");
        });
    }

    return $query->latest()->paginate(10);
    }

    public function store(Request $request)
{
// POST
    // STEP 1: Validation
    $validated = $request->validate([
        'room_number' => 'required|string|unique:room_m_g_t_s,room_number',
        'room_type'          => 'required|string|max:100',
        'default_unit_price' => 'required|numeric|min:0',
        'status'             => 'required|in:available,maintenance,inactive,occupied',
        'note'               => 'nullable|string',
    ]);

    // STEP 2: Persistence (Saving)
    $room = \App\Models\RoomMGT::create($validated);

    // STEP 3: Response 
    return response()->json([
        'success' => true,
        'message' => 'Room created successfully!',
        'data'    => $room
    ], 201); 
}

//Show By ID
public function show($id)
{
    $room = \App\Models\RoomMGT::find($id);

    if (!$room) {
        return response()->json(['message' => 'Room not found'], 404);
    }

    return response()->json($room);
}

//Update
public function update(Request $request, $id)
{
    $roommgt = \App\Models\RoomMGT::find($id);

    if (!$roommgt) {
        return response()->json(['message' => 'Room not found'], 404);
    }
    $validated = $request->validate([
        'room_number'        => "required|unique:room_m_g_t_s,room_number,{$id}",
        'room_type'          => 'required',
        'default_unit_price' => 'required|numeric',
        'status'             => 'required',
        'note'               => 'nullable|string',
    ]);
    // Force the update and check the boolean result
    $updated = $roommgt->update($validated);
    return response()->json([
        'success' => $updated,
        'message' => $updated ? 'Updated!' : 'Update failed',
        'data'    => $roommgt->fresh() // .fresh() gets the latest data from the DB
    ]);
}

public function destroy($id)
{
    // 1. Try to find it
    $item = \App\Models\RoomMGT::find($id);

    // 2. Check if it actually exists before trying to delete
    if (!$item) {
        return response()->json([
            'success' => false,
            'message' => "Error: Room ID {$id} not found in database."
        ], 404); // 404 is the professional way to say "Doesn't exist"
    }

    // 3. Kill it
    $item->delete();

    // 4. Confirm
    return response()->json([
        'success' => true,
        'message' => 'Room deleted successfully.'
    ], 200);
}

}

