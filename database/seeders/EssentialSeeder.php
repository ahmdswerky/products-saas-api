<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MerchantMeta;
use Illuminate\Database\Seeder;

class EssentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PaymentMethodSeeder::class);
        $this->call(IntegrationNameSeeder::class);
    }
}
