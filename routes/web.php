<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('login', function () {
    return view('auth.login');
})->name('login.page');

Route::get('forgot_password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::get('register', function () {
    return view('auth.register');
})->name('register-page');

Route::post('google-login', [UserController::class, 'googleLogin'])->name('google.login');
Route::post('login', [UserController::class, 'login'])->name('login');
Route::post('register', [UserController::class, 'register'])->name('register');

Route::middleware('auth')->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
    Route::put('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::get('/dashboard/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');
    Route::put('/dashboard/settings', [DashboardController::class, 'updateSettings'])->name('dashboard.settings.update');

    // Logout
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function() {

    });

    Route::middleware('agent')->group(function() {

    });
});
