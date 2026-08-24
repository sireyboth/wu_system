<?php
namespace App\Models;

class Batch extends IModel
{

    public function __construct()
    {
        $this->fillable = array_merge(DEFAULT_FIELD_AND_SHORTCUT, ['academic_year']);
    }

    public function snapshots()
    {
        return $this->hasMany(StudentSnapshot::class);
    }
}
