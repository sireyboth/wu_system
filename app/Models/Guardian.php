<?php
namespace App\Models;

use App\Helpers\IModel;

class Guardian extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         ...DEFAULT_FIELD,
        'student_id',
        'phones',
        'addresses',
        'job',
        'relationship',
    ];

    protected $casts = ['phones' => 'array', 'addresses' => 'array'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
