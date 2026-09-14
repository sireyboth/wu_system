<?php
namespace App\Models;

class ClassSession extends IModel
{
    protected $fillable = ['class_id', 'session_date', 'started_at', 'ended_at', 'locked_at', 'status', 'remark'];

    protected $casts = [
        'session_date' => 'date:Y-m-d',
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
        'locked_at'    => 'datetime',
    ];

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class, 'class_id');
    }

    public function sessionRosters()
    {
        return $this->hasMany(SessionRoster::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function qrTokens()
    {
        return $this->hasMany(QrToken::class);
    }
}
