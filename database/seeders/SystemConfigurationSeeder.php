<?php

namespace Database\Seeders;

use App\Models\SystemConfiguration;
use Illuminate\Database\Seeder;

class SystemConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $configurations = [
            // General configurations
            [
                'key' => 'app.name',
                'category' => 'general',
                'value' => 'D-BONE',
                'data_type' => 'string',
                'description' => 'Nama aplikasi',
                'is_editable' => true,
                'is_active' => true,
            ],
            [
                'key' => 'app.logo',
                'category' => 'general',
                'value' => 'images/bki-main.webp',
                'data_type' => 'string',
                'description' => 'Path/URL logo aplikasi (untuk halaman login, sidebar, dsb). Path relatif terhadap folder public/ (mis. "images/bki-main.webp") atau URL absolut. Email client tetap memakai email_logo_url() yang di-hosting eksternal.',
                'is_editable' => true,
                'is_active' => true,
            ],
            [
                'key' => 'app.timezone',
                'category' => 'general',
                'value' => 'Asia/Jakarta',
                'data_type' => 'string',
                'description' => 'Timezone aplikasi',
                'is_editable' => true,
                'is_active' => true,
            ],

            // Registration configurations
            [
                'key' => 'registration.deadline',
                'category' => 'general',
                'value' => '',
                'data_type' => 'datetime',
                'description' => 'Batas waktu pendaftaran. Pendaftaran aktif jika: ada nilai, belum lewat deadline, dan status aktif. Kosongkan atau nonaktifkan untuk menutup pendaftaran.',
                'is_editable' => true,
                'is_active' => false,
            ],
            [
                'key' => 'registration.closed_message',
                'category' => 'general',
                'value' => 'Pendaftaran telah ditutup. Silakan hubungi administrator untuk informasi lebih lanjut.',
                'data_type' => 'string',
                'description' => 'Pesan yang ditampilkan ketika pendaftaran sudah ditutup',
                'is_editable' => true,
                'is_active' => true,
            ],
            [
                'key' => 'registration.requires_approval',
                'category' => 'general',
                'value' => '1',
                'data_type' => 'boolean',
                'description' => 'Apakah pendaftaran user baru memerlukan approval admin sebelum bisa login. Aktif = user baru harus di-approve admin. Nonaktif = user langsung aktif setelah daftar.',
                'is_editable' => true,
                'is_active' => true,
            ],
            [
                'key' => 'registration.default_role',
                'category' => 'general',
                'value' => 'staff cabang',
                'data_type' => 'string',
                'description' => 'Role default yang diberikan ke user yang mendaftar sendiri (self-registration). Role ini akan di-assign saat user di-approve oleh admin.',
                'is_editable' => true,
                'is_active' => true,
            ],
        ];

        foreach ($configurations as $config) {
            SystemConfiguration::firstOrCreate(
                ['key' => $config['key']],
                collect($config)->except('key')->toArray()
            );
        }
    }
}
