<?php
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
    // This is the missing piece that connects to your Controller
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Additional routes based on your Controller methods
    Route::resource('lecturer', LecturerController::class)->only('index');
    Route::resource('shift', ShiftController::class)->only('index');
    Route::resource('faculty', FacultyController::class)->only('index');
    Route::resource('major', MajorController::class)->only('index');
    Route::resource('batch', BatchController::class)->only('index');
    Route::resource('group', GroupController::class)->only('index');
    Route::resource('campus', CampusController::class)->only('index');

    Route::resource('student', StudentController::class)->only('index');
    Route::resource('StudentStatusCertificate', StudentStatusController::class)->only('index');
    Route::resource('app-status', StatusController::class)->only('index');

    Route::resource('profile', ProfileController::class)->only(['edit', 'update', 'destroy']);
});

require __DIR__ . '/auth.php';
