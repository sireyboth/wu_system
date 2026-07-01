<?php
namespace App\Models;

use App\Helpers\IModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function communes(): HasMany
    {
        return $this->hasMany(Commune::class);
    }

    public function villages(): HasManyThrough
    {
        return $this->hasManyThrough(Village::class, Commune::class);
    }
}
