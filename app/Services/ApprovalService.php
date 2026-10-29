<?php

namespace App\Services;

use App\Enums\ApprovalActionType;
use App\Enums\ApprovalInstanceStatus;
use App\Enums\ApprovalMode;
use App\Enums\ApprovalStepStatus;
use App\Models\ApprovalFlow;
use App\Models\ApprovalInstance;
use App\Models\ApprovalInstanceStep;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    /**
     * Buat snapshot alur persetujuan aktif untuk sebuah entity.
     * Snapshot beku: perubahan master flow setelah ini tidak mempengaruhi instance.
     */
    public function submit(Model $approvable, ?User $submitter = null): ?ApprovalInstance
    {
        $flow = $this->resolveFlow($approvable);

        if (! $flow || $flow->steps->isEmpty()) {
            return null;
        }

        return DB::transaction(function () use ($approvable, $flow, $submitter) {
            $submitterId = $submitter?->id ?? auth()->id();

            // Self-approval: jika pengaju adalah approver di suatu step, step
            // itu dan semua step di bawahnya dihapus dari snapshot — persetujuannya
            // implisit oleh aksi submit. Step terbawah menang bila pengaju ada
            // di lebih dari satu step.
            $steps = $flow->steps->values();
            $cutIndex = null;
            if ($submitterId) {
                foreach ($steps as $i => $step) {
                    if ($step->approvers->contains('id', $submitterId)) {
                        $cutIndex = $i;
                        break;
                    }
                }
            }
            $stepsToSnapshot = $cutIndex === null ? $steps : $steps->slice($cutIndex + 1)->values();
            $autoApproved = $stepsToSnapshot->isEmpty();

            $instance = $approvable->approvalInstances()->create([
                'approval_flow_id' => $flow->id,
                'flow_name' => $flow->name,
                'conditions_snapshot' => $flow->conditions->map(fn ($c) => [
                    'field' => $c->field,
                    'operator' => $c->operator->value,
                    'value' => $c->value,
                    'value2' => $c->value2,
                ])->all(),
                'status' => $autoApproved ? ApprovalInstanceStatus::Approved->value : ApprovalInstanceStatus::InProgress->value,
                'current_step' => $stepsToSnapshot->first()?->position ?? 1,
                'completed_at' => $autoApproved ? now() : null,
                'submitted_by' => $submitterId,
            ]);

            foreach ($stepsToSnapshot as $i => $step) {
                $instanceStep = $instance->steps()->create([
                    'position' => $step->position,
                    'name' => $step->name,
                    'mode' => $step->mode->value,
                    'status' => $i === 0 ? ApprovalStepStatus::Active->value : ApprovalStepStatus::Pending->value,
                ]);

                $instanceStep->approvers()->sync($step->approvers->pluck('id'));
            }

            $instance->actions()->create([
                'user_id' => $instance->submitted_by,
                'action' => ApprovalActionType::Submitted->value,
            ]);

            if ($autoApproved) {
                $this->onFinalized($instance->refresh());
            }

            return $instance;
        });
    }

    /**
     * Pilih flow: prioritas terendah dulu; kondisi kosong = fallback paling akhir.
     */
    public function resolveFlow(Model $approvable): ?ApprovalFlow
    {
        return ApprovalFlow::query()
            ->active()
            ->forEntity($approvable::approvalEntityType())
            ->with(['steps.approvers', 'conditions'])
            ->orderBy('priority')
            ->orderBy('id')
            ->get()
            ->sortBy(fn ($f) => $f->conditions->isEmpty() ? 1 : 0)
            ->first(fn ($f) => $f->matches($approvable));
    }

    public function canActOnCurrentStep(ApprovalInstance $instance, User $user): bool
    {
        if (! $instance->isInProgress()) {
            return false;
        }

        $step = $instance->currentStep()->with('approvers')->first();

        return $step
            && $step->approvers->contains('id', $user->id)
            && ! $this->hasUserActedOnStep($step, $user);
    }

    /**
     * User sudah bertindak (approve/reject) di step ini — tidak boleh dua kali.
     */
    protected function hasUserActedOnStep(ApprovalInstanceStep $step, User $user): bool
    {
        return $step->actions()
            ->where('user_id', $user->id)
            ->whereIn('action', [ApprovalActionType::Approved->value, ApprovalActionType::Rejected->value])
            ->exists();
    }

    public function getPendingStepFor(ApprovalInstance $instance): ?ApprovalInstanceStep
    {
        return $instance->currentStep()->with('approvers')->first();
    }

    /**
     * Setujui step aktif. Mode ANY: satu approve cukup. Mode ALL: semua harus approve.
     * Mengembalikan instance yang sudah di-refresh.
     */
    public function approve(ApprovalInstance $instance, User $user, ?string $note = null): ApprovalInstance
    {
        return DB::transaction(function () use ($instance, $user, $note) {
            $step = $instance->currentStep()->with('approvers')->first();
            abort_unless($step && $step->approvers->contains('id', $user->id), 403, 'Anda bukan approver pada tahap ini.');
            abort_if($this->hasUserActedOnStep($step, $user), 422, 'Anda sudah memberikan keputusan pada tahap ini.');

            $step->actions()->create([
                'approval_instance_id' => $instance->id,
                'user_id' => $user->id,
                'action' => ApprovalActionType::Approved->value,
                'note' => $note,
            ]);

            $approvedCount = $step->actions()
                ->where('action', ApprovalActionType::Approved->value)
                ->distinct('user_id')
                ->count('user_id');

            $isComplete = $step->mode === ApprovalMode::Any
                ? $approvedCount >= 1
                : $approvedCount >= $step->approvers->count();

            if ($isComplete) {
                $this->advanceStep($instance, $step);
            }

            return $instance->refresh();
        });
    }

    public function reject(ApprovalInstance $instance, User $user, string $reason): ApprovalInstance
    {
        return DB::transaction(function () use ($instance, $user, $reason) {
            $step = $instance->currentStep()->with('approvers')->first();
            abort_unless($step && $step->approvers->contains('id', $user->id), 403, 'Anda bukan approver pada tahap ini.');

            $step->actions()->create([
                'approval_instance_id' => $instance->id,
                'user_id' => $user->id,
                'action' => ApprovalActionType::Rejected->value,
                'note' => $reason,
            ]);

            $step->update(['status' => ApprovalStepStatus::Rejected->value]);
            $instance->update([
                'status' => ApprovalInstanceStatus::Rejected->value,
                'completed_at' => now(),
            ]);

            $this->onFinalized($instance);

            return $instance->refresh();
        });
    }

    /**
     * Ajukan ulang setelah ditolak: batalkan instance lama, snapshot ulang dari flow aktif terbaru.
     */
    public function resubmit(ApprovalInstance $oldInstance, ?User $submitter = null): ?ApprovalInstance
    {
        $approvable = $oldInstance->approvable;

        $oldInstance->update([
            'status' => ApprovalInstanceStatus::Cancelled->value,
        ]);

        $oldInstance->actions()->create([
            'user_id' => $submitter?->id ?? auth()->id(),
            'action' => ApprovalActionType::Cancelled->value,
        ]);

        return $this->submit($approvable, $submitter);
    }

    protected function advanceStep(ApprovalInstance $instance, ApprovalInstanceStep $step): void
    {
        $step->update(['status' => ApprovalStepStatus::Approved->value]);

        $nextStep = $instance->steps()->where('position', '>', $step->position)->first();

        if ($nextStep) {
            $nextStep->update(['status' => ApprovalStepStatus::Active->value]);
            $instance->update(['current_step' => $nextStep->position]);
        } else {
            $instance->update([
                'status' => ApprovalInstanceStatus::Approved->value,
                'completed_at' => now(),
            ]);
        }

        if (! $instance->isInProgress() || $nextStep === null) {
            $this->onFinalized($instance->refresh());
        }
    }

    /**
     * Hook saat instance selesai (approved/rejected) — entity update statusnya sendiri.
     */
    protected function onFinalized(ApprovalInstance $instance): void
    {
        $approvable = $instance->approvable;

        if (method_exists($approvable, 'onApprovalFinalized')) {
            $approvable->onApprovalFinalized($instance);
        }
    }

    public function getActiveInstance(Model $approvable): ?ApprovalInstance
    {
        return $approvable->approvalInstances()
            ->where('status', ApprovalInstanceStatus::InProgress->value)
            ->with('steps.approvers', 'steps.actions.user')
            ->latest()
            ->first();
    }

    public function getHistory(Model $approvable): Collection
    {
        return ApprovalInstance::query()
            ->where('approvable_type', $approvable->getMorphClass())
            ->where('approvable_id', $approvable->getKey())
            ->with('actions.user', 'actions.step')
            ->latest()
            ->get()
            ->flatMap(fn ($i) => $i->actions)
            ->sortBy('created_at')
            ->values();
    }
}
