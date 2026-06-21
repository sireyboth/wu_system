<?php

use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\RoomMGTApiController;
use App\Http\Controllers\Api\SaleMgtApiController;
use App\Http\Controllers\Api\TaxMgtApiController;

use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::apiResource('rooms', \App\Http\Controllers\Api\RoomController::class);
});

Route::name('api.')->group(function () {
    Route::apiResource('roomsmgt', \App\Http\Controllers\Api\RoomMGTApiController::class);
});

Route::name('api.')->group(function () {
    Route::apiResource('sales', \App\Http\Controllers\Api\SaleMgtApiController::class);
});

Route::name('api.')->group(function () {
    Route::apiResource('taxmgt', \App\Http\Controllers\Api\TaxMgtApiController::class);
});
Route::name('api.')->group(function () {
    Route::apiResource('dashboard', \App\Http\Controllers\Api\DashboardApiController::class);
});
