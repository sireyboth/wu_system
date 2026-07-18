<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberCounter extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['year', 'last_sequence'];
}
