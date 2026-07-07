<?php
namespace App\Models;

use App\Helpers\IModel;

class Lecturer extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = DEFAULT_FIELD_AND_CODE;

    protected function casts(): array
    {
        return ['hired_at' => 'date'];
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }
}
