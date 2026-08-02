<?php
namespace App\Models;

use App\Helpers\IModel;

class StudentTerm extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'term_id',
        'status_id',
        'confirm_by',
        'confirmed_at',
        'remark',
    ];

    protected $casts = ['confirmed_at' => 'date'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function status()
    {
        return $this->belongsTo(Student::class);
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirm_by');
    }
}
