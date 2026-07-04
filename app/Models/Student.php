<?php
namespace App\Models;

use App\Helpers\IModel;

class Student extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'person_id',
        'batch_id',
        'code',
        'major_id',
        'admission_at',
        'bacc_2_code',
        'status',
        'entrance_exam',
        'exit_exam',
    ];

    protected function casts(): array
    {
        return ['admission_at' => 'date'];
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function guardians()
    {
        return $this->belongsToMany(Guardian::class, StudentGuardian::class)
            ->withPivot(['relationship', 'is_primary'])->withTimestamps();
    }
}
