<?php
namespace App\Models;

class ClassSchedule extends IModel
{
    protected $fillable = ['class_id', 'room_id', 'day_of_week', 'starts_at', 'ends_at', 'remark'];

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class, 'class_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
