<?php

namespace App\Policies;

use App\Models\BisnisArea;
use App\Models\User;

class BisnisAreaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('bisnis_area_view');
    }

    public function view(User $user, BisnisArea $bisnisArea): bool
    {
        return $user->can('bisnis_area_view');
    }

    public function create(User $user): bool
    {
        return $user->can('bisnis_area_create');
    }

    public function update(User $user, BisnisArea $bisnisArea): bool
    {
        return $user->can('bisnis_area_update');
    }

    public function delete(User $user, BisnisArea $bisnisArea): bool
    {
        // Dependency check harus melihat SEMUA pengadaan (bypass global scope
        // bisnis-area) — BA tidak boleh dihapus walau pengadaannya di luar scope user.
        if ($bisnisArea->pengadaans()->withoutGlobalScope('bisnis-area')->exists()) {
            return false;
        }

        return $user->can('bisnis_area_delete');
    }

    public function toggleStatus(User $user, BisnisArea $bisnisArea): bool
    {
        return $user->can('bisnis_area_update');
    }
}
