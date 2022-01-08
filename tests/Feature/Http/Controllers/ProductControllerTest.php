<?php

use App\Models\Product;

use function Pest\Laravel\get;
use function Pest\Laravel\post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Http\Controllers\Website\ProductController;

it('validates required fields on the product form', function () {
    post(action([ProductController::class, 'store']), [])->assertJsonValidationErrors([
        'title',
        'price',
        'photo',
    ])->assertStatus(422);
});

it('can create a new product', function () {
    Storage::fake('public');

    $initial = Product::count();

    $data = [
        'title' => 'First Product',
        'description' => 'lorem ipsum dolor',
        'price' => 100,
        'category' => 'Skincare',
        'photo' => UploadedFile::fake()->image('test.jpg'),
    ];

    post(action([ProductController::class, 'store']), $data)
        ->assertStatus(201)
        ->assertJson(function (AssertableJson $json) use ($data) {
            $json->has('product', function (AssertableJson $json) use ($data) {
                $json->has('id')
                    ->has('price')
                    ->has('usd_price')
                    ->whereAllType([
                        'id' => 'string',
                        'usd_price' => 'integer',
                        'price' => 'integer',
                        'title' => 'string',
                        'description' => 'string',
                        'photo.path' => 'string',
                    ])
                    ->whereAll(
                        collect($data)
                            ->filter(fn ($value, $key) => !in_array($key, ['photo']))
                            ->toArray(),
                    )
                    ->etc();
            });
        });

    expect(Product::get())->toHaveCount($initial + 1);
});
