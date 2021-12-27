<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(EssentialSeeder::class);

        if (!app()->environment('production')) {
            $this->call(UserSeeder::class);
            $this->call(ProductSeeder::class);
        }
    }
}