<?php
namespace App\Models;

use App\Helpers\Generic;
use App\Helpers\TModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class IModel extends Model
{
    use HasFactory, SoftDeletes, TModel, Generic;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (empty($this->searchable) && ! empty($this->fillable)) {
            $this->searchable = $this->fillable;
        }
    }
}
