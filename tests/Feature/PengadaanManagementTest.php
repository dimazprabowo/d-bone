<?php

namespace Tests\Feature;

use App\Enums\JatuhTempoStatus;
use App\Livewire\Pengadaan\PengadaanForm;
use App\Livewire\Pengadaan\PengadaanManagement;
use App\Models\ApprovalFlow;
use App\Models\ApprovalInstance;
use App\Models\BisnisArea;
use App\Models\KategoriItem;
use App\Models\Klaster;
use App\Models\Pengadaan;
use App\Models\Satuan;
use App\Models\SystemConfiguration;
use App\Models\User;
use App\Models\Vendor;
use App\Services\ApprovalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PengadaanManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function actingUserWithPermissions(array $permissions): User
    {
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        Permission::firstOrCreate(['name' => 'access_all_cabang', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'access_all_bisnis_area', 'guard_name' => 'web']);

        $role = Role::firstOrCreate(['name' => 'test-role-pengadaan', 'guard_name' => 'web']);
        $role->syncPermissions(array_merge($permissions, ['access_all_cabang', 'access_all_bisnis_area']));

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    /**
     * User BA-scoped: punya akses semua cabang TAPI tanpa
     * access_all_bisnis_area, dan BA di-attach via pivot bisnis_area_user.
     * User ini hanya boleh melihat pengadaan BA-nya sendiri.
     */
    protected function actingBaScopedUser(array $permissions, BisnisArea $ba): User
    {
        foreach (array_merge($permissions, ['access_all_cabang']) as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $role = Role::create(['name' => 'test-role-ba-scoped-'.uniqid(), 'guard_name' => 'web']);
        $role->syncPermissions(array_merge($permissions, ['access_all_cabang']));

        $user = User::factory()->create();
        $user->assignRole($role);
        $user->bisnisAreas()->attach($ba->id);

        return $user;
    }

    public function test_user_without_permission_cannot_view_pengadaan_list(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(PengadaanManagement::class)->assertForbidden();
    }

    public function test_user_with_permission_can_view_pengadaan_list(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view']);
        $pengadaan = Pengadaan::factory()->create(['no_pesanan_spk' => 'SPK-TEST-001']);

        $this->actingAs($user);

        Livewire::test(PengadaanManagement::class)
            ->assertSee('SPK-TEST-001');
    }

    public function test_pengadaan_list_search_matches_nama_pemohon(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view']);
        Pengadaan::factory()->create(['nama_pemohon' => 'Budi Santoso', 'no_pesanan_spk' => 'SPK-PEMOHON-1']);
        Pengadaan::factory()->create(['nama_pemohon' => 'Joko Prasetyo', 'no_pesanan_spk' => 'SPK-PEMOHON-2']);

        $this->actingAs($user);

        Livewire::test(PengadaanManagement::class)
            ->set('search', 'Budi Santoso')
            ->assertSee('SPK-PEMOHON-1')
            ->assertDontSee('SPK-PEMOHON-2');
    }

    public function test_bisnis_area_scoped_user_only_sees_own_ba_pengadaan(): void
    {
        $baTrl = BisnisArea::factory()->create(['name' => 'TRL']);
        $baEni = BisnisArea::factory()->create(['name' => 'ENI']);
        $user = $this->actingBaScopedUser(['pengadaan_view'], $baTrl);

        Pengadaan::factory()->create(['no_pesanan_spk' => 'SPK-BA-TRL', 'bisnis_area_id' => $baTrl->id]);
        Pengadaan::factory()->create(['no_pesanan_spk' => 'SPK-BA-ENI', 'bisnis_area_id' => $baEni->id]);

        $this->actingAs($user);

        Livewire::test(PengadaanManagement::class)
            ->assertSee('SPK-BA-TRL')
            ->assertDontSee('SPK-BA-ENI');
    }

    public function test_bisnis_area_scoped_user_with_no_ba_sees_nothing(): void
    {
        $ba = BisnisArea::factory()->create();
        Pengadaan::factory()->create(['no_pesanan_spk' => 'SPK-BA-ANY', 'bisnis_area_id' => $ba->id]);

        $user = User::factory()->create();
        $role = Role::create(['name' => 'test-role-no-ba-'.uniqid(), 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'pengadaan_view', 'guard_name' => 'web']);
        $role->syncPermissions(['pengadaan_view']);
        $user->assignRole($role);

        $this->actingAs($user);

        Livewire::test(PengadaanManagement::class)
            ->assertDontSee('SPK-BA-ANY');
    }

    public function test_user_with_access_all_bisnis_area_sees_all(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view']);
        $baTrl = BisnisArea::factory()->create(['name' => 'TRL']);
        $baEni = BisnisArea::factory()->create(['name' => 'ENI']);
        Pengadaan::factory()->create(['no_pesanan_spk' => 'SPK-BA-TRL', 'bisnis_area_id' => $baTrl->id]);
        Pengadaan::factory()->create(['no_pesanan_spk' => 'SPK-BA-ENI', 'bisnis_area_id' => $baEni->id]);

        $this->actingAs($user);

        Livewire::test(PengadaanManagement::class)
            ->assertSee('SPK-BA-TRL')
            ->assertSee('SPK-BA-ENI');
    }

    public function test_dropdown_filters_accept_multiple_values(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view']);
        $vendorA = Vendor::factory()->create();
        $vendorB = Vendor::factory()->create();
        $vendorC = Vendor::factory()->create();
        Pengadaan::factory()->create(['vendor_id' => $vendorA->id, 'no_pesanan_spk' => 'SPK-MULTI-A']);
        Pengadaan::factory()->create(['vendor_id' => $vendorB->id, 'no_pesanan_spk' => 'SPK-MULTI-B']);
        Pengadaan::factory()->create(['vendor_id' => $vendorC->id, 'no_pesanan_spk' => 'SPK-MULTI-C']);

        $this->actingAs($user);

        Livewire::test(PengadaanManagement::class)
            ->set('vendorFilter', [$vendorA->id, $vendorB->id])
            ->assertSee('SPK-MULTI-A')
            ->assertSee('SPK-MULTI-B')
            ->assertDontSee('SPK-MULTI-C');
    }

    public function test_multiple_status_approval_values_filter_together(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view']);
        Pengadaan::factory()->create(['no_pesanan_spk' => 'SPK-PENDING-1', 'status_approval' => 'pending']);
        Pengadaan::factory()->approved()->create(['no_pesanan_spk' => 'SPK-APPROVED-1']);
        Pengadaan::factory()->create(['no_pesanan_spk' => 'SPK-REJECTED-1', 'status_approval' => 'rejected']);

        $this->actingAs($user);

        Livewire::test(PengadaanManagement::class)
            ->set('statusFilter', ['pending', 'approved'])
            ->assertSee('SPK-PENDING-1')
            ->assertSee('SPK-APPROVED-1')
            ->assertDontSee('SPK-REJECTED-1');
    }

    public function test_user_can_create_pengadaan_with_items(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view', 'pengadaan_create']);
        $vendor = Vendor::factory()->create();
        $kategoriItem = KategoriItem::factory()->create();
        $vendor->kategoriItems()->sync([$kategoriItem->id]);

        $this->actingAs($user);

        Livewire::test(PengadaanForm::class)
            ->set('no_pesanan_spk', 'spk-2026-001')
            ->set('nama_pemohon', 'Budi Santoso')
            ->set('tipe_biaya', 'Fix Cost')
            ->set('klaster_id', $vendor->klaster_id)
            ->set('vendor_id', $vendor->id)
            ->set('bisnis_area_id', BisnisArea::factory()->create()->id)
            ->set('tanggal_pesanan_spk', now()->format('Y-m-d'))
            ->set('items', [
                ['id' => null, 'nama_item' => 'Laptop', 'kategori_item_id' => $kategoriItem->id, 'qty' => 2, 'satuan_id' => Satuan::factory()->create()->id, 'harga_satuan' => '10000000'],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pengadaans', [
            'no_pesanan_spk' => 'SPK-2026-001',
            'nama_pemohon' => 'Budi Santoso',
            'tipe_biaya' => 'Fix Cost',
            'no_wbs' => null,
            'vendor_id' => $vendor->id,
            'total_biaya' => 20000000,
        ]);

        $pengadaan = Pengadaan::where('no_pesanan_spk', 'SPK-2026-001')->first();
        $this->assertDatabaseHas('pengadaan_items', [
            'pengadaan_id' => $pengadaan->id,
            'nama_item' => 'Laptop',
            'kategori_item_id' => $kategoriItem->id,
            'qty' => 2,
            'subtotal' => 20000000,
        ]);
    }

    public function test_vendor_options_filtered_by_selected_klaster(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view', 'pengadaan_create']);
        $klasterA = Klaster::factory()->create();
        $klasterB = Klaster::factory()->create();
        $vendorA = Vendor::factory()->create(['klaster_id' => $klasterA->id]);
        Vendor::factory()->create(['klaster_id' => $klasterB->id]);

        $this->actingAs($user);

        $component = Livewire::test(PengadaanForm::class);
        $this->assertEmpty($component->instance()->vendorOptions);

        $component->set('klaster_id', $klasterA->id)->assertSet('vendor_id', null);

        $options = collect($component->instance()->vendorOptions);
        $this->assertEquals([$vendorA->id], $options->pluck('value')->all());
    }

    public function test_kategori_item_options_filtered_by_selected_vendor(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view', 'pengadaan_create']);
        $vendorA = Vendor::factory()->create();
        $vendorB = Vendor::factory()->create();
        $kategoriA = KategoriItem::factory()->create();
        $kategoriB = KategoriItem::factory()->create();
        $vendorA->kategoriItems()->sync([$kategoriA->id]);
        $vendorB->kategoriItems()->sync([$kategoriB->id]);

        $this->actingAs($user);

        $component = Livewire::test(PengadaanForm::class);
        $this->assertEmpty($component->instance()->kategoriItemOptions);

        $component->set('vendor_id', $vendorA->id);

        $options = collect($component->instance()->kategoriItemOptions);
        $this->assertEquals([$kategoriA->id], $options->pluck('value')->all());
    }

    public function test_item_kategori_must_belong_to_selected_vendor(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view', 'pengadaan_create']);
        $vendor = Vendor::factory()->create();
        $otherKategori = KategoriItem::factory()->create();

        $this->actingAs($user);

        Livewire::test(PengadaanForm::class)
            ->set('no_pesanan_spk', 'SPK-2026-002')
            ->set('nama_pemohon', 'Andi Wijaya')
            ->set('tipe_biaya', 'Fix Cost')
            ->set('klaster_id', $vendor->klaster_id)
            ->set('vendor_id', $vendor->id)
            ->set('bisnis_area_id', BisnisArea::factory()->create()->id)
            ->set('tanggal_pesanan_spk', now()->format('Y-m-d'))
            ->set('items', [
                ['id' => null, 'nama_item' => 'Laptop', 'kategori_item_id' => $otherKategori->id, 'qty' => 1, 'satuan_id' => Satuan::factory()->create()->id, 'harga_satuan' => '10000000'],
            ])
            ->call('save')
            ->assertHasErrors(['items.0.kategori_item_id']);
    }

    public function test_nama_project_and_no_wbs_required_when_tipe_biaya_rab_project(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view', 'pengadaan_create']);
        $vendor = Vendor::factory()->create();

        $this->actingAs($user);

        Livewire::test(PengadaanForm::class)
            ->set('no_pesanan_spk', 'SPK-2026-003')
            ->set('nama_pemohon', 'Siti Rahayu')
            ->set('tipe_biaya', 'RAB Project')
            ->set('klaster_id', $vendor->klaster_id)
            ->set('vendor_id', $vendor->id)
            ->set('tanggal_pesanan_spk', now()->format('Y-m-d'))
            ->set('items', [
                ['id' => null, 'nama_item' => 'Laptop', 'kategori_item_id' => null, 'qty' => 1, 'satuan_id' => Satuan::factory()->create()->id, 'harga_satuan' => '5000000'],
            ])
            ->call('save')
            ->assertHasErrors(['nama_project', 'no_wbs', 'bisnis_area_id'])
            ->set('nama_project', 'Project Kalibrasi Tahap 1')
            ->set('no_wbs', 'wbs-prj-001')
            ->set('bisnis_area_id', BisnisArea::factory()->create()->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pengadaans', [
            'no_pesanan_spk' => 'SPK-2026-003',
            'tipe_biaya' => 'RAB Project',
            'nama_project' => 'Project Kalibrasi Tahap 1',
            'no_wbs' => 'WBS-PRJ-001',
        ]);
    }

    public function test_user_can_create_pengadaan_with_only_required_fields(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view', 'pengadaan_create']);

        $this->actingAs($user);

        Livewire::test(PengadaanForm::class)
            ->set('no_pesanan_spk', null)
            ->set('nama_pemohon', 'Budi Santoso')
            ->set('tipe_biaya', 'Fix Cost')
            ->set('klaster_id', null)
            ->set('vendor_id', null)
            ->set('bisnis_area_id', null)
            ->set('tanggal_pesanan_spk', null)
            ->set('items', [
                ['id' => null, 'nama_item' => 'Laptop', 'kategori_item_id' => null, 'qty' => 1, 'satuan_id' => Satuan::factory()->create()->id, 'harga_satuan' => '5000000'],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pengadaans', [
            'no_pesanan_spk' => null,
            'nama_pemohon' => 'Budi Santoso',
            'vendor_id' => null,
            'bisnis_area_id' => null,
            'tanggal_pesanan_spk' => null,
            'total_biaya' => 5000000,
        ]);
    }

    public function test_user_without_create_permission_cannot_create_pengadaan(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view']);
        $this->actingAs($user);

        Livewire::test(PengadaanForm::class)->assertForbidden();
    }

    protected function makeApprovalInstance(Pengadaan $pengadaan, array $approverIds): ApprovalInstance
    {
        $flow = ApprovalFlow::create([
            'name' => 'Test Flow',
            'entity_type' => 'pengadaan',
            'is_active' => true,
        ]);

        $step = $flow->steps()->create([
            'position' => 1,
            'name' => 'Review',
            'mode' => 'any',
        ]);

        $step->approvers()->sync($approverIds);

        return app(ApprovalService::class)->submit($pengadaan, $pengadaan->creator);
    }

    public function test_user_with_approve_permission_can_approve_pending_pengadaan(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view', 'pengadaan_approve']);
        $pengadaan = Pengadaan::factory()->create();
        $this->makeApprovalInstance($pengadaan, [$user->id]);

        $this->actingAs($user);

        Livewire::test(PengadaanManagement::class)
            ->call('confirmApprove', $pengadaan->id)
            ->call('approve')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pengadaans', [
            'id' => $pengadaan->id,
            'status_approval' => 'approved',
            'approved_by' => $user->id,
        ]);
    }

    public function test_user_with_approve_permission_can_reject_pending_pengadaan(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view', 'pengadaan_approve']);
        $pengadaan = Pengadaan::factory()->create();
        $this->makeApprovalInstance($pengadaan, [$user->id]);

        $this->actingAs($user);

        Livewire::test(PengadaanManagement::class)
            ->call('confirmReject', $pengadaan->id)
            ->set('rejectionReason', 'Harga tidak sesuai anggaran')
            ->call('reject')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pengadaans', [
            'id' => $pengadaan->id,
            'status_approval' => 'rejected',
            'rejection_reason' => 'Harga tidak sesuai anggaran',
        ]);
    }

    public function test_cannot_approve_already_approved_pengadaan(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view', 'pengadaan_approve']);
        $pengadaan = Pengadaan::factory()->approved()->create();

        $this->actingAs($user);

        Livewire::test(PengadaanManagement::class)
            ->call('confirmApprove', $pengadaan->id)
            ->assertForbidden();
    }

    public function test_user_with_permission_can_delete_pengadaan(): void
    {
        $user = $this->actingUserWithPermissions(['pengadaan_view', 'pengadaan_delete']);
        $pengadaan = Pengadaan::factory()->create();

        $this->actingAs($user);

        Livewire::test(PengadaanManagement::class)
            ->call('confirmDelete', $pengadaan->id)
            ->call('delete');

        $this->assertSoftDeleted('pengadaans', ['id' => $pengadaan->id]);
    }

    public function test_deleted_pengadaan_spk_number_can_be_reused(): void
    {
        $pengadaan = Pengadaan::factory()->create(['no_pesanan_spk' => 'SPK-REUSE-1']);

        $pengadaan->delete();

        $this->assertDatabaseHas('pengadaans', [
            'id' => $pengadaan->id,
            'deleted_spk_marker' => (string) $pengadaan->id,
        ]);

        Pengadaan::factory()->create(['no_pesanan_spk' => 'SPK-REUSE-1']);

        $this->assertEquals(2, Pengadaan::withTrashed()
            ->where('no_pesanan_spk', 'SPK-REUSE-1')
            ->count());
    }

    public function test_jatuh_tempo_status_derived_from_unpaid_invoice_age(): void
    {
        SystemConfiguration::create([
            'key' => 'pengadaan.jatuh_tempo_warning_days',
            'category' => 'pengadaan',
            'value' => '30',
            'data_type' => 'integer',
            'is_editable' => true,
            'is_active' => true,
        ]);
        SystemConfiguration::create([
            'key' => 'pengadaan.jatuh_tempo_overdue_days',
            'category' => 'pengadaan',
            'value' => '60',
            'data_type' => 'integer',
            'is_editable' => true,
            'is_active' => true,
        ]);

        $pengadaan = Pengadaan::factory()->create();
        $aman = $pengadaan->invoices()->create(['no_invoice' => 'INV-A', 'tanggal_invoice' => now()->subDays(10), 'jumlah' => 1000]);
        $proses = $pengadaan->invoices()->create(['no_invoice' => 'INV-B', 'tanggal_invoice' => now()->subDays(40), 'jumlah' => 1000]);
        $lewat = $pengadaan->invoices()->create(['no_invoice' => 'INV-C', 'tanggal_invoice' => now()->subDays(70), 'jumlah' => 1000]);

        $this->assertSame(JatuhTempoStatus::Aman, $aman->jatuh_tempo_status);
        $this->assertSame(JatuhTempoStatus::Proses, $proses->jatuh_tempo_status);
        $this->assertSame(JatuhTempoStatus::Lewat, $lewat->jatuh_tempo_status);
        $this->assertSame(JatuhTempoStatus::Lewat, $pengadaan->jatuh_tempo_status);

        // Invoice lunas tidak ikut dihitung status jatuh tempo
        $pengadaanLunas = Pengadaan::factory()->create();
        $lunas = $pengadaanLunas->invoices()->create(['no_invoice' => 'INV-D', 'tanggal_invoice' => now()->subDays(80), 'jumlah' => 1000]);
        $lunas->payments()->create(['tanggal_bayar' => now(), 'jumlah_bayar' => 1000, 'metode_bayar' => 'Transfer']);

        $this->assertNull($lunas->jatuh_tempo_status);
        $this->assertNull($pengadaanLunas->jatuh_tempo_status);
    }

    public function test_jatuh_tempo_alert_disabled_when_config_inactive(): void
    {
        SystemConfiguration::create([
            'key' => 'pengadaan.jatuh_tempo_warning_days',
            'category' => 'pengadaan',
            'value' => '30',
            'data_type' => 'integer',
            'is_editable' => true,
            'is_active' => false,
        ]);

        $pengadaan = Pengadaan::factory()->create();
        $invoice = $pengadaan->invoices()->create(['no_invoice' => 'INV-E', 'tanggal_invoice' => now()->subDays(70), 'jumlah' => 1000]);

        $this->assertNull($invoice->jatuh_tempo_status);
        $this->assertNull($pengadaan->jatuh_tempo_status);
    }
}
