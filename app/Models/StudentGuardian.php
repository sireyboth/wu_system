<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class StudentGuardian extends Pivot
{
    protected $table = 'student_guardian';

    protected $fillable = [
        'student_id',
        'guardian_id',
        'relationship',
        'is_primary',
    ];

    protected $casts = [
        'is_primary'   => 'boolean',
        'relationship' => 'string',
        'student_id'   => 'integer',
        'guardian_id'  => 'integer',
    ];
}
