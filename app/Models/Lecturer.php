<?php
namespace App\Models;

use App\Helpers\IModel;

class Lecturer extends IModel
{
    protected $fillable = ['name_kh', 'name_en', 'code', 'remark'];
     protected array $searchable = [
        'name_kh',
        'name_en',
        'code',
        'remark'
    ];
}
