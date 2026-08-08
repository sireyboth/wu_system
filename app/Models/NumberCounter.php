<?php
namespace App\Models;

class NumberCounter extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['year', 'last_sequence'];
}
