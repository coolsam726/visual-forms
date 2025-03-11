<?php

namespace Coolsam\VisualForms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VisualFormFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'is_active' => $this->faker->boolean,
        ];
    }
}
