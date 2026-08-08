<?php
namespace App\Models;

class Country extends IModel
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
