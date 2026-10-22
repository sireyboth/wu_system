<?php
namespace App\Models;

class StudentLeave extends IModel
{
    protected $fillable = ['student_id', 'starts_on', 'ends_on', 'status', 'approved_by', 'remark'];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on'   => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
