<?php
namespace App\Helpers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

if (! function_exists('make_fields')) {
    function make_fields(string $name, callable $fn, array $fields = []): void
    {
        Schema::create($name, fn(Blueprint $table) => fields($table, function () use ($table, $fn) {
            $fn($table);
        }, $fields));
    }
}

if (! function_exists('fields')) {
    function fields(Blueprint $table, callable $fn, array $fields = []): void
    {
        $table->id();
        if ($fields) {
            foreach ($fields as $field) {
                $table->string($field);
            }
        }
        $fn();

        $table->text('remark')->nullable();
        $table->timestamps();
        $table->softDeletes();
    }
}

if (! function_exists('to_list')) {
    function to_list(mixed $instance, array $fields = []): array
    {
        return [
            'id'         => $instance->id,
            ...$fields,
            'remark'     => $instance->remark,
            'created_at' => $instance->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $instance->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}

if (! function_exists('to_name')) {
    function to_name(mixed $instance): string
    {
        return "{$instance->name_kh} ({$instance->name_en})";
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
    function no_data(
        string $message,
        int $code = 400,
    ): JsonResponse {
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
            Route::post("{$slug}/{id}/restore", [$controller, 'restore'])
                ->name("{$slug}.restore");

            // Force Delete
            Route::delete("{$slug}/{id}/force", [$controller, 'force_destroy'])
                ->name("{$slug}.force-destroy");
        }
    }
}
