<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomMGTController extends Controller
{
    public function index()
    {
        // Professionally, we only return the view. 
        // JavaScript will fetch the data later.
        return view('roommgt.index');
    }
}
