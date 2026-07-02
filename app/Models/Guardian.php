<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['person_id', 'relationship'];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
