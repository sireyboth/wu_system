<?php
namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;

class Student extends IModel
{
    protected string $keyName = 'payment_as';
    public const NONE         = 'none';
    public const YEARLY       = 'yearly';
    public const SEMESTER     = 'semester';

    protected $fillable = [
        'person_id',
        'guardian_id',
        'batch_id',
        'major_id',
        'group_id',
        'shift_id',
        'status_id',
        'code',
        'payment_as',
        'year_level',
        'from_school',
        'admission_date',
        'degree_type',
        'bacc_2_code',
        'intake',
        'scholarship',
        'entrance_exam',
        'exit_exam',
        'remark',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->searchable = array_map(fn($p) => "person.{$p}", [
            'first_name',
            'last_name',
            'first_name_kh',
            'last_name_kh',
            'dob',
            'sex',
            'email',
        ]);
    }

    protected function casts(): array
    {
        return ['admission_date' => 'date'];
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function guardians()
    {
        return $this->hasMany(Guardian::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function provisionalCertificates()
    {
        return $this->certificates()->provisional();
    }

    public function statusCertificates()
    {
        return $this->certificates()->status();
    }

    public function scopeYearly(Builder $query)
    {
        return $this->getBy($query, self::YEARLY);
    }

    public function scopeSemester(Builder $query)
    {
        return $this->getBy($query, self::SEMESTER);
    }
}
