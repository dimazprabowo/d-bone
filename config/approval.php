<?php

use App\Enums\TipeBiaya;
use App\Models\BisnisArea;
use App\Models\Cabang;
use App\Models\Vendor;

return [
    /*
    | Whitelist field yang bisa dipakai sebagai kondisi pemilihan alur
    | per entity. type: 'number' (operator pembanding), 'enum' (opsi dari
    | backed enum), 'relation' (opsi dari tabel, ambil id => name).
    */
    'entities' => [
        'pengadaan' => [
            'label' => 'Pengadaan',
            'fields' => [
                'total_biaya' => ['label' => 'Total Biaya', 'type' => 'number'],
                'tipe_biaya' => ['label' => 'Tipe Biaya', 'type' => 'enum', 'options' => TipeBiaya::class],
                'cabang_id' => ['label' => 'Cabang', 'type' => 'relation', 'model' => Cabang::class],
                'vendor_id' => ['label' => 'Vendor', 'type' => 'relation', 'model' => Vendor::class],
                'bisnis_area_id' => ['label' => 'Bisnis Area', 'type' => 'relation', 'model' => BisnisArea::class],
            ],
        ],
    ],
];
