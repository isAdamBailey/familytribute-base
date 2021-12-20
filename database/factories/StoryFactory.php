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
            'title' => $this->faker->words(2, true),
            'excerpt' => $this->faker->sentences(3, true),
            'content' => $this->faker->paragraphs(3, true),
        ];
    }
}
