<?php

namespace Database\Factories;

use App\Enums\SatuanStatus;
use App\Models\Satuan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Satuan>
 */
class SatuanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'status' => SatuanStatus::Aktif->value,
        ];
    }
}
