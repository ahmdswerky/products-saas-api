<?php

namespace Tests;

use App\Models\User;
use App\Models\Product;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\MerchantMeta;
use Database\Seeders\UserSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\EssentialSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EssentialSeeder::class);
        $this->seed(UserSeeder::class);

        //$this->artisan('currency:fetch');

        Currency::create([
            'name' => 'USD',
            'base' => 'USD',
            'date' => today()->format('Y-m-d'),
            'value' => 1,
        ]);

        //$this->seed(ProductSeeder::class);

        $product = Product::factory()->make();
        Merchant::first()->products()->create($product->toArray());

        $this->withHeaders([
            'api-key' => User::first()->apiKey,
        ]);
    }

    //public static function prepare()
    //{
    //    (new EssentialSeeder)->run();
    //    (new UserSeeder)->run();
    //}

    //public static function cleanup()
    //{
    //    User::get()->each->forceDelete();
    //    Customer::get()->each->forceDelete();
    //    Merchant::get()->each->forceDelete();
    //    MerchantMeta::get()->each->forceDelete();
    //}
}
