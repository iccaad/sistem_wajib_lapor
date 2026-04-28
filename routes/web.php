<?php

use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Peserta\AbsenceController;
use App\Http\Controllers\Peserta\DashboardController as PesertaDashboardController;
use App\Http\Controllers\Peserta\HistoryController;
use App\Http\Controllers\Peserta\PesertaAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Root redirect: sends users to the appropriate dashboard based on role.
| Two separate authentication flows:
|   Admin  → /admin/login  (email + password via Breeze)
|   Peserta → /login        (NIK only, no password)
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect('/admin/dashboard')
            : redirect('/peserta/dashboard');
    }
    return redirect('/login');
});

// -------------------------------------------------------
// Participant (Peserta) Authentication
// -------------------------------------------------------
Route::get('/login', [PesertaAuthController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('peserta.login');

Route::post('/login', [PesertaAuthController::class, 'login'])
    ->middleware('guest')
    ->name('peserta.login.submit');

// -------------------------------------------------------
// Participant (Peserta) Protected Routes
// -------------------------------------------------------
Route::middleware(['auth', 'peserta'])->prefix('peserta')->name('peserta.')->group(function () {
    Route::get('/dashboard', [PesertaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/absensi', [AbsenceController::class, 'show'])->name('absence');
    Route::post('/absensi', [AbsenceController::class, 'store'])->middleware('throttle:absensi')->name('absence.store');
    Route::get('/riwayat', [HistoryController::class, 'index'])->name('history');
    Route::post('/logout', [PesertaAuthController::class, 'logout'])->name('logout');
});

// -------------------------------------------------------
// Admin Protected Routes
// -------------------------------------------------------
Route::middleware(['auth', 'admin', 'log.activity'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Participants CRUD
    Route::resource('participants', ParticipantController::class);

    // Attendance override & photo access
    Route::post('participants/{participant}/attendance/override', [AdminAttendanceController::class, 'override'])
        ->name('attendance.override');
    Route::get('attendance/{log}/photo', [AdminAttendanceController::class, 'showPhoto'])
        ->name('attendance.photo');

    // Locations CRUD (no show, no destroy — use toggle instead)
    Route::resource('locations', LocationController::class)->except(['show', 'destroy']);
    Route::patch('locations/{location}/toggle', [LocationController::class, 'toggle'])
        ->name('locations.toggle');

    // Violation Types CRUD
    Route::resource('violation-types', \App\Http\Controllers\Admin\ViolationTypeController::class)->except(['show']);

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{participant}', [ReportController::class, 'show'])->name('reports.show');
});

// -------------------------------------------------------
// Profile routes (Breeze) - admin auth middleware
// -------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
