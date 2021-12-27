<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\PaymentController;
use App\Http\Controllers\Website\ProductController;
use App\Http\Controllers\Website\MerchantController;
use App\Http\Controllers\Website\CustomerCardController;
use App\Http\Controllers\Website\PaymentGatewayController;
use App\Http\Controllers\Website\AttachPaymentMethodController;
use App\Http\Controllers\Website\GenerateMerchantLinkController;
use App\Http\Controllers\Website\MerchantBalanceController;

Route::apiResource('products', ProductController::class)->middleware('auth:key');
Route::apiResource('merchants', MerchantController::class)->middleware('auth:key');
Route::get('merchant/balance', MerchantBalanceController::class)->middleware('auth:key');
Route::post('customers/cards', CustomerCardController::class);
Route::post('customers/payment-method', AttachPaymentMethodController::class);
Route::apiResource('payments', PaymentController::class)->except('destroy')->middleware('auth:key');
Route::apiResource('payment-gateways', PaymentGatewayController::class)->only('index', 'show')->middleware('auth:key');
Route::post('merchant/link', GenerateMerchantLinkController::class)->middleware('auth:key');
