<?php
namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

abstract class IModel extends Model
{
    use HasFactory, SoftDeletes;

    protected array $searchable = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (empty($this->searchable) && ! empty($this->fillable)) {
            $this->searchable = $this->fillable;
        }
    }

    // public function scopeSearch(Builder $query, ?string $keyword) : Builder
    // {
    //     if (! $keyword || empty($this->searchable)) {
    //         return $query;
    //     }

    //     return $query->where(function (Builder $q) use ($keyword) {

    //         foreach ($this->searchable as $column) {
    //             $q->orWhere($column, 'LIKE', "%{$keyword}%");
    //         }

    //     });
    // }

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

    // Auto combine when creating/updating
    public function setNameKhAttribute(string $value)
    {
        $this->attributes['name_kh'] = $value;
        $this->attributes['name']    = $this->combineName($value, $this->name_en ?? request('name_en'));
    }

    public function setNameEnAttribute(string $value)
    {
        $this->attributes['name_en'] = $value;
        $this->attributes['name']    = $this->combineName($this->name_kh ?? request('name_kh'), $value);
    }

    private function combineName(string $name_kh, string $name_en): string
    {
        return "{$name_kh} ({$name_en})";
    }
}
