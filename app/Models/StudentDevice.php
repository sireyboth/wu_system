<?php
namespace App\Models;

class StudentDevice extends IModel
{
    protected $fillable = ['fingerprint', 'trusted', 'first_seen_at', 'last_seen_at', 'remark'];

    protected $casts = [
        'trusted'       => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at'  => 'datetime',
    ];

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'device_id');
    }
}
