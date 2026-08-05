<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Village extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable  = [ ...DEFAULT_FIELD_PRIVATE, 'commune_id'];
    public $incrementing = true;
    protected $keyType   = 'int';

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    /**
     * Convenience accessors up the chain. Eager-load with
     * ->load('commune.district.province') to avoid N+1 queries.
     */
    public function getDistrictAttribute(): ?District
    {
        return $this->commune?->district;
    }

    public function getProvinceAttribute(): ?Province
    {
        return $this->commune?->district?->province;
    }
}
