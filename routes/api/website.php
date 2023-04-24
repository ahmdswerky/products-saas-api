<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\PaymentController;
use App\Http\Controllers\Website\ProductController;
use App\Http\Controllers\Website\MerchantController;
use App\Http\Controllers\Website\IntegrationController;
use App\Http\Controllers\Website\IntegrationNameController;
use App\Http\Controllers\Website\CustomerCardController;
use App\Http\Controllers\Website\PaymentGatewayController;
use App\Http\Controllers\Website\MerchantBalanceController;
use App\Http\Controllers\Website\AttachPaymentMethodController;
use App\Http\Controllers\Website\GenerateMerchantLinkController;

Route::apiRoutes('products', ProductController::class)->middleware('auth:key');
//Route::get('products/{product}', function (Product $product) {
//    //Route::get('products/{product}', function ($product) {
//    return response([
//        'product' => Product::where('slug', $product)->first(),
//    ]);
//});
Route::apiRoutes('merchants', MerchantController::class)->middleware('auth:key');
// Route::apiRoutes('integration-names', IntegrationNameController::class)->only('index', 'show')->middleware('auth:key');
Route::apiRoutes('integrations', IntegrationController::class)->except('update', 'destroy')->middleware('auth:key');
Route::get('merchant/balance', MerchantBalanceController::class)->middleware('auth:key');
Route::post('customers/cards', CustomerCardController::class);
Route::post('customers/payment-method', AttachPaymentMethodController::class);
Route::apiRoutes('payments', PaymentController::class)->except('destroy')->middleware('auth:key');
Route::apiRoutes('payment-gateways', PaymentGatewayController::class)->only('index', 'show')->middleware('auth:key');
Route::post('merchant/link', GenerateMerchantLinkController::class)->middleware('auth:key');
