<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function Index(){
        return view ('campus.index');
    }
}
