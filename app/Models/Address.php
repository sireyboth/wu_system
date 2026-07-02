<?php
namespace App\Models;

use App\Helpers\IModel;

class Address extends IModel
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

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}
