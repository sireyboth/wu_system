<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'person_id',
        'province_id',
        'district_id',
        'commune_id',
        'village_id',
        'street',
        'house_no',
        'type',
        'remark',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
