<?php

namespace App\Services;

use App\Enums\KategoriItemStatus;
use App\Models\KategoriItem;
use App\Traits\HasDynamicLike;
use App\Traits\SortsQuery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class KategoriItemService
{
    use HasDynamicLike, SortsQuery;

    public function getFiltered(
        ?string $search = null,
        ?string $statusFilter = null,
        int $perPage = 15,
        ?string $sortField = null,
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        $query = KategoriItem::withCount(['vendors', 'pengadaanItems']);

        if ($search) {
            $operator = $this->getLikeOperator();
            $query->where(function ($q) use ($search, $operator) {
                $q->where('code', $operator, "%{$search}%")
                    ->orWhere('name', $operator, "%{$search}%");
            });
        }

        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        $this->applySorting($query, [
            'code' => 'kategori_items.code',
            'name' => 'kategori_items.name',
            'email' => 'kategori_items.email',
            'pic_name' => 'kategori_items.pic_name',
            'phone' => 'kategori_items.phone',
            'status' => 'kategori_items.status',
            'created_at' => 'kategori_items.created_at',
        ], $sortField, $sortDir, 'name');

        return $query->paginate($perPage);
    }

    public function create(array $data): KategoriItem
    {
        $data['code'] = strtoupper($data['code']);

        return KategoriItem::create($data);
    }

    public function update(KategoriItem $kategoriItem, array $data): KategoriItem
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $kategoriItem->update($data);

        return $kategoriItem;
    }

    public function delete(KategoriItem $kategoriItem): void
    {
        // pengadaan_items.kategori_item_id = restrictOnDelete — blokir.
        $dipakaiItem = $kategoriItem->pengadaanItems()->count();
        if ($dipakaiItem > 0) {
            throw new \DomainException(
                "Kategori item \"{$kategoriItem->name}\" tidak dapat dihapus karena masih digunakan oleh {$dipakaiItem} item pengadaan."
            );
        }

        // Masih terhubung ke vendor (pivot kategori_item_vendor) — hapus
        // kategori dari vendor tsb dulu (incl. vendor soft-deleted, link-nya
        // masih ada & kembali aktif saat vendor di-restore).
        $dipakaiVendor = $kategoriItem->vendors()->withTrashed()->count();
        if ($dipakaiVendor > 0) {
            throw new \DomainException(
                "Kategori item \"{$kategoriItem->name}\" tidak dapat dihapus karena masih terhubung ke {$dipakaiVendor} vendor."
            );
        }

        $kategoriItem->delete();
    }

    public function toggleStatus(KategoriItem $kategoriItem): KategoriItem
    {
        $newStatus = $kategoriItem->status === KategoriItemStatus::Aktif
            ? KategoriItemStatus::Nonaktif
            : KategoriItemStatus::Aktif;

        $kategoriItem->update(['status' => $newStatus->value]);

        return $kategoriItem;
    }

    public function getActiveKategoriItems(): Collection
    {
        return KategoriItem::active()->orderBy('name')->get();
    }
}
