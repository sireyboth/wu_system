<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
     use SoftDeletes;

    protected $fillable = [
        'room_type',
        'room_number',
        'default_unit_price',
        'status',
        'note',
    ];
}
