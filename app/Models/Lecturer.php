<?php
namespace App\Models;

use App\Helpers\IModel;

class Lecturer extends IModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $major   = ['major.name', 'major.name_kh', 'major.name_en', 'major.shortcut'];
        $faculty = ['major.faculty.name', 'major.faculty.name_kh', 'major.faculty.name_en', 'major.faculty.shortcut'];
        $person  = ['person.first_name', 'person.last_name', 'person.first_name_kh', 'person.last_name_kh',
            'person.dob', 'person.sex', 'person.email',
        ];
        $nationality = ['person.nationality.name', 'person.nationality.name_en', 'person.nationality.name_kh', 'person.nationality.code'];
        $address     = ['person.addresses.type', 'person.addresses.street', 'person.addresses.house_no',
            'person.addresses.province.name', 'person.addresses.province.name_en', 'person.addresses.province.name_en',
            'person.addresses.district.name', 'person.addresses.district.name_en', 'person.addresses.district.name_en',
            'person.addresses.commune.name', 'person.addresses.commune.name_en', 'person.addresses.commune.name_en',
            'person.addresses.village.name', 'person.addresses.village.name_en', 'person.addresses.village.name_en',
        ];

        $this->fillable   = ['major_id', 'hired_at', 'remark', 'code', 'person_id'];
        $this->searchable = array_merge($this->fillable, [
             ...$major, ...$faculty, ...$person, ...$nationality, ...$address,
        ]);
    }

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
