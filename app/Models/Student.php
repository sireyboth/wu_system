<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'person_id',
        'bactch_id',
        'code',
        'admission_at',
        'bacc_2_code',
        'status',
        'entrance_exam',
        'exit_exam',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
