<?php

namespace App\Helpers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

if (! function_exists('fields')) {
    function fields(Blueprint $table, callable $fn, array $names = []): void
    {
        $table->id();
        if ($names) {
            foreach ($names as $name) {
                $table->string($name);
            }
        }
        $fn();

        $table->timestamps();
        $table->softDeletes();
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
