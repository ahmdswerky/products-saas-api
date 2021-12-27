<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;
use App\Services\PaymentService;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory()->count(1)->create([
            'public_id' => Str::random(20),
            'name' => 'Customer',
            'email' => 'ahmdswerky@gmail.com',
        ]);

        $users->map(function ($user) {
            $avatar = generate_letter_image($user->name[0]);

            $user->addMedia($avatar, 'photo', true, [
                'name' => 'avatar',
            ], true);

            $user->merchant()->create();

            $merchant = $user->merchants()->create([
                'public_id' => Str::random(20),
                'api_key' => Str::random(15),
                'api_secret' => Str::random(30),
                'title' => 'The Store',
                'payment_gateway_id' => PaymentGateway::byKey('stripe'),
            ]);

            $avatar = 'https://cdn-icons-png.flaticon.com/512/4483/4483129.png';

            $merchant->addMedia($avatar, 'photo', true, [
                'name' => 'avatar',
            ], true);
        });

        $users->map(function ($user) {
            $customers = PaymentService::createCustomers([
                'name' => $user->name,
                'email' => $user->email,
            ]);

            collect($customers)->map(function ($customer) use ($user) {
                $user->customers()->create([
                    'public_id' => Str::random(20),
                    'reference_id' => $customer->id,
                    'payment_gateway_id' => PaymentGateway::byKey($customer->gateway),
                ]);
            });
        });
    }
}
