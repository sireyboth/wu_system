<?php
namespace App\Models;

use App\Helpers\IModel;

class Shift extends IModel
{
    protected $fillable         = [ ...DEFAULT_FIELD_AND_SHORTCUT];
    protected array $searchable = [
         ...DEFAULT_FIELD_AND_SHORTCUT,
    ];
}
