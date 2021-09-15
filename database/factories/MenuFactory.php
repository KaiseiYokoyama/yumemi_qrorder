<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Menu::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $restaurant_ids = Restaurant::all()->pluck('id');
        return [
            'restaurant_id' => $this->faker->randomElement($restaurant_ids),
            'name' => $this->faker->name(),
            'price' => $this->faker->numberBetween(0, 1000),
            'image_url' => $this->faker->imageUrl()
        ];
    }
}
