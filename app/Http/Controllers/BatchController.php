<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function Index(){
        return view ('batch.index');
    }
}
