<?php

namespace Database\Seeders;

use App\Enums\BisnisAreaStatus;
use App\Models\BisnisArea;
use Illuminate\Database\Seeder;

class BisnisAreaSeeder extends Seeder
{
    public function run(): void
    {
        $bisnisAreas = [
            ['name' => 'TRL', 'description' => 'Transportation and Logistics — layanan untuk sektor transportasi dan logistik.'],
            ['name' => 'ACY', 'description' => 'BKI Academy — layanan pelatihan, pengembangan kompetensi, dan sertifikasi profesi.'],
            ['name' => 'ENI', 'description' => 'Energy and Industry — layanan untuk sektor energi dan industri.'],
            ['name' => 'TKDN & Pemerintahan', 'description' => 'Verifikasi Tingkat Komponen Dalam Negeri (TKDN) untuk produk dan jasa dalam negeri dan pemerintahan.'],
        ];

        foreach ($bisnisAreas as $bisnisArea) {
            BisnisArea::firstOrCreate(
                ['name' => $bisnisArea['name']],
                [...$bisnisArea, 'status' => BisnisAreaStatus::Aktif->value]
            );
        }
    }
}
