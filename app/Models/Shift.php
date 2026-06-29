<?php
namespace App\Models;

use App\Helpers\IModel;

class Shift extends IModel
{
    protected $fillable = ['name_kh', 'name_en', 'shortcut', 'remark'];
    protected array $searchable = [
        'name_kh',
        'name_en',
        'shortcut',
        'remark'
    ];
}
