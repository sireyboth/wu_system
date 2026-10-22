<?php
namespace App\Models;

class StudentAcademicHistory extends IModel
{
    protected $fillable = [
        'student_id',
        'term_id',
        'batch_id',
        'major_id',
        'group_id',
        'shift_id',
        'campus_id',
        'status_id',
        'year_level',
        'semester',
        'effective_date',
        'is_current',
        'remark',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'is_current'     => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }
}
