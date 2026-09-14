<?php
namespace App\Models;

/**
 * One offering/section of a subject, one term. Named ClassSection, not
 * Class — "class" is a reserved word in PHP. Table stays "classes".
 */
class ClassSection extends IModel
{
    protected $table = 'classes';

    protected $fillable = ['subject_id', 'term_id', 'campus_id', 'shift_id', 'capacity', 'code', 'remark'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class, 'class_id');
    }

    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class, 'class_id');
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class, 'class_id');
    }

    public function scoreConfig()
    {
        return $this->hasOne(ClassScoreConfig::class, 'class_id');
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class, 'class_id');
    }
}
