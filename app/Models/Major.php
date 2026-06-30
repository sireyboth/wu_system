<?php
namespace App\Models;

use App\Helpers\IModel;

class Major extends IModel
{
    protected $fillable = [
         ...DEFAULT_FIELD,
        'faculty_id',
        'shortcut',
    ];

    /**
     * Get the faculty that owns this major
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
}
