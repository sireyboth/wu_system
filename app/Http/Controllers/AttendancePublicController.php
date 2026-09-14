<?php

namespace App\Http\Controllers;

class AttendancePublicController extends Controller
{
    public function index()
    {
        return view('attend.index');
    }
}
