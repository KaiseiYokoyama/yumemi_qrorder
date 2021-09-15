<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * お勧めコマンド: 'sail artisan migrate:refresh --seed'
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RestaurantSeeder::class,
            PartySeeder::class,
            MenuSeeder::class,
        ]);
    }
}
