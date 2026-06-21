<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'sale_mgt_id', 'room_mgt_id',
        'room_number_snapshot', 'room_type_snapshot', 'room_unit_price_snapshot',
         'food_price', 'discount_percent',
        'total_price', 'note'
    ];/**
     * Relationship: This item belongs to one specific Sale
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(SaleMGT::class, 'sale_mgt_id');
    }
}
