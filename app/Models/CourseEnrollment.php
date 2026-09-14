<?php
namespace App\Models;

class CourseEnrollment extends IModel
{
    protected $fillable = ['student_academic_history_id', 'class_id', 'status', 'remark'];

    public function studentAcademicHistory()
    {
        return $this->belongsTo(StudentAcademicHistory::class);
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class, 'class_id');
    }

    public function classScores()
    {
        return $this->hasMany(ClassScore::class);
    }
}
