<?php

namespace App\Models;

use App\Enums\ApprovalMode;
use App\Enums\ApprovalStepStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalInstanceStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_instance_id',
        'position',
        'name',
        'mode',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'mode' => ApprovalMode::class,
            'status' => ApprovalStepStatus::class,
        ];
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(ApprovalInstance::class, 'approval_instance_id');
    }

    public function approvers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'approval_instance_approvers', 'approval_instance_step_id', 'user_id')
            ->withTimestamps()
            ->orderBy('users.name');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(ApprovalInstanceAction::class)->oldest();
    }

    public function hasApproverApproved(int $userId): bool
    {
        return $this->actions->contains(fn ($a) => $a->user_id === $userId && $a->action->value === 'approved');
    }

    public function hasApproverRejected(int $userId): bool
    {
        return $this->actions->contains(fn ($a) => $a->user_id === $userId && $a->action->value === 'rejected');
    }
}
