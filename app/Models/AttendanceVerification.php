<?php
namespace App\Models;

class AttendanceVerification extends IModel
{
    protected $fillable = ['attendance_record_id', 'risk_score', 'signals', 'flagged', 'reviewed_by', 'reviewed_at', 'remark'];

    protected $casts = [
        'signals'     => 'array',
        'flagged'     => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
