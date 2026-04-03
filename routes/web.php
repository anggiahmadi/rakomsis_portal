<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawalController;
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

    // Agent Routes - Protected by agent middleware
    Route::middleware('agent')->group(function () {
        Route::get('/dashboard/agent', [AgentController::class, 'index'])->name('dashboard.agent');
        Route::post('/dashboard/agent/withdrawal', [AgentController::class, 'requestWithdrawal'])->name('agent.withdrawal.request');
    });

    // Customer Routes - Protected by agent middleware
    Route::middleware('customer')->group(function () {
        // Dashboard Routes - Available to all authenticated users
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
        Route::put('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
        Route::get('/dashboard/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');
        Route::put('/dashboard/settings', [DashboardController::class, 'updateSettings'])->name('dashboard.settings.update');

        // Agent Registration Routes - Available to all authenticated users
        Route::get('/agent/create', [AgentController::class, 'create'])->name('agent.create');
        Route::post('/agent', [AgentController::class, 'store'])->name('agent.store');
    });

    // Admin Routes - Protected by employee middleware
    Route::middleware('employee')->group(function () {
        Route::get('admin', [AdminController::class, 'index'])->name('admin');

        // Product CRUD
        Route::resource('products', ProductController::class);
        Route::get('products/{product}/check-usage', [ProductController::class, 'checkUsage'])->name('products.check-usage');

        // Promotion CRUD
        Route::resource('promotions', PromotionController::class)->except(['show']);

        // Customer List
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');

        // Tenant List
        Route::get('tenants', [TenantController::class, 'index'])->name('tenants.index');

        // Agent List
        Route::get('agents', [AgentController::class, 'index'])->name('agents.index');

        // Subscription List
        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');

        // Withdrawal List
        Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('withdrawals/{withdrawal}/respond', [WithdrawalController::class, 'respond'])->name('withdrawals.respond');

        // Payment List
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    });

    // Logout
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});
