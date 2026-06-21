<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Taxmgt extends Model
{
      use HasFactory, SoftDeletes;

    // This allows the Controller to 'fill' these columns
    protected $fillable = [
        'sale_mgt_id',
        'tax_invoice_number',
        'tax_cus_vattin',
        'tax_cus_address',
        'tax_vat_price',
        'tax_at_price',
        'tax_hidden_price',
        'tax_sub_total',
        'tax_balance_remaining',
        'tax_balance_complete',
        'tax_balance_final',
        'status',
    ];

    // Define the relationship back to the Sale
    public function sale()
    {
        return $this->belongsTo(SaleMgt::class, 'sale_mgt_id');
    }
}
