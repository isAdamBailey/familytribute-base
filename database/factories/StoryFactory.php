<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->words(4, true),
            'excerpt' => $this->faker->sentences(3, true),
            'content' => $this->faker->paragraphs(3, true),
            'year' => $this->faker->numberBetween(1900, 2000),
            'private' => false,
        ];
    }
}
