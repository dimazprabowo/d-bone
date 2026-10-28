<?php

namespace Database\Seeders;

use App\Enums\CabangStatus;
use App\Models\Cabang;
use Illuminate\Database\Seeder;

class CabangSeeder extends Seeder
{
    public function run(): void
    {
        $cabangs = [
            [
                'code' => 'JKT',
                'name' => 'Cabang Utama Komersial Jakarta',
                'address' => 'Jl. Yos Sudarso 38-39-40, Tanjung Priok, Jakarta Utara - 14320',
                'phone' => '021-43930990',
                'pic_name' => 'Kepala Cabang Utama Komersial Jakarta',
                'pic_phone' => '081200010001',
                'status' => CabangStatus::Active->value,
            ],
        ];

        foreach ($cabangs as $cabang) {
            Cabang::firstOrCreate(
                ['code' => $cabang['code']],
                $cabang
            );
        }
    }
}
