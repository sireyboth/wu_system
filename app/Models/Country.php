<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
/**
 * The attributes that are mass assignable.
 *
 * @var array
 */
    protected $fillable = ['name', 'ranking', 'alpha2', 'alpha3', 'nationality'];

    public function person()
    {
        return $this->hasOne(Person::class);
    }
}
