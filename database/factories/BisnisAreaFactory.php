<?php

namespace Database\Factories;

use App\Enums\BisnisAreaStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class BisnisAreaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'status' => BisnisAreaStatus::Aktif->value,
        ];
    }
}
