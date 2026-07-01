<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
