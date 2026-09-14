<?php
namespace App\Models;

class SessionRoster extends IModel
{
    protected $fillable = ['class_session_id', 'course_enrollment_id', 'remark'];

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function courseEnrollment()
    {
        return $this->belongsTo(CourseEnrollment::class);
    }

    public function attendanceRecord()
    {
        return $this->hasOne(AttendanceRecord::class);
    }
}
