<?php

use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\CampusController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\LecturerController;
use App\Http\Controllers\Api\MajorController;
use App\Http\Controllers\Api\RoomMGTApiController;
use App\Http\Controllers\Api\SaleMgtApiController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TaxMgtApiController;
use Illuminate\Support\Facades\Route;

/**
 * Route Api for application
 */
Route::prefix('v1')->group(function () {
    api_routes([
        'faculties' => FacultyController::class,
        'majors'    => MajorController::class,
        'shifts'    => ShiftController::class,
        'campuses'  => CampusController::class,
        'lecturers' => LecturerController::class,
        'subjects'  => SubjectController::class,
        'batches'   => BatchController::class,
        'groups'    => GroupController::class,
        'students'  => StudentController::class,
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
