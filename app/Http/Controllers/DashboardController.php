<?php
namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {

        // Return the VIEW and pass the variables
        return view('dashboard.index');
    }

// // Add this method to your Controller
//     public function exportSales()
//     {
//         // This naming convention is professional: SiteName_ReportType_Date
//         $fileName = 'Hotel_Sales_Report_' . now()->format('Y-m-d') . '.xlsx';

//         return Excel::download(new SalesExport, $fileName);
//     }
}
