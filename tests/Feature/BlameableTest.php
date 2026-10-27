<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlameableTest extends TestCase
{
    use RefreshDatabase;

    public function test_created_by_and_updated_by_filled_from_auth_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cabang = Cabang::create(['code' => 'CBG1', 'name' => 'Cabang Uji', 'status' => 'active']);

        $this->assertSame($user->id, $cabang->created_by);
        $this->assertSame($user->id, $cabang->updated_by);
    }

    public function test_updated_by_changes_on_update_but_created_by_stays(): void
    {
        $creator = User::factory()->create();
        $editor = User::factory()->create();

        $this->actingAs($creator);
        $cabang = Cabang::create(['code' => 'CBG2', 'name' => 'Cabang Uji 2', 'status' => 'active']);

        $this->actingAs($editor);
        $cabang->update(['name' => 'Cabang Uji 2 Revisi']);

        $this->assertSame($creator->id, $cabang->created_by);
        $this->assertSame($editor->id, $cabang->updated_by);
    }

    public function test_audit_columns_null_when_no_authenticated_user(): void
    {
        $cabang = Cabang::create(['code' => 'CBG3', 'name' => 'Cabang Tanpa Auth', 'status' => 'active']);

        $this->assertNull($cabang->created_by);
        $this->assertNull($cabang->updated_by);
    }

    public function test_creator_and_updater_relations_resolve_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cabang = Cabang::create(['code' => 'CBG4', 'name' => 'Cabang Relasi', 'status' => 'active']);

        $this->assertTrue($cabang->creator->is($user));
        $this->assertTrue($cabang->updater->is($user));
    }
}
