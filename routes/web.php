<?php

use App\Http\Controllers\Admin\StudentSnapshotController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentStatusController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Additional routes based on your Controller methods
    Route::resource('lecturer', LecturerController::class);
    Route::resource('shift', ShiftController::class);
    Route::resource('faculty', FacultyController::class);
    Route::resource('major', MajorController::class);
    Route::resource('batch', BatchController::class);
    Route::resource('group', GroupController::class);
    Route::resource('campus', CampusController::class);

    Route::resource('student', StudentController::class);
    Route::get('/students/export', [StudentController::class, 'export_data'])->name('students.export');
    Route::post('/students/import', [StudentController::class, 'import_data'])->name('students.import');

    Route::resource('StudentStatusCertificate', StudentStatusController::class);
    Route::resource('app-status', StatusController::class);

    Route::get('/student-snapshot', StudentSnapshotController::class)->name('student.snapshot');

    ///For user profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
