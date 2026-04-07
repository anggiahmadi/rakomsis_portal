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
Route::post('password/email', [UserController::class, 'forgotPassword'])->name('password.email');
Route::get('reset-password/{token}', function (string $token, \Illuminate\Http\Request $request) {
    return view('auth.reset-password', [
        'request' => $request,
        'token' => $token,
    ]);
})->name('password.reset');
Route::post('reset-password', [UserController::class, 'resetPassword'])->name('password.store');

Route::get('register', function () {
    return view('auth.register');
})->name('register-page');

Route::post('google-login', [UserController::class, 'googleLogin'])->name('google.login');
Route::post('login', [UserController::class, 'login'])->name('login');
Route::post('register', [UserController::class, 'register'])->name('register');
Route::post('xendit-payment-callback', [PaymentController::class, 'xenditCallback'])->name('xendit.callback');

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

        // Start Free Trial Route - Available to all authenticated users
        Route::get('/free-trial', [DashboardController::class, 'freeTrial'])->name('dashboard.free-trial');
        Route::post('/start-trial', [DashboardController::class, 'startTrial'])->name('dashboard.trial.start');

        // Agent Registration Routes - Available to all authenticated users
        Route::get('/agent/create', [AgentController::class, 'create'])->name('agent.create');
        Route::post('/agent', [AgentController::class, 'store'])->name('agent.store');
    });

    // Admin Routes - Protected by employee middleware
    Route::middleware('employee')->group(function () {
        Route::get('admin', [AdminController::class, 'index'])->name('admin');

        // Product CRUD
        Route::resource('products', ProductController::class);
        Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{id}/permanent-delete', [ProductController::class, 'permanentDelete'])->name('products.permanent-delete');

        // Promotion CRUD
        Route::resource('promotions', PromotionController::class);
        Route::post('promotions/{id}/restore', [PromotionController::class, 'restore'])->name('promotions.restore');
        Route::delete('promotions/{id}/permanent-delete', [PromotionController::class, 'permanentDelete'])->name('promotions.permanent-delete');

        // Customer List
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::post('customers/{id}/restore', [CustomerController::class, 'restore'])->name('customers.restore');
        Route::delete('customers/{id}/permanent-delete', [CustomerController::class, 'permanentDelete'])->name('customers.permanent-delete');
    });

    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
    Route::put('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::get('/dashboard/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');
    Route::put('/dashboard/settings', [DashboardController::class, 'updateSettings'])->name('dashboard.settings.update');
    // Tenant List
    Route::get('tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::post('tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
    Route::post('tenants/{tenant}/invite-customer', [TenantController::class, 'inviteCustomer'])->name('tenants.invite-customer');
    Route::delete('tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');
    Route::post('tenants/{tenant}/restore', [TenantController::class, 'restore'])->name('tenants.restore');
    Route::delete('tenants/{tenant}/permanent-delete', [TenantController::class, 'permanentDelete'])->name('tenants.permanent-delete');

    // Subscription List
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::post('subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::put('subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::post('subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew'])->name('subscriptions.renew');

    // Withdrawal List
    Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('withdrawals/{withdrawal}/respond', [WithdrawalController::class, 'respond'])->name('withdrawals.respond');

    // Payment List
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    // Logout
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});
