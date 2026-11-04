<?php

namespace Database\Seeders;

use App\Enums\TipeBiaya;
use App\Models\ApprovalFlow;
use App\Models\BisnisArea;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApprovalFlowSeeder extends Seeder
{
    public function run(): void
    {
        $mk = User::where('email', 'manager.keuangan@app.com')->first();
        $smDukbis = User::where('email', 'sm.dukbis.penjualan@app.com')->first();

        if (! $mk || ! $smDukbis) {
            $this->command->warn('Approver account belum ada — lewati ApprovalFlowSeeder.');

            return;
        }

        // Flow 1: Fix Cost — satu tahap, Manager Keuangan ATAU SM Dukbis dan Penjualan (prioritas 1)
        $flowFixCost = ApprovalFlow::firstOrCreate(
            ['name' => 'Pengadaan Fix Cost', 'entity_type' => 'pengadaan'],
            ['description' => 'Approval untuk pengadaan tipe Fix Cost: Manager Keuangan atau SM Dukbis dan Penjualan.', 'is_active' => true, 'priority' => 1]
        );

        $this->syncFlow($flowFixCost, [
            ['field' => 'tipe_biaya', 'operator' => '=', 'value' => TipeBiaya::FixCost->value],
        ], [
            ['name' => 'Approval Manager Keuangan atau SM Dukbis dan Penjualan', 'mode' => 'any', 'approvers' => [$mk->id, $smDukbis->id]],
        ], 1);

        // Flow per Bisnis Area: RAB Project — SM BA dulu → Manager Keuangan atau SM Dukbis
        $smPerBa = [
            'TRL' => 'sm.trl@app.com',
            'ACY' => 'sm.acy@app.com',
            'ENI' => 'sm.eni@app.com',
            'TKDN & Pemerintahan' => 'sm.tkdn@app.com',
        ];

        $priority = 2;
        foreach ($smPerBa as $baName => $email) {
            $ba = BisnisArea::where('name', $baName)->first();
            $smBa = User::where('email', $email)->first();

            if (! $ba || ! $smBa) {
                continue;
            }

            $flowBa = ApprovalFlow::firstOrCreate(
                ['name' => "Pengadaan RAB Project {$baName}", 'entity_type' => 'pengadaan'],
                ['description' => "Approval untuk pengadaan RAB Project {$baName}: SM {$baName} lalu Manager Keuangan atau SM Dukbis dan Penjualan.", 'is_active' => true, 'priority' => $priority]
            );

            $this->syncFlow($flowBa, [
                ['field' => 'tipe_biaya', 'operator' => '=', 'value' => TipeBiaya::RabProject->value],
                ['field' => 'bisnis_area_id', 'operator' => '=', 'value' => (string) $ba->id],
            ], [
                ['name' => "Approval SM {$baName}", 'mode' => 'any', 'approvers' => [$smBa->id]],
                ['name' => 'Approval Manager Keuangan atau SM Dukbis dan Penjualan', 'mode' => 'any', 'approvers' => [$mk->id, $smDukbis->id]],
            ], $priority);

            $priority++;
        }
    }

    protected function syncFlow(ApprovalFlow $flow, array $conditions, array $steps, int $priority): void
    {
        $flow->update(['is_active' => true, 'priority' => $priority]);

        $flow->conditions()->delete();
        foreach ($conditions as $c) {
            $flow->conditions()->create($c + ['value2' => null]);
        }

        $flow->steps()->delete();
        foreach ($steps as $i => $s) {
            $step = $flow->steps()->create([
                'position' => $i + 1,
                'name' => $s['name'],
                'mode' => $s['mode'],
            ]);
            $step->approvers()->sync($s['approvers']);
        }
    }
}
