<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SampleController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $searchable = ['name_kh' => 'Name KH', 'name_en' => 'Name EN', 'shortcut' => 'Group By'];
        return view('admin.sample.index', compact('searchable'));
    }
}
