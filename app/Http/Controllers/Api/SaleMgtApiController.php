<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaleMGT;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use App\Models\RoomMGT;


// Carbon::parse($date): 
// This handles various date formats automatically. It converts 
// "2026-02-10" into a smart PHP object that understands time.
use Carbon\Carbon;

class SaleMgtApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Use paginate(10) instead of get()
    $sales = \App\Models\SaleMGT::with(['items' => function($query) {
            $query->orderBy('created_at', 'asc');
        }])
        ->when($request->search, function($query) use ($request) {
            $search = $request->search;
            $query->where('invoice_no', 'LIKE', "%{$search}%")
                ->orWhere('cus_first_name', 'LIKE', "%{$search}%")
                ->orWhere('cus_last_name', 'LIKE', "%{$search}%")
                ->orWhere('cus_contact', 'LIKE', "%{$search}%");
        })
        ->latest() 
        ->paginate(10); // This automatically handles page numbers

    return response()->json([
        'success' => true,
        'data'    => $sales // This now contains the pagination metadata!
    ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    return DB::transaction(function () use ($request) {
        
        // 1. Calculate the global number of nights
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);
        
        // Ensure at least 1 day is charged
        $nights = $checkIn->diffInDays($checkOut);
        $nights = $nights <= 0 ? 1 : $nights;

        // 2. Create the Parent (SaleMGT)
        // Note: 'qty' is now stored here in the parent table
        $sale = SaleMGT::create([
            'invoice_no'         => 'INV-' . now()->format('YmdHis'),
            'cus_first_name'     => $request->cus_first_name,
            'cus_last_name'      => $request->cus_last_name,
            'cus_contact'        => $request->cus_contact,
            'check_in_date'      => $request->check_in_date,
            'check_out_date'     => $request->check_out_date,
            'qty'                => $nights, // Storing nights as decimal in SaleMGT
            'booking_price'      => $request->booking_price ?? 0,
            'balance_completion' => $request->balance_completion ?? 0,
            'status'             => 'pending',
            'note'               => $request->note ?? '',
        ]);

        $subtotal = 0;

        // 3. Loop through Rooms (Items)
        // Assuming your frontend sends an array of rooms
        foreach ($request->rooms as $item) {
            $room = RoomMGT::findOrFail($item['room_id']); 
            
            // We use the room's default price or a price sent from the form
            $unitPrice = $item['unit_price'] ?? $room->default_unit_price; 

            // MATH: ((Nights * UnitPrice) * (1 - Discount%)) + Food
            $discount = $item['discount'] ?? 0;
            $foodPrice = $item['food_price'] ?? 0;
            
            $itemPrice = (($nights * $unitPrice) * (1 - $discount / 100)) + $foodPrice;
            
            $sale->items()->create([
                'room_mgt_id'              => $room->id,
                'room_number_snapshot'     => $room->room_number,
                'room_type_snapshot'       => $room->room_type,
                'room_unit_price_snapshot' => $unitPrice,
                // 'qty' is NOT here anymore; it is in the parent.
                'food_price'               => $foodPrice,
                'discount_percent'         => $discount,
                'total_price'              => $itemPrice,
                'note'                     => $item['note'] ?? '',
            ]);

            $subtotal += $itemPrice;
        }

        // 4. Final Totals Mapping (Based on your specific rules)
        $remaining = $subtotal - $sale->booking_price;
        $grandTotal = $remaining - $sale->balance_completion;

        $sale->update([
            'balance_subtotal'    => $subtotal,
            'balance_remaining'   => $remaining,
            'balance_grand_total' => $grandTotal
        ]);

        return response()->json([
            'success' => true,
            'message' => "Invoice created for $nights nights.",
            'data'    => $sale->load('items')
        ]);
    });
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Process: Find one sale or fail; include the item snapshots
    $sale = SaleMGT::with('items')->findOrFail($id);

    return response()->json([
        'success' => true,
        'data' => $sale
    ]);
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, $id)
{
    // DEBUG: This will write the incoming request to storage/logs/laravel.log
    \Log::info('Update Request Received', $request->all());

    return DB::transaction(function () use ($request, $id) {
        $sale = SaleMGT::findOrFail($id);

        if ($request->input('action_type') === 'checkout') {
            // FORCE the update and save
            $sale->status = 'Paid'; 
            $sale->balance_completion = $request->input('balance_completion');
            $sale->save(); // save() is more direct than update()

            return response()->json([
                'success' => true,
                'message' => 'Status forced to Paid',
                'debug_received' => $request->all() // Send back what was received
            ]);
        }

        // --- 2. FULL EDIT (The Sledgehammer) ---
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);
        $nights = $checkIn->diffInDays($checkOut) ?: 1;

        // Update basic info
        $sale->update([
            'cus_first_name' => $request->cus_first_name,
            'cus_last_name' => $request->cus_last_name,
            'cus_contact' => $request->cus_contact,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'qty' => $nights,
            'booking_price' => $request->booking_price,
            'balance_completion' => $request->balance_completion ?? 0,
            // NOTICE: We removed 'status' update here so it stays whatever it was
        ]);

        // Re-sync rooms
        $sale->items()->delete(); 
        $subtotal = 0;

        if ($request->has('rooms')) {
            foreach ($request->rooms as $item) {
                // Skip empty rows if any
                if (!isset($item['room_id'])) continue;

                $room = RoomMGT::findOrFail($item['room_id']);
                $unitPrice = $room->default_unit_price;
                $itemPrice = (($nights * $unitPrice) * (1 - ($item['discount'] ?? 0) / 100)) + ($item['food_price'] ?? 0);

                $sale->items()->create([
                    'room_mgt_id' => $room->id,
                    'room_number_snapshot' => $room->room_number,
                    'room_type_snapshot' => $room->room_type,
                    'room_unit_price_snapshot' => $unitPrice,
                    'food_price' => $item['food_price'] ?? 0,
                    'discount_percent' => $item['discount'] ?? 0,
                    'total_price' => $itemPrice,
                    'note' => $item['note'] ?? '',
                ]);
                $subtotal += $itemPrice;
            }
        }

        // Final Totals
        $remaining = $subtotal - $sale->booking_price;
        $grandTotal = $remaining - $sale->balance_completion;

        $sale->update([
            'balance_subtotal' => $subtotal,
            'balance_remaining' => $remaining,
            'balance_grand_total' => $grandTotal
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Booking details updated successfully.'
        ]);
    });
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      return DB::transaction(function () use ($id) {
        $sale = SaleMGT::findOrFail($id);

        // FIX: Change status ONLY. 
        // Do NOT call $sale->delete() or $sale->items()->delete()
        $sale->update([
            'status' => 'Cancelled'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated to Cancelled.'
        ]);
    });
    }
}
