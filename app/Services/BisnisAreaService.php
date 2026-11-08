<?php

namespace App\Services;

use App\Enums\BisnisAreaStatus;
use App\Models\BisnisArea;
use App\Traits\HasDynamicLike;
use App\Traits\SortsQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BisnisAreaService
{
    use HasDynamicLike, SortsQuery;

    public function getFiltered(
        ?string $search = null,
        ?string $statusFilter = null,
        int $perPage = 15,
        ?string $sortField = null,
        string $sortDir = 'asc'
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

        $this->applySorting($query, [
            'code' => 'bisnis_areas.code',
            'name' => 'bisnis_areas.name',
            'email' => 'bisnis_areas.email',
            'pic_name' => 'bisnis_areas.pic_name',
            'phone' => 'bisnis_areas.phone',
            'status' => 'bisnis_areas.status',
            'created_at' => 'bisnis_areas.created_at',
        ], $sortField, $sortDir, 'name');

        return $query->paginate($perPage);
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
