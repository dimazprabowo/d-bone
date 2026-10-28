<?php

namespace Database\Factories;

use App\Enums\PengadaanApprovalStatus;
use App\Enums\TipeBiaya;
use App\Models\BisnisArea;
use App\Models\Pengadaan;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pengadaan>
 */
class PengadaanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no_pesanan_spk' => 'SPK-'.fake()->unique()->numberBetween(1, 99999),
            'nama_pemohon' => fake()->name(),
            'tipe_biaya' => TipeBiaya::FixCost->value,
            'nama_project' => null,
            'no_wbs' => null,
            'vendor_id' => Vendor::factory(),
            'bisnis_area_id' => BisnisArea::factory(),
            'cabang_id' => null,
            'tanggal_pesanan_spk' => fake()->date(),
            'tanggal_jatuh_tempo' => fake()->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
            'total_biaya' => 0,
            'status_approval' => PengadaanApprovalStatus::Pending->value,
            'catatan' => fake()->sentence(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status_approval' => PengadaanApprovalStatus::Approved->value,
            'approved_at' => now(),
        ]);
    }
}
