<?php
namespace App\Models;

use App\Helpers\IModel;

class Faculty extends IModel
{

    protected $fillable = [ ...DEFAULT_FIELD_AND_SHORTCUT, 'shortcut'];

    /**
     * Get all majors that belong to this faculty
     */
    public function majors()
    {
        return $this->hasMany(Major::class);
    }

    public function lecturers()
    {
        return $this->hasMany(Lecturer::class);
    }

}
