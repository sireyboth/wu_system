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
            'batch_id',
            'shift_id',
            'major_id',
            'code',
            'admission_at',
            'bacc_2_code',
            'status',
            'entrance_exam',
            'exit_exam',
        ];
        $batch   = ['batch.name', 'batch.name_en', 'batch.name_kh', 'batch.shortcut', 'batch.academic_year', 'batch.remark'];
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
        $guardian = ['guardians.occupation', 'guardians.pivot.relationship', 'guardians.pivot.is_primary'];

        $this->searchable = array_merge($this->fillable, [
             ...$batch, ...$faculty, ...$person, ...$major, ...$nationality, ...$address, ...$guardian,
        ]);
    }

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

    public function shift()
    {
        return $this->belongsTo(Shift::class);
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
