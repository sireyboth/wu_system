<?php
namespace App\Models;

use App\Helpers\IModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commune extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable  = [ ...DEFAULT_FIELD_PRIVATE, 'district_id'];
    public $incrementing = true;
    protected $keyType   = 'int';

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
    }

    /**
     * Shortcut up the chain: commune -> district -> province.
     * Not a relation object (Eloquent can't belongsTo through a belongsTo),
     * just a convenience accessor. Eager-load with ->load('district.province')
     * beforehand to avoid an extra query.
     */
    public function getProvinceAttribute(): ?Province
    {
        return $this->district?->province;
    }
}
