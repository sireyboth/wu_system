<?php
namespace App\Models;

use App\Helpers\IModel;

class Major extends IModel
{
    protected $fillable = [ ...DEFAULT_FIELD_AND_SHORTCUT, 'faculty_id'];

    /**
     * Get the faculty that owns this major
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function lecturers()
    {
        return $this->hasMany(Lecturer::class);
    }
}
