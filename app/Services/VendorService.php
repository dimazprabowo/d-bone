<?php

namespace App\Services;

use App\Enums\VendorStatus;
use App\Models\Pengadaan;
use App\Models\Vendor;
use App\Traits\HasDynamicLike;
use App\Traits\SortsQuery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class VendorService
{
    use HasDynamicLike, SortsQuery;

    public function getFiltered(
        ?string $search = null,
        ?string $statusFilter = null,
        int $perPage = 15,
        ?string $sortField = null,
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        $query = Vendor::with('klaster')->withCount(['pengadaans', 'kategoriItems']);

        if ($search) {
            $operator = $this->getLikeOperator();
            $query->where(function ($q) use ($search, $operator) {
                $q->where('code', $operator, "%{$search}%")
                    ->orWhere('name', $operator, "%{$search}%")
                    ->orWhere('contact_person', $operator, "%{$search}%")
                    ->orWhere('phone', $operator, "%{$search}%")
                    ->orWhere('email', $operator, "%{$search}%");
            });
        }

        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        $this->applySorting($query, [
            'code' => 'vendors.code',
            'name' => 'vendors.name',
            'email' => 'vendors.email',
            'pic_name' => 'vendors.pic_name',
            'phone' => 'vendors.phone',
            'status' => 'vendors.status',
            'created_at' => 'vendors.created_at',
        ], $sortField, $sortDir, 'name');

        return $query->paginate($perPage);
    }

    public function create(array $data, array $kategoriItemIds = []): Vendor
    {
        $data['code'] = strtoupper($data['code']);

        return DB::transaction(function () use ($data, $kategoriItemIds) {
            $vendor = Vendor::create($data);
            $vendor->kategoriItems()->sync($kategoriItemIds);

            return $vendor;
        });
    }

    public function update(Vendor $vendor, array $data, ?array $kategoriItemIds = null): Vendor
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        return DB::transaction(function () use ($vendor, $data, $kategoriItemIds) {
            $vendor->update($data);

            if ($kategoriItemIds !== null) {
                $vendor->kategoriItems()->sync($kategoriItemIds);
            }

            return $vendor;
        });
    }

    public function delete(Vendor $vendor): void
    {
        // pengadaans.vendor_id = restrictOnDelete — blokir dgn pesan jelas.
        // Cek referensi FISIK: bypass global scope bisnis-area (boleh hapus
        // hanya bila tak ada pengadaan apa pun yang mereferensikan, termasuk
        // yang soft-deleted / di luar BA user).
        // Link kategori_item_vendor cascade bersih otomatis.
        $dipakai = Pengadaan::withTrashed()
            ->withoutGlobalScope('bisnis-area')
            ->where('vendor_id', $vendor->id)
            ->count();
        if ($dipakai > 0) {
            throw new \DomainException(
                "Vendor \"{$vendor->name}\" tidak dapat dihapus karena masih digunakan oleh {$dipakai} pengadaan."
            );
        }

        $vendor->delete();
    }

    public function toggleStatus(Vendor $vendor): Vendor
    {
        $newStatus = $vendor->status === VendorStatus::Aktif
            ? VendorStatus::Nonaktif
            : VendorStatus::Aktif;

        $vendor->update(['status' => $newStatus->value]);

        return $vendor;
    }

    public function getActiveVendors(): Collection
    {
        return Vendor::active()->orderBy('name')->get();
    }
}
