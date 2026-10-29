<?php

namespace App\Services;

use App\Models\ApprovalFlow;
use App\Traits\HasDynamicLike;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApprovalFlowService
{
    use HasDynamicLike;

    public function getFiltered(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return ApprovalFlow::query()
            ->withCount('steps')
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('name', $this->getLikeOperator(), "%{$s}%"))
            ->when($filters['entity_type'] ?? null, fn ($q, $e) => $q->forEntity($e))
            ->orderBy('priority')
            ->paginate($perPage);
    }

    public function create(array $data): ApprovalFlow
    {
        return DB::transaction(fn () => $this->persist(new ApprovalFlow, $data));
    }

    public function update(ApprovalFlow $flow, array $data): ApprovalFlow
    {
        return DB::transaction(fn () => $this->persist($flow, $data));
    }

    protected function persist(ApprovalFlow $flow, array $data): ApprovalFlow
    {
        if (empty($data['steps']) || collect($data['steps'])->contains(fn ($s) => empty($s['approver_ids']))) {
            throw ValidationException::withMessages([
                'steps' => 'Setiap tahap wajib memiliki minimal satu approver.',
            ]);
        }

        $flow->fill([
            'name' => $data['name'],
            'entity_type' => $data['entity_type'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'priority' => $data['priority'] ?? 1,
        ])->save();

        $flow->conditions()->delete();

        foreach ($data['conditions'] ?? [] as $condition) {
            $flow->conditions()->create([
                'field' => $condition['field'],
                'operator' => $condition['operator'],
                'value' => $condition['value'],
                'value2' => $condition['value2'] ?? null,
            ]);
        }

        $flow->steps()->delete();

        foreach ($data['steps'] as $i => $stepData) {
            $step = $flow->steps()->create([
                'position' => $i + 1,
                'name' => $stepData['name'],
                'mode' => $stepData['mode'],
            ]);

            $step->approvers()->sync(array_unique($stepData['approver_ids']));
        }

        return $flow->load('steps.approvers');
    }

    /**
     * Salin alur beserta kondisi, tahapan, dan approver ke alur baru
     * (nonaktif secara default agar tidak langsung mengambil alih dokumen baru).
     */
    public function duplicate(ApprovalFlow $flow): ApprovalFlow
    {
        return DB::transaction(function () use ($flow) {
            $flow->loadMissing('conditions', 'steps.approvers');

            $copy = ApprovalFlow::create([
                'name' => $flow->name.' (Salinan)',
                'entity_type' => $flow->entity_type,
                'description' => $flow->description,
                'is_active' => false,
                'priority' => (int) ApprovalFlow::forEntity($flow->entity_type)->max('priority') + 1,
            ]);

            foreach ($flow->conditions as $condition) {
                $copy->conditions()->create([
                    'field' => $condition->field,
                    'operator' => $condition->operator->value,
                    'value' => $condition->value,
                    'value2' => $condition->value2,
                ]);
            }

            foreach ($flow->steps as $step) {
                $newStep = $copy->steps()->create([
                    'position' => $step->position,
                    'name' => $step->name,
                    'mode' => $step->mode->value,
                ]);

                $newStep->approvers()->sync($step->approvers->pluck('id'));
            }

            return $copy;
        });
    }

    public function delete(ApprovalFlow $flow): void
    {
        if ($flow->instances()->exists()) {
            throw ValidationException::withMessages([
                'flow' => 'Alur tidak dapat dihapus karena sudah dipakai oleh dokumen.',
            ]);
        }

        $flow->delete();
    }

    /**
     * Opsi nilai untuk field kondisi tipe relation/enum — dipakai designer.
     */
    public function getConditionValueOptions(string $entityType): array
    {
        $options = [];

        foreach (config("approval.entities.{$entityType}.fields", []) as $field => $cfg) {
            if (($cfg['type'] ?? null) === 'relation') {
                $options[$field] = $cfg['model']::query()->orderBy('name')->pluck('name', 'id')->all();
            } elseif (($cfg['type'] ?? null) === 'enum') {
                $options[$field] = collect($cfg['options']::cases())->mapWithKeys(fn ($e) => [$e->value => $e->label()])->all();
            }
        }

        return $options;
    }
}
