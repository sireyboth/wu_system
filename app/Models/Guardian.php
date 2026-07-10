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
    protected $fillable = ['phones', 'occupation', 'remark'];

    protected $casts = ['phones' => 'array', 'addresses' => 'array'];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, StudentGuardian::class)
            ->withPivot(['relationship', 'is_primary'])->withTimestamps();
    }
}
