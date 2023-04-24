<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('currency:fetch');

        $this->call(EssentialSeeder::class);

        if (!app()->environment('production')) {
            $this->call(UserSeeder::class);
            $this->call(ProductSeeder::class);
        }
    }
}
