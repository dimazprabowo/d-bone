<?php

namespace Database\Seeders;

use App\Enums\UserApprovalStatus;
use App\Models\Cabang;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $cabangPriok = Cabang::where('code', 'JKT')->first();
        $cabangSurabaya = Cabang::where('code', 'JKT')->first();

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
                'position' => 'Kepala Cabang',
                'cabang_id' => $cabangPriok?->id,
                'role' => 'admin cabang',
            ],
            [
                'email' => 'user@app.com',
                'name' => 'Staff Cabang Surabaya',
                'password' => Hash::make('password'),
                'phone' => '021-1234569',
                'position' => 'Staff',
                'cabang_id' => $cabangSurabaya?->id,
                'role' => 'staff cabang',
            ],
            // Approver akun — dipakai di Alur Persetujuan
            [
                'email' => 'manager.keuangan@app.com',
                'name' => 'Manager Keuangan',
                'password' => Hash::make('password'),
                'phone' => '021-1234570',
                'position' => 'Manager Keuangan',
                'cabang_id' => $cabangPriok?->id,
                'role' => 'staff cabang',
            ],
            [
                'email' => 'sm.dukbis.penjualan@app.com',
                'name' => 'Senior Manager Dukungan Bisnis dan Penjualan',
                'password' => Hash::make('password'),
                'phone' => '021-1234571',
                'position' => 'Senior Manager Dukungan Bisnis dan Penjualan',
                'cabang_id' => $cabangPriok?->id,
                'role' => 'staff cabang',
            ],
            [
                'email' => 'sm.trl@app.com',
                'name' => 'SM Transportation and Logistics',
                'password' => Hash::make('password'),
                'phone' => '021-1234573',
                'position' => 'Senior Manager TRL',
                'cabang_id' => $cabangPriok?->id,
                'role' => 'staff cabang',
            ],
            [
                'email' => 'sm.acy@app.com',
                'name' => 'SM BKI Academy',
                'password' => Hash::make('password'),
                'phone' => '021-1234574',
                'position' => 'Senior Manager ACY',
                'cabang_id' => $cabangPriok?->id,
                'role' => 'staff cabang',
            ],
            [
                'email' => 'sm.eni@app.com',
                'name' => 'SM Energy and Industry',
                'password' => Hash::make('password'),
                'phone' => '021-1234575',
                'position' => 'Senior Manager ENI',
                'cabang_id' => $cabangPriok?->id,
                'role' => 'staff cabang',
            ],
            [
                'email' => 'sm.tkdn@app.com',
                'name' => 'SM TKDN dan Pemerintahan',
                'password' => Hash::make('password'),
                'phone' => '021-1234576',
                'position' => 'Senior Manager TKDN & Pemerintahan',
                'cabang_id' => $cabangPriok?->id,
                'role' => 'staff cabang',
            ],
            [
                'email' => 'kepala.cabang@app.com',
                'name' => 'Kepala Cabang',
                'password' => Hash::make('password'),
                'phone' => '021-1234572',
                'position' => 'Kepala Cabang',
                'cabang_id' => $cabangPriok?->id,
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
    }
}
