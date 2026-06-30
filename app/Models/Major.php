<?php
namespace App\Models;

use App\Helpers\IModel;

class Major extends IModel
{
    protected $fillable = [
        'name_kh',
        'name_en',
        'faculty_id',
        'shortcut',
        'remark'];

    /**
     * Get the faculty that owns this major
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
}
