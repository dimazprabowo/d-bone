<?php

namespace App\Services;

use App\Enums\KategoriItemStatus;
use App\Models\KategoriItem;
use App\Traits\HasDynamicLike;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class KategoriItemService
{
    use HasDynamicLike;

    public function getFiltered(
        ?string $search = null,
        ?string $statusFilter = null,
        int $perPage = 15,
        ?string $sortField = 'name',
        ?string $sortDirection = 'asc'
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

        $sortable = ['code', 'name', 'email', 'pic_name', 'phone', 'status', 'created_at'];
        $field = in_array($sortField, $sortable, true) ? $sortField : 'name';
        $direction = strtolower((string) $sortDirection) === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($field, $direction)->orderByDesc('id')->paginate($perPage);
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
