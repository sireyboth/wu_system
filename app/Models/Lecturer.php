<?php
namespace App\Models;

use App\Helpers\IModel;

class Lecturer extends IModel
{
    protected $fillable         = ['remark', 'code', 'person_id'];
    protected array $searchable = [
        'remark',
        'code',
    ];

    public function person()
    {
        return $this->hasOne(Person::class);
    }
}
