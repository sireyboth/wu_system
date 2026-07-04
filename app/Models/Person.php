<?php
namespace App\Models;

use App\Helpers\IModel;

class Person extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'first_name_kh',
        'last_name_kh',
        'nationality_id',
        'dob',
        'sex',
        'email',
        'phones',
    ];

    protected function casts(): array
    {
        return ['phones' => 'array', 'dob' => 'date'];
    }

    public function guardian()
    {
        return $this->hasOne(Guardian::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function lecturer()
    {
        return $this->hasOne(Lecturer::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }
}
