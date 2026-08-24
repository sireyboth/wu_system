<?php
namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

trait TModel
{
    protected array|string $searchable = [];
    protected string $keyName            = 'shortcut';
    protected string $operator           = '=';
    protected array $searchableExclude   = ['password', 'remember_token', 'api_token'];

    public function scopeSearch(Builder $query, ?string $keyword, array | string | null $fields = null): Builder
    {
        $searchable = $this->resolveSearchableColumns();
        if (blank($keyword) || empty($searchable)) {
            return $query;
        }

        $requested = Arr::wrap($fields);
        $allowed   = $requested
            ? array_values(array_intersect($requested, $searchable))
            : $searchable;

        if (empty($allowed)) {
            $allowed = $searchable;
        }

        // Just use the macro you already registered
        return $query->whereLike($allowed, $keyword);
    }

    protected function resolveSearchableColumns(): array
    {
        // explicit config always wins — this is purely a fallback
        if (! empty($this->searchable)) {
            return $this->searchable;
        }

        return Cache::rememberForever("searchable_columns:" . $this->getTable(), function () {
            $columns = Schema::getColumns($this->getTable()); // Laravel 11+

            return collect($columns)
                ->filter(fn($col) => in_array($col['type_name'], ['varchar', 'char', 'text', 'mediumtext', 'longtext']))
                ->pluck('name')
                ->reject(fn($name) => in_array($name, $this->searchableExclude))
                ->values()
                ->all();
        });
    }

    public function getFullNameAttribute()
    {
        return to_name($this);
    }

    protected function getBy(Builder $query, mixed $value = null)
    {
        return $query->where($this->keyName, $this->operator, $value);
    }
}
