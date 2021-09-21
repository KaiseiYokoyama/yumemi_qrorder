<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\OrderedItem;
use App\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderedItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderedItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $partyIds = Party::all()->pluck('id');
        $menuIds = Menu::all()->pluck('id');

        return [
            'menu_id' => $this->faker->randomElement($menuIds),
            'party_id' => $this->faker->randomElement($partyIds),
        ];
    }
}
