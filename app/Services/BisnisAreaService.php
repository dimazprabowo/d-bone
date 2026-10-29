<?php

namespace App\Services;

use App\Enums\BisnisAreaStatus;
use App\Models\BisnisArea;
use App\Traits\HasDynamicLike;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BisnisAreaService
{
    use HasDynamicLike;

    public function getFiltered(
        ?string $search = null,
        ?string $statusFilter = null,
        int $perPage = 15,
        ?string $sortField = 'name',
        ?string $sortDirection = 'asc'
    ): LengthAwarePaginator {
        $query = BisnisArea::withCount('pengadaans');

        if ($search) {
            $operator = $this->getLikeOperator();
            $query->where(function ($q) use ($search, $operator) {
                $q->where('name', $operator, "%{$search}%")
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

    public function create(array $data): BisnisArea
    {
        return BisnisArea::create($data);
    }

    public function update(BisnisArea $bisnisArea, array $data): BisnisArea
    {
        $bisnisArea->update($data);

        return $bisnisArea;
    }

    public function delete(BisnisArea $bisnisArea): void
    {
        $bisnisArea->delete();
    }

    public function toggleStatus(BisnisArea $bisnisArea): BisnisArea
    {
        $newStatus = $bisnisArea->status === BisnisAreaStatus::Aktif
            ? BisnisAreaStatus::Nonaktif
            : BisnisAreaStatus::Aktif;

        $bisnisArea->update(['status' => $newStatus->value]);

        return $bisnisArea;
    }

    public function getActiveBisnisAreas(): Collection
    {
        return BisnisArea::active()->orderBy('name')->get();
    }
}
