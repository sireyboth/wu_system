<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
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
