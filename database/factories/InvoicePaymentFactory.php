<?php

namespace Database\Factories;

use App\Enums\PaymentApprovalStatus;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoicePayment>
 */
class InvoicePaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'tanggal_bayar' => fake()->date(),
            'jumlah_bayar' => fake()->numberBetween(500000, 10000000),
            'metode_bayar' => 'Transfer Bank',
            'status_approval' => PaymentApprovalStatus::Pending->value,
            'catatan' => fake()->sentence(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status_approval' => PaymentApprovalStatus::Approved->value,
            'approved_at' => now(),
        ]);
    }
}
