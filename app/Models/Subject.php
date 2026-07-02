<?php
namespace App\Models;

use App\Helpers\IModel;

class Subject extends IModel
{
    protected $fillable = [
         ...DEFAULT_FIELD_AND_CODE,
        'year_level',
        'major_id',
        'semester',
        'credit',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }
}
