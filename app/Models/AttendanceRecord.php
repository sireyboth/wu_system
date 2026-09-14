<?php
namespace App\Models;

class AttendanceRecord extends IModel
{
    protected $fillable = [
        'class_session_id', 'student_id', 'session_roster_id', 'status', 'method',
        'marked_at', 'device_id', 'ip_address', 'remark',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function sessionRoster()
    {
        return $this->belongsTo(SessionRoster::class);
    }

    public function device()
    {
        return $this->belongsTo(StudentDevice::class, 'device_id');
    }

    public function verification()
    {
        return $this->hasOne(AttendanceVerification::class);
    }

    public function corrections()
    {
        return $this->hasMany(AttendanceCorrection::class);
    }
}
