<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Taxmgt;
use Illuminate\Support\Facades\DB;

class TaxMgtApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    $taxInvoices = \App\Models\Taxmgt::with(['sale'])
            ->when($request->search, function($query) use ($request) {
                $search = $request->search;
                // Search in Tax table columns
                $query->where('tax_invoice_number', 'LIKE', "%{$search}%")
                    // OR search in the related Sale table (Customer Name)
                    ->orWhereHas('sale', function($q) use ($search) {
                        $q->where('cus_first_name', 'LIKE', "%{$search}%")
                            ->orWhere('cus_last_name', 'LIKE', "%{$search}%")
                            ->orWhere('invoice_no', 'LIKE', "%{$search}%");
                    });
            })
            ->latest()
            ->get(); 

        return response()->json([
            'success' => true,
            'data' => $taxInvoices
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        // 1. Validation

        $validated = $request->validate([
            'sale_mgt_id' => 'required|exists:sale_m_g_t_s,id',
            'tax_invoice_number' => 'required|unique:taxmgts',
            'tax_hidden_price' => 'nullable|numeric',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 2. Calculation Logic
                $hiddenPrice = $request->tax_hidden_price ?? 0;
                // We assume you fetch the original subtotal from the SaleMgt model
                $originalSale = \App\Models\SaleMgt::findOrFail($request->sale_mgt_id);
                $newSubTotal = $originalSale->balance_subtotal + $hiddenPrice;
                $vatPrice = $newSubTotal * 0.10; // 10% VAT
                $finalTotal = $newSubTotal + $vatPrice;

                // 3. Create the Record
                $tax = TaxMgt::create([
                    'sale_mgt_id' => $request->sale_mgt_id,
                    'tax_invoice_number' => $request->tax_invoice_number,
                    'tax_hidden_price' => $hiddenPrice,
                    'tax_sub_total' => $newSubTotal,
                    'tax_vat_price' => $vatPrice,
                    'tax_balance_final' => $finalTotal,
                    'status' => 'pending'
                ]);
                return response()->json(['success' => true, 'data' => $tax]);
            });

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
        // We load the tax record PLUS the linked sale PLUS the items inside that sale
        $tax = \App\Models\TaxMgt::with(['sale.items'])->find($id);

        if (!$tax) {
            return response()->json([
                'success' => false,
                'message' => 'Tax Invoice not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tax
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tax = Taxmgt::findOrFail($id);

    $validated = $request->validate([
        'sale_mgt_id'        => 'required|exists:sale_m_g_t_s,id', // Ensure the new sale exists
        'tax_invoice_number' => 'required|string|unique:taxmgts,tax_invoice_number,' . $id,
        'tax_cus_vattin'     => 'nullable|string',
        'tax_cus_address'    => 'nullable|string',
        'tax_hidden_price'   => 'required|numeric',
        'tax_at_price'       => 'required|numeric',
    ]);

    try {
        DB::beginTransaction();

        // 1. Get the Sale record (either the existing one or the new one selected)
        $sale = \App\Models\SaleMGT::findOrFail($request->sale_mgt_id);

        // 2. Use the Sale's subtotal as the base for math
        $basePrice = $sale->balance_subtotal; 
        $newSubTotal = $basePrice + $request->tax_hidden_price + $request->tax_at_price;
        $vat = $newSubTotal * 0.10;
        $finalTotal = $newSubTotal + $vat;

        // 3. Update the record
        $tax->update([
            'sale_mgt_id'        => $request->sale_mgt_id, // Save the new link
            'tax_invoice_number' => $request->tax_invoice_number,
            'tax_cus_vattin'     => $request->tax_cus_vattin,
            'tax_cus_address'    => $request->tax_cus_address,
            'tax_sub_total'      => $basePrice, // Update base if sale changed
            'tax_hidden_price'   => $request->tax_hidden_price,
            'tax_at_price'       => $request->tax_at_price,
            'tax_vat_price'      => $vat,
            'tax_balance_final'  => $finalTotal,
        ]);

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Invoice and Sale link updated!']);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
    }

    /**
     * Remove the specified resource from storage.
     */ 
    public function destroy(string $id)
    {
       try {
            $tax = Taxmgt::findOrFail($id);
            $tax->delete();

            return response()->json(['success' => true, 'message' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete record'], 500);
        }
    }
}
