<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'avatar' => $this->faker->url(),
            'user_id' => $this->faker->randomElement([1,2,3,4,5]), 
            'class_types_id' => $this->faker->randomElement([1,2,3,4,5]), 
            'password' => '##########'
        ];
    }
}
