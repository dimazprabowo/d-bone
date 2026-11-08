<?php

namespace App\Services;

use App\Enums\SatuanStatus;
use App\Models\Satuan;
use App\Traits\HasDynamicLike;
use App\Traits\SortsQuery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SatuanService
{
    use HasDynamicLike, SortsQuery;

    public function getFiltered(
        ?string $search = null,
        ?string $statusFilter = null,
        int $perPage = 15,
        ?string $sortField = null,
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        $query = Satuan::withCount('pengadaanItems');

        if ($search) {
            $operator = $this->getLikeOperator();
            $query->where(function ($q) use ($search, $operator) {
                $q->where('code', $operator, "%{$search}%")
                    ->orWhere('name', $operator, "%{$search}%")
                    ->orWhere('description', $operator, "%{$search}%");
            });
        }

        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        $this->applySorting($query, [
            'code' => 'satuans.code',
            'name' => 'satuans.name',
            'email' => 'satuans.email',
            'pic_name' => 'satuans.pic_name',
            'phone' => 'satuans.phone',
            'status' => 'satuans.status',
            'created_at' => 'satuans.created_at',
        ], $sortField, $sortDir, 'name');

        return $query->paginate($perPage);
    }

    public function create(array $data): Satuan
    {
        $data['code'] = strtoupper($data['code']);

        return Satuan::create($data);
    }

    public function update(Satuan $satuan, array $data): Satuan
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $satuan->update($data);

        return $satuan;
    }

    public function delete(Satuan $satuan): void
    {
        // pengadaan_items.satuan_id = restrictOnDelete — blokir dgn pesan jelas.
        $dipakai = $satuan->pengadaanItems()->count();
        if ($dipakai > 0) {
            throw new \DomainException(
                "Satuan \"{$satuan->name}\" tidak dapat dihapus karena masih digunakan oleh {$dipakai} item pengadaan."
            );
        }

        $satuan->delete();
    }

    public function toggleStatus(Satuan $satuan): Satuan
    {
        $newStatus = $satuan->status === SatuanStatus::Aktif
            ? SatuanStatus::Nonaktif
            : SatuanStatus::Aktif;

        $satuan->update(['status' => $newStatus->value]);

        return $satuan;
    }

    public function getActiveSatuans(): Collection
    {
        return Satuan::active()->orderBy('name')->get();
    }
}
