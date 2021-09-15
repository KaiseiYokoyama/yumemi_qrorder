<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 課金の切れていない（利用可能な）店舗1件をDBに追加
        Restaurant::factory()
            ->create();
        // 課金の切れた（利用不可能な）店舗1件をDBに追加
        Restaurant::factory()
            ->expired()
            ->create();
    }
}
