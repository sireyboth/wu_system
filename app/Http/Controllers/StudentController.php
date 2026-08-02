<?php
namespace App\Http\Controllers;

use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return view('student.index');
    }

    public function import_data(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv,xls']);
        $response = $this->import(new StudentsImport(), $request->file('file'));

        return back()->with('import_result', $response);
    }

    public function export_data()
    {
        return $this->export(new StudentsExport());
    }
}
