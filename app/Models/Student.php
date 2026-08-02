<?php
namespace App\Models;

use App\Helpers\IModel;

class Student extends IModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fillable = [
            'person_id',
            'year_level',
            'code',
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

    public function snapshots()
    {
        return $this->hasMany(StudentSnapshot::class);
    }
}
