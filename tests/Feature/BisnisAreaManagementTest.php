<?php

namespace Tests\Feature;

use App\Livewire\MasterData\BisnisAreaManagement;
use App\Models\BisnisArea;
use App\Models\Pengadaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BisnisAreaManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function actingUserWithPermissions(array $permissions): User
    {
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $user = User::factory()->create();
        $user->givePermissionTo($permissions);

        return $user;
    }

    public function test_index_requires_permission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(BisnisAreaManagement::class)->assertForbidden();
    }

    public function test_user_can_view_bisnis_area_list(): void
    {
        $user = $this->actingUserWithPermissions(['bisnis_area_view']);
        BisnisArea::factory()->create(['name' => 'TRL']);

        $this->actingAs($user);

        Livewire::test(BisnisAreaManagement::class)
            ->assertSee('TRL');
    }

    public function test_user_can_create_bisnis_area(): void
    {
        $user = $this->actingUserWithPermissions(['bisnis_area_view', 'bisnis_area_create']);

        $this->actingAs($user);

        Livewire::test(BisnisAreaManagement::class)
            ->set('name', 'TKDN')
            ->set('description', 'Verifikasi Tingkat Komponen Dalam Negeri.')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('bisnis_areas', [
            'name' => 'TKDN',
        ]);
    }

    public function test_bisnis_area_used_by_pengadaan_cannot_be_deleted(): void
    {
        $user = $this->actingUserWithPermissions(['bisnis_area_view', 'bisnis_area_delete']);
        $bisnisArea = BisnisArea::factory()->create();
        Pengadaan::factory()->create(['bisnis_area_id' => $bisnisArea->id]);

        $this->actingAs($user);

        $this->assertFalse($user->can('delete', $bisnisArea));
        $this->assertDatabaseHas('bisnis_areas', ['id' => $bisnisArea->id]);
    }
}
