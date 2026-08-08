<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

abstract class IModel extends Model
{
    use HasFactory, SoftDeletes;

    protected array $searchable = [];
    protected string $keyName   = 'shortcut';
    protected string $operator  = '=';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (empty($this->searchable) && ! empty($this->fillable)) {
            $this->searchable = $this->fillable;
        }
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (blank($keyword) || empty($this->searchable)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            foreach ($this->searchable as $field) {
                if (! Str::contains($field, '.')) {
                    $q->orWhere($field, 'like', "%{$keyword}%");

                    continue;
                }

                $parts    = explode('.', $field);
                $column   = array_pop($parts);
                $relation = implode('.', $parts);

                $q->orWhereHas($relation, function (Builder $query) use ($column, $keyword) {
                    $query->where($column, 'like', "%{$keyword}%");
                });

            }
        });
    }

    public function getNameAttribute()
    {
        return to_name($this);
    }

    protected function getBy(Builder $query, mixed $value = null)
    {
        return $query->where($this->keyName, $this->operator, $value);
    }
}
