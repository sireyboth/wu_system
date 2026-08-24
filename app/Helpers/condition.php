<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Register API resources with soft delete routes (restore + force delete)
 */
if (! function_exists('api_routes')) {
    function api_routes(array $resources): void
    {
        Route::apiResources($resources);

        foreach ($resources as $slug => $controller) {
            $parameter = str_replace('-', '_', Str::singular($slug));

            // Restore
            Route::patch("{$slug}/{{$parameter}}/restore", [$controller, 'restore'])
                ->withTrashed()
                ->name("{$slug}.restore");

            // Delete data from DB
            Route::delete("{$slug}/{{$parameter}}/empty", [$controller, 'empty'])
                ->name("{$slug}.empty");
        }
    }
}

if (! function_exists('web_routes')) {
    function web_routes(array $actions, callable $fn): void
    {
        foreach ($actions as $slug => $controller) {
            Route::get("/{$slug}", [$controller, $slug])->name("{$slug}.invoke");
            $fn();
        }
    }
}

if (! function_exists('check_unique')) {
    function check_unique(
        string $table_name,
        string $key = 'shortcut',
        bool $required = false,
        int $max = 50,
        string $type = 'string',
        ?string $route_param = null,
    ): array {
        return [
            $key => [
                $required ? 'required' : 'nullable',
                $type,
                "max:{$max}",
                Rule::unique($table_name, $key)
                    ->ignore(request()
                            ->route($route_param ?? Str::singular($table_name)))
                    ->withoutTrashed(),
            ],
        ];
    }
}

if (! function_exists('check_exist_many')) {
    function check_exist_many(array $map, bool $required = true): array
    {
        return collect($map)
            ->mapWithKeys(fn($table_name, $key) => check_exist($key, $table_name, required: $required))
            ->all();
    }
}

function check_exist(
    string $key,
    string $table_name,
    string $column = 'id',
    bool $required = true,
    bool $numeric = true
): array {
    $rules = array_filter([
        $required ? 'required' : 'nullable',
        $numeric ? 'integer' : null,
        "exists:{$table_name},{$column}",
    ]);

    return [$key => implode('|', $rules)];
}
