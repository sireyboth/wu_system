<?php
namespace App\Models;

use App\Helpers\IModel;

class Faculty extends IModel
{

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // DEFAULT_FIELD_AND_SHORTCUT may be defined as a constant array elsewhere.
        $base = [];
        if (defined('DEFAULT_FIELD_AND_SHORTCUT') && is_array(DEFAULT_FIELD_AND_SHORTCUT)) {
            $base = DEFAULT_FIELD_AND_SHORTCUT;
        }

        $this->fillable   = array_merge($base);
        $this->searchable = array_merge($this->fillable, [
            'majors.name', 'majors.name_kh', 'majors.name_en', 'majors.shortcut',
        ]);
    }

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
