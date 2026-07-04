<?php
namespace App\Models;

use App\Helpers\IModel;

class Lecturer extends IModel
{
    protected $fillable         = ['major_id', 'hired_at', 'remark', 'code', 'person_id'];
    protected array $searchable = [
        'remark',
        'code',
    ];

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
