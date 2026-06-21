<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomMGT; // Double check this matches your filename!
use App\Models\SaleMGT; // Double check this matches your filename!
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
 public function index()
    {
        try {
            $today = Carbon::today()->toDateString();

            // 1. Daily KPIs
            $totalRooms = RoomMGT::count() ?? 0;
            $roomsSoldToday = SaleMGT::whereDate('created_at', $today)->count() ?? 0;
            $availableRooms = RoomMGT::where('status', 'AVAILABLE')->count() ?? 0;
            
            $occupancyRate = $totalRooms > 0 
                ? round(($roomsSoldToday / $totalRooms) * 100, 1) 
                : 0;

            $revenueToday = SaleMGT::whereDate('created_at', $today)
                ->where('status', 'paid')
                ->sum('balance_subtotal') ?? 0;

            $checkInsToday = SaleMGT::whereDate('check_in_date', $today)->count() ?? 0;
            $checkOutsToday = SaleMGT::whereDate('check_out_date', $today)->count() ?? 0;

            // 2. History
            $history = [
                'total_revenue_all_time' => (float)SaleMGT::where('status', 'paid')->sum('balance_subtotal'),
                'total_bookings_count'   => SaleMGT::count() ?? 0,
                'total_customers_served' => SaleMGT::distinct('cus_contact')->count('cus_contact') ?? 0,
            ];

            // 3. Chart (Last 30 Days)
                 $chartData = SaleMGT::where('status', 'paid')
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->select(
            // Use CAST to convert datetime to date for SQL Server
            DB::raw('CAST(created_at AS DATE) as date'), 
            DB::raw('SUM(balance_subtotal) as total')
        )
        // IMPORTANT: SQL Server requires the GROUP BY to match the SELECT exactly
        ->groupBy(DB::raw('CAST(created_at AS DATE)'))
        ->orderBy(DB::raw('CAST(created_at AS DATE)'), 'ASC')
        ->get();

            return response()->json([
                'success' => true,
                'daily'   => [
                    'revenue'    => (float)$revenueToday,
                    'rooms_sold' => $roomsSoldToday,
                    'occupancy'  => $occupancyRate,
                    'available'  => $availableRooms,
                    'checkins'   => $checkInsToday,
                    'checkouts'  => $checkOutsToday,
                ],
                'history' => $history,
                'chart'   => $chartData
            ], 200);

        } catch (\Exception $e) {
            // This is the "Safety Net" - it sends the error as JSON so you can see it
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
