<?php

namespace Database\Factories;

use App\Enums\KlasterStatus;
use App\Models\Klaster;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Klaster>
 */
class KlasterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'KLS-'.fake()->unique()->numberBetween(1, 99999),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'status' => KlasterStatus::Aktif->value,
        ];
    }
}
