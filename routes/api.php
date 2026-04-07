<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('generate-xendit-invoice', [PaymentController::class, 'generateXenditInvoice']);
Route::post('xendit-payment-callback', [PaymentController::class, 'xenditCallback']);
