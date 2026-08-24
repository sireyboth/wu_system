<?php
namespace App\Models;

class Event extends IModel
{
    protected $fillable = ['title', 'subtitle', 'start', 'end', 'color', 'repeat_freq', 'repeat_interval', 'repeat_until', 'remarks'];
    protected $casts    = [
        'start'        => 'datetime',
        'end'          => 'datetime',
        'repeat_until' => 'datetime',
    ];
}
