<?php

namespace App\Http\Controllers;

use App\Models\SaleMGT;
use Illuminate\Http\Request;

class SaleMGTController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       // Process: Fetch data exactly like the API
        $sales = SaleMGT::with('items')->latest()->paginate(10); 

        // Code Work: Return a blade view file and pass the $sales variable
        return view('salemgt.index');
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sale = SaleMGT::with('items')->findOrFail($id);
        
        return view('admin.sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SaleMGT $saleMGT)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SaleMGT $saleMGT)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SaleMGT $saleMGT)
    {
        //
    }
}
