<?php
namespace App\Models;

use App\Helpers\IModel;

class Shift extends IModel
{
    protected $fillable         = [ ...DEFAULT_FIELD, 'shortcut'];
    protected array $searchable = [
         ...DEFAULT_FIELD,
        'shortcut',
    ];
}
