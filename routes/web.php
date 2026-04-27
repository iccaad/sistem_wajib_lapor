<?php

use App\Http\Controllers\Admin\ParticipantController;
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
    Route::get('/dashboard', function () {
        return view('peserta.dashboard');
    })->name('dashboard');

    Route::post('/logout', [PesertaAuthController::class, 'logout'])->name('logout');
});

// -------------------------------------------------------
// Admin Protected Routes
// -------------------------------------------------------
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('participants', ParticipantController::class);
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
