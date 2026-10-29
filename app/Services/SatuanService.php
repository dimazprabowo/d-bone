<?php

namespace App\Services;

use App\Enums\SatuanStatus;
use App\Models\Satuan;
use App\Traits\HasDynamicLike;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SatuanService
{
    use HasDynamicLike;

    public function getFiltered(
        ?string $search = null,
        ?string $statusFilter = null,
        int $perPage = 15,
        ?string $sortField = 'name',
        ?string $sortDirection = 'asc'
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

        $sortable = ['code', 'name', 'email', 'pic_name', 'phone', 'status', 'created_at'];
        $field = in_array($sortField, $sortable, true) ? $sortField : 'name';
        $direction = strtolower((string) $sortDirection) === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($field, $direction)->orderByDesc('id')->paginate($perPage);
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
