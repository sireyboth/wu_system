<?php
namespace App\Models;

use App\Helpers\IModel;

class Batch extends IModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ ...DEFAULT_FIELD, 'shortcut', 'academic_year'];
}
