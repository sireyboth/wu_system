<?php
namespace App\Models;

use App\Helpers\IModel;

class Subject extends IModel
{
    protected $fillable = [...DEFAULT_FIELD, 'code', 'year', 'semester', 'credit'];
}
