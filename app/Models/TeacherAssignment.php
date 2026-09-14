<?php
namespace App\Models;

class TeacherAssignment extends IModel
{
    protected $fillable = ['lecturer_id', 'class_id', 'role', 'assigned_from', 'assigned_to', 'remark'];

    protected $casts = [
        'assigned_from' => 'date',
        'assigned_to'   => 'date',
    ];

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class, 'class_id');
    }
}
