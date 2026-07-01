<?php
namespace App\Models;

use App\Helpers\IModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Province extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = DEFAULT_FIELD_PRIVATE;
     public $incrementing = true;
    protected $keyType = 'int';

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    /**
     * All communes belonging to this province, via districts.
     */
    public function communes(): HasManyThrough
    {
        return $this->hasManyThrough(Commune::class, District::class);
    }

    /**
     * All villages belonging to this province.
     * Eloquent doesn't support a 3-level hasManyThrough out of the box,
     * so this is a plain query rather than a lazy/eager-loadable relation.
     */
    public function villages()
    {
        return Village::whereHas('commune.district', function ($query) {
            $query->where('province_id', $this->id);
        })->get();
    }
}
