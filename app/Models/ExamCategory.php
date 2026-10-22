<?php
namespace App\Models;

class ExamCategory extends IModel
{
    protected $fillable = DEFAULT_FIELD;

    public function terms()
    {
        return $this->hasMany(ExamTerm::class);
    }
}
