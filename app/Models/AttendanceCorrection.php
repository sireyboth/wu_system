<?php
namespace App\Models;

class AttendanceCorrection extends IModel
{
    protected $fillable = [
        'attendance_record_id', 'requested_by', 'approved_by',
        'old_status', 'new_status', 'reason', 'status', 'remark',
    ];

    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
