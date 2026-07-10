<?php
namespace App\Models;

use App\Helpers\IModel;

class Shift extends IModel
{
    protected $fillable = [ ...DEFAULT_FIELD_AND_SHORTCUT];


    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
