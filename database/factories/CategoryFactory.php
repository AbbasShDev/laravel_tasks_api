<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'     => User::all()->random()->id,
            'title'       => $this->faker->sentence(1),
            'description' => $this->faker->sentence(),
        ];
    }
}
