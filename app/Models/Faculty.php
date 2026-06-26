<?php
namespace App\Models;

use App\Helpers\IModel;

class Faculty extends IModel
{

    protected $fillable = ['name_kh', 'name_en', 'shortcut', 'remark'];

    /**
     * Get all majors that belong to this faculty
     */
    public function majors()
    {
        return $this->hasMany(Major::class);
    }

}
