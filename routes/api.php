<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\CampusController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\ExamStateController;
use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\LecturerController;
use App\Http\Controllers\Api\MajorController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Route Api for application
 */
Route::prefix('v1')->group(function () {
    // Custom API routes for specific controllers
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/preview-number', [CertificateController::class, 'preview'])->name('preview');
        Route::get('/report', [CertificateController::class, 'report'])->name('report');
    });

    Route::prefix('exam-states')->name('exam-states.')->group(function () {
        Route::get('/report', [ExamStateController::class, 'report'])->name('report');
        Route::delete('/bulk', [ExamStateController::class, 'bulkDestroy'])->name('bulk-destroy');
    });

    // Register API resource routes for various controllers
    api_routes([
        'faculties'    => FacultyController::class,
        'majors'       => MajorController::class,
        'shifts'       => ShiftController::class,
        'campuses'     => CampusController::class,
        'lecturers'    => LecturerController::class,
        'subjects'     => SubjectController::class,
        'batches'      => BatchController::class,
        'groups'       => GroupController::class,
        'students'     => StudentController::class,
        'statuses'     => StatusController::class,
        'certificates' => CertificateController::class,
        'exam-states'  => ExamStateController::class,
        'users'        => UserController::class,
    ]);

    // Address API routes
    Route::get('/provinces', [AddressController::class, 'provinces'])->name('provinces.all');
    Route::get('/nationalities', [AddressController::class, 'nationalities'])->name('nationalities.all');

    // Using Model Binding
    Route::get('/districts/{province}', [AddressController::class, 'districts'])->name('districts.by-province');
    Route::get('/communes/{district}', [AddressController::class, 'communes'])->name('communes.by-district');
    Route::get('/villages/{commune}', [AddressController::class, 'villages'])->name('villages.by-commune');
});
