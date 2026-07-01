<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomMGTController;
use App\Http\Controllers\SaleMGTController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\BatchController;


Route::get('/', function () {
    return redirect()->route('dashboard');
});
Route::get('/export/sales', [DashboardController::class, 'exportSales'])->name('export.sales');

Route::middleware(['auth'])->group(function () {
    // This is the missing piece that connects to your Controller
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Additional routes based on your Controller methods
    Route::resource('roomsmgt', RoomMGTController::class);
    Route::resource('salemgt', SaleMGTController::class);
    // Route::resource('taxmgt', TaxMgtController::class);
    Route::resource('lecturer', LecturerController::class);
    Route::resource('shift', ShiftController::class);
    Route::resource('faculty', FacultyController::class);
    Route::resource('major', MajorController::class);
    Route::resource('batch', BatchController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
