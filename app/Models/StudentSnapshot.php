<?php
namespace App\Models;

use App\Helpers\IModel;

class StudentSnapshot extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'batch_id',
        'campus_id',
        'major_id',
        'group_id',
        'shift_id',
        'status_id',
        'effective_date',
        'is_current',
        'remark',
    ];

    protected array $searchable = ['sudent.person.first_name'];

    protected $casts = ['effective_date' => 'date', 'is_current' => 'boolean'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
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

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
