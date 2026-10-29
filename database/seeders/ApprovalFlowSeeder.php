<?php

namespace Database\Seeders;

use App\Models\ApprovalFlow;
use App\Models\BisnisArea;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApprovalFlowSeeder extends Seeder
{
    public function run(): void
    {
        $mk = User::where('email', 'manager.keuangan@app.com')->first();
        $sm = User::where('email', 'sm.dukbis.penjualan@app.com')->first();
        $kc = User::where('email', 'kepala.cabang@app.com')->first();

        if (! $mk || ! $sm || ! $kc) {
            $this->command->warn('Approver account belum ada — lewati ApprovalFlowSeeder.');

            return;
        }

        // Flow 1: di bawah Rp10 juta — sequential MK → SM (prioritas 1)
        $flowKecil = ApprovalFlow::firstOrCreate(
            ['name' => 'Pengadaan < Rp10 Juta', 'entity_type' => 'pengadaan'],
            ['description' => 'Approval berjenjang untuk pengadaan dengan total biaya di bawah Rp10.000.000.', 'is_active' => true, 'priority' => 1]
        );

        $this->syncFlow($flowKecil, [
            ['field' => 'total_biaya', 'operator' => '<', 'value' => '10000000'],
        ], [
            ['name' => 'Approval Manager Keuangan', 'mode' => 'any', 'approvers' => [$mk->id]],
            ['name' => 'Approval Senior Manager Dukungan Bisnis dan Penjualan', 'mode' => 'any', 'approvers' => [$sm->id]],
        ], 1);

        // Flow per Bisnis Area: SM BA dulu → Keuangan & SM Dukbis bersamaan → Kepala Cabang
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
                ['name' => "Pengadaan {$baName} ≥ Rp10 Juta", 'entity_type' => 'pengadaan'],
                ['description' => "Approval berjenjang untuk pengadaan {$baName}: SM {$baName} → Keuangan & SM Dukbis → Kepala Cabang.", 'is_active' => true, 'priority' => $priority]
            );

            $this->syncFlow($flowBa, [
                ['field' => 'bisnis_area_id', 'operator' => '=', 'value' => (string) $ba->id],
                ['field' => 'total_biaya', 'operator' => '>=', 'value' => '10000000'],
            ], [
                ['name' => "Approval SM {$baName}", 'mode' => 'any', 'approvers' => [$smBa->id]],
                ['name' => 'Approval Manager Keuangan & SM Dukbis dan Penjualan', 'mode' => 'all', 'approvers' => [$mk->id, $sm->id]],
                ['name' => 'Approval Kepala Cabang', 'mode' => 'any', 'approvers' => [$kc->id]],
            ], $priority);

            $priority++;
        }

        // Flow fallback: Rp10 juta ke atas tanpa BA khusus — MK + SM bersamaan → Kepala Cabang
        $flowBesar = ApprovalFlow::firstOrCreate(
            ['name' => 'Pengadaan ≥ Rp10 Juta', 'entity_type' => 'pengadaan'],
            ['description' => 'Approval untuk pengadaan dengan total biaya Rp10.000.000 atau lebih tanpa alur khusus bisnis area.', 'is_active' => true, 'priority' => 9]
        );

        $this->syncFlow($flowBesar, [
            ['field' => 'total_biaya', 'operator' => '>=', 'value' => '10000000'],
        ], [
            ['name' => 'Approval Manager Keuangan & SM Dukbis dan Penjualan', 'mode' => 'all', 'approvers' => [$mk->id, $sm->id]],
            ['name' => 'Approval Kepala Cabang', 'mode' => 'any', 'approvers' => [$kc->id]],
        ], 9);
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
