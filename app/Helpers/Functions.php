<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

if (! function_exists('make_fields')) {
    function make_fields(string $table_name,
        ? callable $fn = null,
        bool $is_common = true,
    ) : void {
        Schema::create($table_name, function (Blueprint $table) use ($fn, $is_common) {
            fields($table, fn() => $fn($table), $is_common);
        });
    }
}

if (! function_exists('fields')) {
    function fields(Blueprint $table,
        ? callable $fn = null,
        bool $is_common = false,
    ) : void {
        $table->id();
        if ($is_common) {
            $table->string('name_en', 100);
            $table->string('name_kh', 100);
            // $table->string('name', 255)->nullable();
        }

        $fn($table);
        $table->text('remark')->nullable();
        $table->timestamps();
        $table->softDeletes();

    }
}

if (! function_exists('to_list')) {
    function to_list(object $self, array $fields = [], bool $is_common = true, $is_extra = true): array
    {
        $data = array_merge(['id' => $self->id], $fields);
        if ($is_common) {
            $data['name']    = $self->name;
            $data['name_kh'] = $self->name_kh;
            $data['name_en'] = $self->name_en;
        }

        if ($is_extra) {
            $data['remark']     = $self->remark ?? null;
            $data['created_at'] = $self->created_at?->format('Y-m-d H:i:s');
            $data['updated_at'] = $self->updated_at?->format('Y-m-d H:i:s');
        }

        return $data;
    }
}

if (! function_exists('to_name')) {
    function to_name(object $object): string
    {
        return "{$object->name_kh} ({$object->name_en})";
    }
}

if (! function_exists('dated_format')) {
    function dated_format(object $object): string
    {
        return "{$object->name_kh} ({$object->name_en})";
    }
}

if (! function_exists('handle')) {
    function handle(callable $fn, $message = 'Success', $code = 200)
    {
        try {
            return has_data($fn(), $message, $code);
        } catch (\Exception $err) {
            Log::error($err);
            return no_data($err->getMessage(), $err->getCode() ?: 500);
        }
    }
}

if (! function_exists('execute')) {
    function execute(callable $fn)
    {
        try {
            return DB::transaction($fn);
        } catch (\Throwable $err) {
            Log::error($err);
            throw $err;
            // return no_data($err->getMessage(), $err->getCode() ?: 500);
        }
    }
}

if (! function_exists('paginated')) {
    function paginated(LengthAwarePaginator $paginator, string $resourceClass)
    {
        return response()->json($resourceClass::collection($paginator)->response()->getData(true));
    }
}

if (! function_exists('has_data')) {
    function has_data(mixed $data = null, string $message = 'OK', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ], $code);
    }
}

