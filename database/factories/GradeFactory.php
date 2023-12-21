<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
           'name' => $this->faker
           ->randomElement(['grade 1', 'grade 2', 'grade 3', 'grade 4', 'grade 5' ]) 
        ];
    }
}
