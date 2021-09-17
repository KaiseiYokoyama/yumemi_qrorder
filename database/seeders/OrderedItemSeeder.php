<?php

namespace Database\Seeders;

use App\Models\OrderedItem;
use Illuminate\Database\Seeder;

class OrderedItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrderedItem::factory()
            ->count(50)
            ->create();
    }
}
