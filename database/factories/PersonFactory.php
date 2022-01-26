<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->unique()->firstName(),
            'last_name' => $this->faker->lastName(),
            'photo_url' => $this->faker->imageUrl(),
        ];
    }
}
