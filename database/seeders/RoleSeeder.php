<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Struktur role aplikasi pengadaan barang & jasa (COE vs Cabang):
     *
     * - Super Admin  : bypass semua (Gate::before di AuthServiceProvider), tetap disync semua permission.
     * - Admin Pusat  : COE — akses & kontrol penuh SEMUA cabang (punya `access_all_cabang`).
     * - Admin Cabang : kelola pengadaan CABANGNYA SENDIRI saja (data-scoped di Service/Policy
     *                  berdasarkan `access_all_cabang` TIDAK dimiliki role ini).
     * - Staff Cabang : basic — lihat pengadaan cabangnya.
     *
     * PENTING: seluruh authorization di kode (Policy/Service/Blade) HANYA membaca permission,
     * TIDAK PERNAH membaca nama role. Role di sini murni bundel permission untuk kemudahan assign.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Super Admin - All permissions (bypass via Gate::before di AuthServiceProvider)
        $superAdmin = Role::firstOrCreate(['name' => 'super admin']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. Admin Pusat (COE) - Full access + akses lintas-cabang
        $adminPusat = Role::firstOrCreate(['name' => 'admin pusat']);
        $adminPusat->syncPermissions(Permission::all());

        // 3. Admin Cabang - Kelola pengadaan cabang sendiri (TANPA access_all_cabang,
        //    tapi bisa lihat semua BA di dalam cakupan cabangnya)
        $adminCabang = Role::firstOrCreate(['name' => 'admin cabang']);
        $adminCabang->syncPermissions([
            'dashboard_view',
            'cabang_view',
            'pengadaan_view',
            'pengadaan_create',
            'pengadaan_update',
            'pengadaan_delete',
            'pengadaan_export_excel',
            'pengadaan_export_pdf',
            'notifications_view',
            'chat_view',
            'chat_create',
            'chat_delete',
            'access_all_bisnis_area',
        ]);

        // 4. Staff Cabang - Basic: lihat pengadaan cabangnya (semua BA)
        $staffCabang = Role::firstOrCreate(['name' => 'staff cabang']);
        $staffCabang->syncPermissions([
            'dashboard_view',
            'cabang_view',
            'pengadaan_view',
            'notifications_view',
            'chat_view',
            'chat_create',
            'access_all_bisnis_area',
        ]);

        // 5. Admin Bisnis Area - Kelola pengadaan BA-nya sendiri di SEMUA cabang
        //    (access_all_cabang agar tidak ter-scoped cabang; TANPA
        //    access_all_bisnis_area sehingga ter-scope ke BA di pivot
        //    bisnis_area_user — berlaku untuk list & summary biaya).
        //    Juga kelola master data pendukung pengadaan (klaster, satuan,
        //    kategori item, vendor — TANPA cabang, itu domain pusat).
        //    Delete dibatasi Policy: data yang sudah dipakai
        //    (vendor/pengadaan item/pengadaan) tidak bisa dihapus.
        $adminBa = Role::firstOrCreate(['name' => 'admin bisnis area']);
        $adminBa->syncPermissions([
            'dashboard_view',
            'klaster_view',
            'klaster_create',
            'klaster_update',
            'klaster_delete',
            'satuan_view',
            'satuan_create',
            'satuan_update',
            'satuan_delete',
            'kategori_item_view',
            'kategori_item_create',
            'kategori_item_update',
            'kategori_item_delete',
            'vendor_view',
            'vendor_create',
            'vendor_update',
            'vendor_delete',
            'pengadaan_view',
            'pengadaan_create',
            'pengadaan_update',
            'pengadaan_delete',
            'pengadaan_export_excel',
            'pengadaan_export_pdf',
            'notifications_view',
            'chat_view',
            'chat_create',
            'chat_delete',
            'access_all_cabang',
        ]);

        // 6. Senior Manager - View-only untuk BA-nya di semua cabang (approver
        //    alur persetujuan; TANPA access_all_bisnis_area → scoped per BA).
        $seniorManager = Role::firstOrCreate(['name' => 'senior manager']);
        $seniorManager->syncPermissions([
            'dashboard_view',
            'cabang_view',
            'pengadaan_view',
            'notifications_view',
            'chat_view',
            'chat_create',
            'access_all_cabang',
        ]);

        // 7. Manager Pusat - Staff-level tapi lintas cabang & BA (untuk
        //    approver pusat seperti Manager Keuangan & SM Dukbis — mereka
        //    adalah approver step-2 untuk pengadaan semua BA/cabang).
        $managerPusat = Role::firstOrCreate(['name' => 'manager pusat']);
        $managerPusat->syncPermissions([
            'dashboard_view',
            'cabang_view',
            'pengadaan_view',
            'notifications_view',
            'chat_view',
            'chat_create',
            'access_all_cabang',
            'access_all_bisnis_area',
        ]);
    }
}
