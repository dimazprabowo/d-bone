<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;

class VendorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('vendor_view');
    }

    public function view(User $user, Vendor $vendor): bool
    {
        return $user->can('vendor_view');
    }

    public function create(User $user): bool
    {
        return $user->can('vendor_create');
    }

    public function update(User $user, Vendor $vendor): bool
    {
        return $user->can('vendor_update');
    }

    public function delete(User $user, Vendor $vendor): bool
    {
        // Dependency check bypass global scope bisnis-area — vendor tidak
        // boleh dihapus walau pengadaannya di luar BA scope user.
        if ($vendor->pengadaans()->withoutGlobalScope('bisnis-area')->exists()) {
            return false;
        }

        return $user->can('vendor_delete');
    }

    public function toggleStatus(User $user, Vendor $vendor): bool
    {
        return $user->can('vendor_update');
    }

    public function exportExcel(User $user): bool
    {
        return $user->can('vendor_export_excel');
    }

    public function exportPdf(User $user): bool
    {
        return $user->can('vendor_export_pdf');
    }
}
