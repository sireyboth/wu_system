<?php
namespace App\Models;

class Room extends IModel
{
    protected $fillable = [...DEFAULT_FIELD_AND_CODE, 'campus_id', 'capacity'];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }
}
