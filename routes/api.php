<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\CampusController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\ExamStateController;
use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\LecturerController;
use App\Http\Controllers\Api\MajorController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Route Api for application
 */
Route::prefix('v1')->middleware('auth')->group(function () {
    // Custom API routes for specific controllers
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/preview-number', [CertificateController::class, 'preview'])->name('preview');
        Route::get('/report', [CertificateController::class, 'report'])->name('report');
    });

    Route::prefix('exam-states')->name('exam-states.')->group(function () {
        Route::get('/report', [ExamStateController::class, 'report'])->name('report');
        Route::delete('/bulk', [ExamStateController::class, 'bulkDestroy'])->name('bulk-destroy');
    });

    Route::prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/dashboard', [AlertController::class, 'dashboard'])->name('dashboard');
        Route::delete('/bulk', [AlertController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::post('/{alert}/complete', [AlertController::class, 'complete'])->name('complete');
        Route::post('/{alert}/snooze', [AlertController::class, 'snooze'])->name('snooze');
        Route::get('/{alert}/logs', [AlertController::class, 'logs'])->name('logs');
    });

    Route::middleware('permission:role.view')->prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/permission-catalog', [RoleController::class, 'permissionCatalog'])->name('permission-catalog');
    });
    Route::middleware('permission:role.create')->post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::middleware('permission:role.edit')->put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::middleware('permission:role.delete')->delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::middleware('permission:role.edit')->put('/users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update');

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
        'alerts'       => AlertController::class,
        'users'        => UserController::class,
    ], [
        'faculties'    => 'faculty',
        'majors'       => 'major',
        'shifts'       => 'shift',
        'campuses'     => 'campus',
        'lecturers'    => 'lecturer',
        'subjects'     => 'subject',
        'batches'      => 'batch',
        'groups'       => 'group',
        'students'     => 'student',
        'statuses'     => 'app-status',
        'certificates' => 'certificate',
        'exam-states'  => 'state-exam',
        // 'alerts' intentionally ungated — every logged-in user manages alerts.
        'users'        => 'role',
    ]);

    // Address API routes
    Route::get('/provinces', [AddressController::class, 'provinces'])->name('provinces.all');
    Route::get('/nationalities', [AddressController::class, 'nationalities'])->name('nationalities.all');

    // Using Model Binding
    Route::get('/districts/{province}', [AddressController::class, 'districts'])->name('districts.by-province');
    Route::get('/communes/{district}', [AddressController::class, 'communes'])->name('communes.by-district');
    Route::get('/villages/{commune}', [AddressController::class, 'villages'])->name('villages.by-commune');
});
