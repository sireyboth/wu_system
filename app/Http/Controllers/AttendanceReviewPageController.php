<?php

namespace App\Http\Controllers;

class AttendanceReviewPageController extends Controller
{
    public function index()
    {
        return view('attendance-review.index');
    }
}
