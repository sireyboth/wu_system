<?php
namespace App\Models;

use App\Helpers\IModel;

class Batch extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ ...DEFAULT_FIELD_AND_SHORTCUT, 'academic_year'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
