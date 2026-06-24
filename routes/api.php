<?php

use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\Api\MajorController;
use App\Http\Controllers\Api\RoomMGTApiController;
use App\Http\Controllers\Api\SaleMgtApiController;
use App\Http\Controllers\Api\TaxMgtApiController;
use function App\Helpers\api_routes;
use Illuminate\Support\Facades\Route;

/**
 * Route Api for application
 */
Route::prefix('v1')->group(function () {
    api_routes([
        'faculties' => FacultyController::class,
        'majors'    => MajorController::class,
    ]);
});

// Route::name('api.')->group(function () {
//     Route::apiResource('rooms', \App\Http\Controllers\Api\RoomController::class);
// });

Route::name('api.')->group(function () {
    Route::apiResource('roomsmgt', RoomMGTApiController::class);
});

Route::name('api.')->group(function () {
    Route::apiResource('sales', SaleMgtApiController::class);
});

Route::name('api.')->group(function () {
    Route::apiResource('taxmgt', TaxMgtApiController::class);
});
Route::name('api.')->group(function () {
    Route::apiResource('dashboard', DashboardApiController::class);
});
