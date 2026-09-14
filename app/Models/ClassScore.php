<?php
namespace App\Models;

class ClassScore extends IModel
{
    protected $fillable = ['course_enrollment_id', 'component', 'points', 'recorded_by', 'remark'];

    public function courseEnrollment()
    {
        return $this->belongsTo(CourseEnrollment::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
