<?php
namespace App\Models;

class Group extends IModel
{
    protected $fillable = DEFAULT_FIELD_AND_SHORTCUT;

    public function snapshots()
    {
        return $this->hasMany(StudentSnapshot::class);
    }
}
