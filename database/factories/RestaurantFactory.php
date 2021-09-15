<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class RestaurantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Restaurant::class;

    /**
     * ユーザ（お店）の課金が切れていることを示す
     * 
     * @return RestaurantFactory
     */
    public function expired()
    {
        return $this->state(function (array $attributes) {
            return [
                'expired_date' => $this->faker->date('2000-1-1'),
            ];
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'expired_date' => $this->faker->date('2121-9-15'),
            'pass_hash' => Hash::make('yumemi2021'),
        ];
    }
}
