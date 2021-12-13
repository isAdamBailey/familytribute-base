<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObituaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'person_id' => Person::factory(),
            'birth_date' => $this->faker->date('Y-m-d', '-100 years'),
            'death_date' => $this->faker->date('Y-m-d', '-10 years'),
            'content' => $this->faker->paragraphs(3, true),
            'headstone_url' => $this->faker->imageUrl(),
        ];
    }
}
