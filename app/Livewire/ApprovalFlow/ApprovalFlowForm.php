<?php

namespace App\Livewire\ApprovalFlow;

use App\Enums\ApprovalConditionOperator;
use App\Livewire\Traits\HasNotification;
use App\Models\ApprovalFlow;
use App\Models\User;
use App\Services\ApprovalFlowService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ApprovalFlowForm extends Component
{
    use HasNotification;

    public $editMode = false;

    public $flowId;

    public $name = '';

    public $entity_type = 'pengadaan';

    public $description = '';

    public $is_active = true;

    public $steps = [];

    public $conditions = [];

    public $priority = 1;

    public $approverSearch = '';

    public function mount(?ApprovalFlow $approvalFlow = null): void
    {
        if ($approvalFlow?->exists) {
            $this->authorize('update', $approvalFlow);
            $this->editMode = true;
            $this->flowId = $approvalFlow->id;
            $this->name = $approvalFlow->name;
            $this->entity_type = $approvalFlow->entity_type;
            $this->description = $approvalFlow->description ?? '';
            $this->is_active = $approvalFlow->is_active;
            $this->priority = $approvalFlow->priority;
            $this->conditions = $approvalFlow->conditions->map(fn ($c) => [
                'field' => $c->field,
                'operator' => $c->operator->value,
                'value' => $c->value,
                'value2' => $c->value2,
            ])->all();
            $this->steps = $approvalFlow->steps->map(fn ($s) => [
                'name' => $s->name,
                'mode' => $s->mode->value,
                'approver_ids' => $s->approvers->pluck('id')->all(),
            ])->all();
        } else {
            $this->authorize('create', ApprovalFlow::class);
            $this->steps = [['name' => '', 'mode' => 'any', 'approver_ids' => []]];
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'entity_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'priority' => 'required|integer|min:1|max:9999',
            'conditions' => 'nullable|array',
            'conditions.*.field' => 'required|string',
            'conditions.*.operator' => 'required|string',
            'conditions.*.value' => 'required|string|max:255',
            'conditions.*.value2' => 'nullable|string|max:255',
            'steps' => 'required|array|min:1',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.mode' => 'required|in:any,all',
            'steps.*.approver_ids' => 'required|array|min:1',
            'steps.*.approver_ids.*' => 'exists:users,id',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => 'nama alur',
            'entity_type' => 'entity',
            'description' => 'deskripsi',
            'steps.*.name' => 'nama tahap',
            'steps.*.mode' => 'mode tahap',
            'steps.*.approver_ids' => 'approver',
            'priority' => 'prioritas',
            'conditions.*.field' => 'field kondisi',
            'conditions.*.operator' => 'operator kondisi',
            'conditions.*.value' => 'nilai kondisi',
            'conditions.*.value2' => 'nilai kondisi kedua',
        ];
    }

    public function addCondition(): void
    {
        $this->conditions[] = ['field' => '', 'operator' => '=', 'value' => '', 'value2' => null];
    }

    public function removeCondition(int $index): void
    {
        array_splice($this->conditions, $index, 1);
    }

    public function updatedConditions($value, $key): void
    {
        [$index, $prop] = explode('.', $key);

        if ($prop === 'field') {
            $type = $this->conditionFields[$value]['type'] ?? 'number';
            $this->conditions[$index]['operator'] = ApprovalConditionOperator::forFieldType($type)[0]->value;
            $this->conditions[$index]['value'] = '';
            $this->conditions[$index]['value2'] = null;
        }
    }

    /** Alur lain yang memakai prioritas sama — untuk warning duplikat. */
    public function getPriorityConflictProperty()
    {
        if (! $this->priority) {
            return null;
        }

        return ApprovalFlow::forEntity($this->entity_type)
            ->where('priority', $this->priority)
            ->when($this->flowId, fn ($q) => $q->where('id', '!=', $this->flowId))
            ->first();
    }

    public function getConditionFieldsProperty(): array
    {
        return config("approval.entities.{$this->entity_type}.fields", []);
    }

    public function getConditionValueOptionsProperty(): array
    {
        return app(ApprovalFlowService::class)->getConditionValueOptions($this->entity_type);
    }

    public function getOperatorsForProperty(): array
    {
        return ApprovalConditionOperator::cases();
    }

    public function operatorsForField(string $field): array
    {
        return ApprovalConditionOperator::forFieldType($this->conditionFields[$field]['type'] ?? 'select');
    }

    public function addStep(): void
    {
        $this->steps[] = ['name' => '', 'mode' => 'any', 'approver_ids' => []];
    }

    public function removeStep(int $index): void
    {
        if (count($this->steps) <= 1) {
            $this->notifyWarning('Minimal harus ada satu tahap.');

            return;
        }

        array_splice($this->steps, $index, 1);
    }

    public function moveStep(int $index, int $direction): void
    {
        $target = $index + $direction;

        if ($target < 0 || $target >= count($this->steps)) {
            return;
        }

        [$this->steps[$index], $this->steps[$target]] = [$this->steps[$target], $this->steps[$index]];
    }

    public function addApprover(int $stepIndex, int $userId): void
    {
        if (! isset($this->steps[$stepIndex]) || in_array($userId, $this->steps[$stepIndex]['approver_ids'])) {
            return;
        }

        $this->steps[$stepIndex]['approver_ids'][] = $userId;
    }

    public function removeApprover(int $stepIndex, int $userId): void
    {
        $this->steps[$stepIndex]['approver_ids'] = array_values(
            array_filter($this->steps[$stepIndex]['approver_ids'], fn ($id) => $id !== $userId)
        );
    }

    public function getUserPoolProperty()
    {
        $query = User::query()->active()->orderBy('name');

        if ($this->approverSearch) {
            $query->where('name', 'like', "%{$this->approverSearch}%");
        }

        return $query->get(['id', 'name', 'email']);
    }

    public function getUsersByIdProperty()
    {
        $ids = collect($this->steps)->flatMap(fn ($s) => $s['approver_ids'])->unique();

        return User::whereIn('id', $ids)->get(['id', 'name', 'email'])->keyBy('id');
    }

    public function save(ApprovalFlowService $service): void
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $flow = ApprovalFlow::findOrFail($this->flowId);
                $this->authorize('update', $flow);
                $service->update($flow, $this->payload());
                $this->notifySuccess('Alur persetujuan berhasil diperbarui.');
            } else {
                $this->authorize('create', ApprovalFlow::class);
                $service->create($this->payload());
                $this->notifySuccess('Alur persetujuan berhasil dibuat.');
            }

            $this->redirect(route('approval-flows.index'), navigate: true);
        } catch (AuthorizationException) {
            $this->notifyError('Anda tidak memiliki izin untuk aksi ini.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            report($e);
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    protected function payload(): array
    {
        return [
            'name' => $this->name,
            'entity_type' => $this->entity_type,
            'description' => $this->description ?: null,
            'is_active' => $this->is_active,
            'priority' => $this->priority,
            'conditions' => array_values(array_filter($this->conditions, fn ($c) => $c['field'] !== '')),
            'steps' => $this->steps,
        ];
    }

    public function cancel(): void
    {
        $this->redirect(route('approval-flows.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.approval-flow.approval-flow-form');
    }
}