if (! function_exists('no_data')) {
    function no_data(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}

/**
 * Register API resources with soft delete routes (restore + force delete).
 *
 * $modules optionally maps a resource slug to its permission-catalog module
 * (e.g. 'statuses' => 'app-status') so reads require "{module}.view",
 * creates "{module}.create", and every mutation (update/trash/restore/
 * destroy/clear) requires "{module}.edit" or "{module}.delete" as
 * appropriate. A slug left out of $modules stays ungated (e.g. 'alerts',
 * which every logged-in user can manage regardless of role).
 */
if (! function_exists('api_routes')) {
    function api_routes(array $resources, array $modules = []): void
    {
        foreach ($resources as $slug => $controller) {
            $module    = $modules[$slug] ?? null;
            $parameter = str_replace('-', '_', Str::singular($slug));

            $gate = fn (?string $ability) => $module && $ability ? ["permission:{$module}.{$ability}"] : [];

            Route::middleware($gate('view'))->group(function () use ($slug, $parameter, $controller) {
                Route::get($slug, [$controller, 'index'])->name("{$slug}.index");
                Route::get("{$slug}/{{$parameter}}", [$controller, 'show'])->name("{$slug}.show");
            });

            Route::middleware($gate('create'))
                ->post($slug, [$controller, 'store'])->name("{$slug}.store");

            Route::middleware($gate('edit'))->group(function () use ($slug, $parameter, $controller) {
                Route::match(['put', 'patch'], "{$slug}/{{$parameter}}", [$controller, 'update'])->name("{$slug}.update");
                Route::patch("{$slug}/{{$parameter}}/trash", [$controller, 'trash'])->withTrashed()->name("{$slug}.trash");
                Route::patch("{$slug}/{{$parameter}}/restore", [$controller, 'restore'])->withTrashed()->name("{$slug}.restore");
            });

            Route::middleware($gate('delete'))->group(function () use ($slug, $parameter, $controller) {
                Route::delete("{$slug}/{{$parameter}}", [$controller, 'destroy'])->name("{$slug}.destroy");
                Route::delete("{$slug}/{{$parameter}}/clear", [$controller, 'force_destroy'])->name("{$slug}.remove");
            });
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

if (! function_exists('set_data')) {
    function set_data(string $filename, array $keys = [], bool $is_common = true): void
    {
        $records = get_data($filename);
        $now     = now();
        $data    = array_map(function ($row) use ($now, $keys, $is_common) {
            $item = $is_common
                ? [
                'id'      => $row['id'] ?? null,
                'name_en' => $row['name_en'] ?? null,
                'name_kh' => $row['name_kh'] ?? null,
                // 'name'    => trim(($row['name_kh'] ?? '') . ' (' . ($row['name_en'] ?? '') . ')', ' ()'),
            ]
                : [];

            foreach ($keys as $key) {
                $item[$key] = $row[$key] ?? null;
            }

            $item['created_at'] = $now;
            $item['updated_at'] = $now;

            return $item;
        }, $records);

        foreach (array_chunk($data, 500) as $chunk) {
            DB::table($filename)->insertOrIgnore($chunk);
        }
    }
}

if (! function_exists('get_data')) {
    function get_data(string $filename): array
    {
        $path = database_path("data/{$filename}.json");

        if (! file_exists($path)) {
            throw new \RuntimeException("Seed file not found: {$filename}.json");
        }

        $rows = json_decode(file_get_contents($path), true);

        if (! is_array($rows)) {
            throw new \RuntimeException("Invalid JSON in seed file: {$filename}.json");
        }

        return $rows;
    }
}

if (! function_exists('set_records')) {
    function set_records(string $filename, callable $fn, bool $single = true): void
    {
        $records = get_data($filename);
        foreach ($records as $record) {
            $now = now();
            $single ? $fn($record, $now) : execute(fn() => $fn($record, $now));
        }
    }
}

if (! function_exists('initials')) {
    function initials(string $name, int $limit = 2): string
    {
        $words = explode(' ', trim($name));

        $initials = collect($words)
            ->filter() // remove empty strings from extra spaces
            ->map(fn($word) => strtoupper(mb_substr($word, 0, 1)))
            ->take($limit)
            ->implode('');

        return $initials;
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

if (! function_exists('check_exist_many')) {
    function check_exist_many(array $map, bool $required = true): array
    {
        return collect($map)
            ->mapWithKeys(fn($table, $name) => check_exist($name, $table, required: $required))
            ->all();
    }
}

if (! function_exists('grouped')) {
    function grouped(mixed $data, array $keys = []): array
    {
        return collect($keys)->map(fn($key) => data_get($data, $key, ''))->all();
    }
}

if (! function_exists('imploded')) {
    function imploded(mixed $data, ?string $key = null, string $symbol = '|'): string
    {
        $items = collect($data)
            ->map(fn($item) => $key ? data_get($item, $key) : $item)
            ->filter(fn($item) => filled($item))
            ->all();

        return implode(" {$symbol} ", $items);
    }
}

if (! function_exists('split_full_name')) {
    /**
     * Cambodian convention: first word = family/last name,
     * everything after it = given/first name.
     */
    function split_full_name(?string $full_name): array
    {
        $name = trim((string) $full_name);
        if ($name === '') {
            return ['first_name' => null, 'last_name' => null];
        }

        // limit=2 so "last_name" swallows every remaining word,
        // not just the second one
        $parts = preg_split('/\s+/u', $name, 2);

        return [
            'last_name'  => $parts[0] ?? null,
            'first_name' => $parts[1] ?? null,
        ];
    }
}

if (! function_exists('join_full_name')) {
    function join_full_name(?string $first_name, ?string $last_name): ?string
    {
        $joined = collect([$last_name, $first_name])->filter()->implode(' ');

        return $joined === '' ? null : $joined;
    }
}
