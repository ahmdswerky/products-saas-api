<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    protected $gateways = [
        'stripe' => ['credit_card'],
        'paypal' => ['paypal'],
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect($this->gateways)->keys()->map(function ($key) {
            $gateway = PaymentGateway::firstOrCreate(['key' => $key], [
                'name' => $key,
            ]);

            collect($this->gateways[$key])->map(function ($method) use ($gateway) {
                $gateway->methods()->firstOrCreate(['key' => $method], [
                    'name' => $method,
                ]);
            });
        });
    }
}
