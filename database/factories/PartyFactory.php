<?php

namespace Database\Factories;

use App\Models\Party;
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
            'restaurant_id' => 0,
            'state' => 0,
            'uuid' => $this->faker->unique()->uuid(),
        ];
    }
}
