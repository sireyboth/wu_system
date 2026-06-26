<?php
namespace App\Models;

use App\Helpers\IModel;

class Campus extends IModel
{
    protected $fillable = ['name_kh', 'name_en', 'remark'];
}
