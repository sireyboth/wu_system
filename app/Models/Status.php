<?php
namespace App\Models;

class Status extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [...DEFAULT_FIELD_AND_SHORTCUT, 'can_attend'];

    protected $casts = [
        'can_attend' => 'boolean',
    ];

    public function student()
    {
        return $this->hasOne(Student::class);
    }
}
