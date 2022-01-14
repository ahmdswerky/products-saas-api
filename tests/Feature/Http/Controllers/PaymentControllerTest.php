<?php

use App\Models\Payment;
use App\Models\Product;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use App\Http\Controllers\Website\PaymentController;

it('validates required fields on the payment form', function () {
    post(action([PaymentController::class, 'store']), [])->assertJsonValidationErrors([
        'product_id',
        'method',
    ])->assertStatus(422);
});

it('validates an existing product id', function () {
    post(action([PaymentController::class, 'store']), [
        'method' => 'credit_card',
        'product_id' => 100000000, // doesn't exists
    ])->assertJsonValidationErrors([
        'product_id',
    ])->assertStatus(422);
});

it('can process credit card payments', function () {
    $product = Product::first();
    $expectedResponse = [
        'data' => [
            [
                'id' => 1,
                'amount' => $product->price,
                'status' => 'pending',
            ],
        ],
    ];

    expect(Product::get())->toHaveCount(1);

    post(action([PaymentController::class, 'store']), [
        'product_id' => $product->public_id,
        'method' => 'credit_card',
    ])->assertStatus(201);

    get(action([PaymentController::class, 'index'], [
        'product' => $product->id,
    ]))->assertJson($expectedResponse)
    ->assertStatus(200);

    expect(Payment::get())->toHaveCount(1);
});

it('can process paypal payments', function () {
    $product = Product::first();
    $expectedResponse = [
        'data' => [
            [
                'id' => 1,
                'amount' => $product->price,
                'status' => 'pending',
            ],
        ],
    ];

    post(action([PaymentController::class, 'store']), [
        'product_id' => $product->public_id,
        'method' => 'paypal',
    ])->assertStatus(201);

    get(action([PaymentController::class, 'index'], [
        'product' => $product->public_id,
    ]))->assertJson($expectedResponse)
    ->assertStatus(200);

    expect(Payment::get())->toHaveCount(1);
});
