<?php

namespace App\Http\Controllers;

class AlertController extends Controller
{
    public function index()
    {
        return view('alerts.index');
    }
}
