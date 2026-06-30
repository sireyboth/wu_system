<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function Index()
    {
        return view('faculty.index');
    }
}
