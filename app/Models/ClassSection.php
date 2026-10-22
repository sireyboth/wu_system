<?php
namespace App\Models;

/**
 * One offering/section of a subject, one term. Named ClassSection, not
 * Class — "class" is a reserved word in PHP. Table stays "classes".
 */
class ClassSection extends IModel
{
    protected $table = 'classes';

    protected $fillable = [
        'subject_id', 'term_id', 'campus_id', 'shift_id', 'capacity', 'code', 'remark',
        // Descriptive-only — see the migration that added these, none of
        // this drives enrollment or scheduling (that stays course_enrollments
        // / class_schedules). majors() below is a many-to-many, not fillable.
        'batch_id', 'room_number', 'time_slot',
    ];

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

    public function majors()
    {
        // Explicit FK names — Laravel would otherwise infer 'class_section_id'
        // from the model name, but the pivot column (and every other FK
        // pointing at this table) is 'class_id', matching `classes`.
        return $this->belongsToMany(Major::class, 'class_major', 'class_id', 'major_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
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
