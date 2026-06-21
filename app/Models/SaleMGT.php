<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleMGT extends Model
{
   use SoftDeletes;
   // Matches your table name
    protected $table = 'sale_m_g_t_s';

    protected $fillable = [
        'invoice_no', 
        'cus_first_name', 'cus_last_name', 'cus_contact',
        'check_in_date', 'check_out_date',
        'balance_subtotal', 'booking_price', 'balance_remaining', 'qty',
        'balance_completion', 'balance_grand_total',
        'status', 'note'
    ];

    /**
     * Relationship: One Sale has Many Items (Rooms)
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_mgt_id');
    }
}
