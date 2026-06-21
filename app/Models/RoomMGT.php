<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomMGT extends Model
{
     use SoftDeletes;

    protected $fillable = [
        'room_number',    
        'room_type',
        'default_unit_price',
        'status',
        'note',
    ];
}
