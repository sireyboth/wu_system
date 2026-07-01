<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

if (! function_exists('make_fields')) {
    function make_fields(string $name, callable $fn, bool $named = true, bool $increment = true): void
    {
        Schema::create($name, fn(Blueprint $table) => fields($table, function () use ($table, $fn) {
            $fn($table);
        }, $named, $increment));
    }
}

if (! function_exists('fields')) {
    function fields(Blueprint $table, callable $fn, bool $named = false, bool $increment = false): void
    {
        $increment ? $table->id() : $table->unsignedInteger('id')->primary();
        $fn();

        if ($named) {
            $table->string('name_kh', 100);
            $table->string('name_en', 100);
            $table->string('name', 255)->nullable();
        }

        if ($increment) {
            $table->text('remark')->nullable();
            $table->timestamps();
            $table->softDeletes();
        } else {
            $table->timestamps();
        }

    }
}

if (! function_exists('to_list')) {
    function to_list(mixed $object, array $fields = [], bool $named = true): array
    {
        $data = [
            'id' => $object->id,
            ...$fields,
        ];

        if ($named) {
            $data['name']    = $object->name;
            $data['name_kh'] = $object->name_kh;
            $data['name_en'] = $object->name_en;
        }

        $data['remark']     = $object->remark;
        $data['created_at'] = $object->created_at?->format('Y-m-d H:i:s');
        $data['updated_at'] = $object->updated_at?->format('Y-m-d H:i:s');

        return $data;
    }
}

if (! function_exists('to_name')) {
    function to_name(mixed $object): string
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
            return no_data($err->getMessage(), $err->getCode() ?: 500);
        }
    }
}

if (! function_exists('execute')) {
    function execute(callable $fn)
    {
        try {
            return DB::transaction($fn);
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }
}

if (! function_exists('paginated')) {
    function paginated(
        LengthAwarePaginator $paginator,
        string $resourceClass
    ): JsonResponse {
        return response()->json(
            $resourceClass::collection($paginator)
                ->response()->getData(true)
        );
    }
}

if (! function_exists('has_data')) {
    function has_data(
        mixed $data = null,
        string $message = 'OK',
        int $code = 200
    ): JsonResponse {
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
 * Register API resources with soft delete routes (restore + force delete)
 */
if (! function_exists('api_routes')) {
    function api_routes(array $resources): void
    {
        Route::apiResources($resources);

        foreach ($resources as $slug => $controller) {
            // Restore
            Route::post("/{$slug}/{id}/restore", [$controller, 'restore'])
                ->name("{$slug}.restore");

            // Force Delete
            Route::delete("/{$slug}/{id}/clear", [$controller, 'force_destroy'])
                ->name("{$slug}.force-destroy");
        }
    }
}

if (! function_exists('web_routes')) {
    function web_routes(array $actions, callable $fn): void
    {
        foreach ($actions as $slug => $controller) {
            Route::get("/{$slug}", [$controller, $slug])->name($slug);
            $fn();
        }
    }
}

if (! function_exists('set_data')) {
    function set_data(string $name, array $fields = []): void
    {
        $path = database_path("/data/{$name}.json");
        $rows = json_decode(file_get_contents($path), true);

        $now  = Carbon::now();
        $data = array_map(function ($row) use ($now, $fields) {
            $name_en = $row['name_en'];
            $name_kh = $row['name_kh'];

            $item = [
                'id'      => $row['id'],
                'name_en' => $name_en,
                'name_kh' => $name_kh,
                'name'    => "{$name_en} ({$name_kh})",
                'created_at' => $now,
                'updated_at' => $now,
            ];

            foreach ($fields as $field) {
                $item[$field] = $row[$field] ?? null;
            }

            return $item;
        }, $rows);

        foreach (array_chunk($data, 500) as $chunk) {
            DB::table($name)->insertOrIgnore($chunk);
        }
    }
}
