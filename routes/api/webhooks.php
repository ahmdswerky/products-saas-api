<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhooks\PayPalWebhooksController;
use App\Http\Controllers\Webhooks\StripeWebhooksController;

Route::post('stripe', StripeWebhooksController::class);
Route::post('paypal', PayPalWebhooksController::class);
