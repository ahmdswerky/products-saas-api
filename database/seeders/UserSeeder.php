<?php

namespace Database\Seeders;

use App\Enums\MerchantStatus;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\MerchantMeta;
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
        if (User::where('email', 'test@test.com')->exists()) {
            return;
        }

        $user = User::factory()->create([
            'public_id' => Str::random(20),
            'name' => 'Customer',
            'email' => 'test@test.com',
        ]);

        $customer = $user->customers()->byMethod('credit_card')->first();

        $customer->update([
            'reference_id' => 'cus_KrtkJRxU91uTPL',
        ]);

        $user->merchant->metas->map(function ($meta) {
            if ($meta->gateway->key === 'stripe') {
                $meta->update([
                    'reference_id' => 'acct_1K6coGJ4LbNpFgBw',
                    'status' => MerchantStatus::CONNECTED,
                ]);
            }

            if ($meta->gateway->key === 'paypal') {
                $meta->update([
                    'reference_id' => 'ESZG3AW3A453G',
                    'status' => MerchantStatus::CONNECTED,
                ]);
            }
        });

        //$users->map(function ($user) {
        //    $avatar = generate_letter_image($user->name[0]);

        //    $user->addMedia($avatar, 'photo', true, [
        //        'name' => 'avatar',
        //    ], true);

        //    $merchant = $user->merchant()->create();

        //    $avatar = 'https://cdn-icons-png.flaticon.com/512/4483/4483129.png';

        //    $merchant->addMedia($avatar, 'photo', true, [
        //        'name' => 'avatar',
        //    ], true);
        //});

        //$users->map(function ($user) {
        //    $customers = PaymentService::createCustomers([
        //        'name' => $user->name,
        //        'email' => $user->email,
        //    ]);

        //    collect($customers)->map(function ($customer) use ($user) {
        //        $user->customers()->create([
        //            'public_id' => Str::random(20),
        //            'reference_id' => $customer->id,
        //            'payment_gateway_id' => PaymentGateway::byKey($customer->gateway),
        //        ]);
        //    });
        //});
    }
}
