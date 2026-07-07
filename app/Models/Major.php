<?php
namespace App\Models;

use App\Helpers\IModel;

class Major extends IModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // DEFAULT_FIELD_AND_SHORTCUT may be defined as a constant array elsewhere.
        $base = [];
        if (defined('DEFAULT_FIELD_AND_SHORTCUT') && is_array(DEFAULT_FIELD_AND_SHORTCUT)) {
            $base = DEFAULT_FIELD_AND_SHORTCUT;
        }

        $this->fillable   = array_merge($base, ['faculty_id']);
        $this->searchable = array_merge($this->fillable, [
            'faculty.name', 'faculty.name_kh', 'faculty.name_en', 'faculty.shortcut',
        ]);
    }

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
