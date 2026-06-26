<?php
namespace App\Models;

use App\Helpers\IModel;

class Subject extends IModel
{
    protected $fillable = ['name_kh', 'name_en', 'code', 'year', 'semester', 'credit', 'remark'];
}
