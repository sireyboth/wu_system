<?php
namespace App\Models;

use App\Helpers\IModel;

class District extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ ...DEFAULT_FIELD_PRIVATE, 'province_id'];

    public $incrementing = true;
    protected $keyType   = 'int';

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function communes()
    {
        return $this->hasMany(Commune::class);
    }

    public function villages()
    {
        return $this->hasManyThrough(Village::class, Commune::class);
    }
}
