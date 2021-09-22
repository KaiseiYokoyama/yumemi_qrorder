<?php

namespace Database\Seeders;

use Faker\Provider\Uuid;
use Illuminate\Database\Seeder;
use App\Models\Party;

class PartySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Party::factory()
            ->count(3)
            ->create();

        // テスト用に店舗id=1に滞在する食べる人が必ず存在するようにする
        Party::create([
            'restaurant_id' => 1,
            'state' => 0,
            'uuid' => Uuid::uuid(),
        ]);
        // テスト用に店舗id=2に滞在する食べる人が必ず存在するようにする
        Party::create([
            'restaurant_id' => 2,
            'state' => 0,
            'uuid' => Uuid::uuid(),
        ]);
    }
}
