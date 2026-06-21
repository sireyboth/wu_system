<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomMGT;
use App\Models\SaleMGT;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
   public function index()
    {
        $today = Carbon::today()->toDateString();

        // 1. Daily Analytics
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

        // 2. History Data
        $history = [
            'total_revenue_all_time' => (float)SaleMGT::where('status', 'paid')->sum('balance_subtotal'),
            'total_bookings_count'   => SaleMGT::count() ?? 0,
            'total_customers_served' => DB::table('sale_m_g_t_s')->distinct()->count('cus_contact') ?? 0,
        ];

        // 3. Chart Data (30 Days)
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

        // Return the VIEW and pass the variables
        return view('dashboard.index', compact(
            'revenueToday', 
            'roomsSoldToday', 
            'availableRooms', 
            'occupancyRate', 
            'checkInsToday', 
            'checkOutsToday', 
            'history', 
            'chartData'
        ));
    }
// Add this method to your Controller
public function exportSales() 
{
    // This naming convention is professional: SiteName_ReportType_Date
    $fileName = 'Hotel_Sales_Report_' . now()->format('Y-m-d') . '.xlsx';
    
    return Excel::download(new SalesExport, $fileName);
}
    }
