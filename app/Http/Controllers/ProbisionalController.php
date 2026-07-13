<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProbisionalController extends Controller
{
    public function Index(){
        return view('probisional.index');
    }
}
