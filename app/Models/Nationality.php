<?php
namespace App\Models;

use App\Helpers\IModel;

class Nationality extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = DEFAULT_FIELD_AND_CODE;

    public function people()
    {
        return $this->hasMany(Person::class);
    }
}
