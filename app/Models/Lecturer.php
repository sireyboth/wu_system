<?php
namespace App\Models;

use App\Helpers\IModel;

class Lecturer extends IModel
{
    protected $fillable         = [ ...DEFAULT_FIELD, 'code'];
    protected array $searchable = [
         ...DEFAULT_FIELD,
        'code',
    ];
}
