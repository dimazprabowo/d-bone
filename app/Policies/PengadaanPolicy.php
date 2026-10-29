<?php

namespace App\Policies;

use App\Models\Pengadaan;
use App\Models\User;
use App\Services\ApprovalService;

class PengadaanPolicy
{
    /**
     * Determine whether the user can access the given pengadaan across cabang boundaries.
     *
     * Users WITHOUT `access_all_cabang` may only interact with pengadaan yang cabang_id-nya
     * cocok dengan cabang mereka sendiri.
     */
    protected function canAccessCabang(User $user, Pengadaan $pengadaan): bool
    {
        if ($user->can('access_all_cabang')) {
            return true;
        }

        return $user->cabang_id !== null && $user->cabang_id === $pengadaan->cabang_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('pengadaan_view');
    }

    public function view(User $user, Pengadaan $pengadaan): bool
    {
        return $user->can('pengadaan_view') && $this->canAccessCabang($user, $pengadaan);
    }

    public function create(User $user): bool
    {
        return $user->can('pengadaan_create');
    }

    public function update(User $user, Pengadaan $pengadaan): bool
    {
        return $user->can('pengadaan_update') && $this->canAccessCabang($user, $pengadaan);
    }

    public function delete(User $user, Pengadaan $pengadaan): bool
    {
        if ($pengadaan->invoices()->exists()) {
            return false;
        }

        return $user->can('pengadaan_delete') && $this->canAccessCabang($user, $pengadaan);
    }

    public function approve(User $user, Pengadaan $pengadaan): bool
    {
        if (! $this->canAccessCabang($user, $pengadaan)) {
            return false;
        }

        $instance = $pengadaan->activeApprovalInstance();

        if (! $instance) {
            return false;
        }

        return app(ApprovalService::class)->canActOnCurrentStep($instance, $user);
    }

    public function exportExcel(User $user): bool
    {
        return $user->can('pengadaan_export_excel');
    }

    public function exportPdf(User $user): bool
    {
        return $user->can('pengadaan_export_pdf');
    }
}
