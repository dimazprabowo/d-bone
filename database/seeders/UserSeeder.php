<?php

namespace Database\Seeders;

use App\Enums\UserApprovalStatus;
use App\Models\BisnisArea;
use App\Models\Cabang;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Semua akun di bawah ini (kecuali super admin & admin pusat/COE)
        // adalah akun Cabang Jakarta (JKT) — satu-satunya cabang saat ini.
        $cabangJkt = Cabang::where('code', 'JKT')->first();

        $seedUsers = [
            [
                'email' => 'superadmin@app.com',
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '021-1234566',
                'position' => 'Super Administrator',
                'cabang_id' => null,
                'role' => 'super admin',
            ],
            [
                'email' => 'admin@app.com',
                'name' => 'Admin Pusat',
                'password' => Hash::make('password'),
                'phone' => '021-1234567',
                'position' => 'COE / Pusat',
                'cabang_id' => null,
                'role' => 'admin pusat',
            ],
            [
                'email' => 'admincabang@app.com',
                'name' => 'Admin Cabang Tanjung Priok',
                'password' => Hash::make('password'),
                'phone' => '021-1234568',
                'position' => 'Admin Cabang',
                'cabang_id' => $cabangJkt?->id,
                'role' => 'admin cabang',
            ],
            [
                'email' => 'user@app.com',
                'name' => 'Staff Cabang Jakarta',
                'password' => Hash::make('password'),
                'phone' => '021-1234569',
                'position' => 'Staff',
                'cabang_id' => $cabangJkt?->id,
                'role' => 'staff cabang',
            ],
            // Approver akun — dipakai di Alur Persetujuan
            [
                'email' => 'manager.keuangan@app.com',
                'name' => 'Manager Keuangan',
                'password' => Hash::make('password'),
                'phone' => '021-1234570',
                'position' => 'Manager Keuangan',
                'cabang_id' => $cabangJkt?->id,
                'role' => 'manager pusat',
            ],
            [
                'email' => 'sm.dukbis.penjualan@app.com',
                'name' => 'Senior Manager Dukungan Bisnis dan Penjualan',
                'password' => Hash::make('password'),
                'phone' => '021-1234571',
                'position' => 'Senior Manager Dukungan Bisnis dan Penjualan',
                'cabang_id' => $cabangJkt?->id,
                'role' => 'manager pusat',
            ],
            [
                'email' => 'kepala.cabang@app.com',
                'name' => 'Kepala Cabang',
                'password' => Hash::make('password'),
                'phone' => '021-1234572',
                'position' => 'Kepala Cabang',
                'cabang_id' => $cabangJkt?->id,
                'role' => 'staff cabang',
            ],
        ];

        foreach ($seedUsers as $data) {
            $role = $data['role'];
            unset($data['role']);

            $data['is_active'] = true;
            $data['approval_status'] = UserApprovalStatus::Approved;
            $data['email_verified_at'] = now();

            $user = User::firstOrCreate(['email' => $data['email']], $data);
            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }
        }

        $this->seedBisnisAreaUsers($cabangJkt);
    }

    /**
     * Buat akun SM + Admin untuk SETIAP bisnis area, lalu attach BA-nya via
     * pivot bisnis_area_user (dipakai global scope Pengadaan — akun ini hanya
     * melihat pengadaan & summary biaya BA-nya sendiri).
     * Email: sm.{token-pertama-nama}@app.com / admin.{token}@app.com
     * (TRL -> sm.trl@, TKDN & Pemerintahan -> sm.tkdn@ — konsisten dengan
     *  mapping approver di ApprovalFlowSeeder via emailLocalPart()).
     * cabang_id = JKT (mereka tetap akun Cabang Jakarta secara organisasi),
     * tapi role diberi access_all_cabang agar melihat BA-nya di SEMUA cabang.
     */
    protected function seedBisnisAreaUsers(?Cabang $cabangJkt): void
    {
        $phoneCounter = 80;

        foreach (BisnisArea::orderBy('id')->get() as $ba) {
            $slug = $ba->emailLocalPart();

            foreach ([
                [
                    'email' => "sm.{$slug}@app.com",
                    'name' => "SM {$ba->name}",
                    'position' => "Senior Manager {$ba->name}",
                    'role' => 'senior manager',
                ],
                [
                    'email' => "admin.{$slug}@app.com",
                    'name' => "Admin {$ba->name}",
                    'position' => "Admin Bisnis Area {$ba->name}",
                    'role' => 'admin bisnis area',
                ],
            ] as $data) {
                $role = $data['role'];
                unset($data['role']);

                $user = User::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        ...$data,
                        'password' => Hash::make('password'),
                        'phone' => '021-1234'.str_pad((string) $phoneCounter++, 3, '0', STR_PAD_LEFT),
                        'cabang_id' => $cabangJkt?->id,
                        'is_active' => true,
                        'approval_status' => UserApprovalStatus::Approved,
                        'email_verified_at' => now(),
                    ]
                );

                // Enforce role & BA assignment (idempotent — aman di re-seed).
                $user->syncRoles([$role]);
                $user->bisnisAreas()->syncWithoutDetaching([$ba->id]);
            }
        }
    }
}
