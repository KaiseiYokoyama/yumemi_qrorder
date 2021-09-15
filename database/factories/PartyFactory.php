<?php

namespace Database\Factories;

use App\Models\Party;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Party::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'restaurant_id' => $this->faker->numberBetween(1,3),
            'state' => 0,
            'uuid' => $this->faker->unique()->uuid(),
        ];
    }
}
