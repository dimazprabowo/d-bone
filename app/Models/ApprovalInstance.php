<?php

namespace App\Models;

use App\Enums\ApprovalInstanceStatus;
use App\Enums\ApprovalStepStatus;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApprovalInstance extends Model
{
    use Blameable, HasFactory;

    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'approval_flow_id',
        'flow_name',
        'conditions_snapshot',
        'status',
        'current_step',
        'submitted_by',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApprovalInstanceStatus::class,
            'conditions_snapshot' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function flow(): BelongsTo
    {
        return $this->belongsTo(ApprovalFlow::class, 'approval_flow_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalInstanceStep::class)->orderBy('position');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(ApprovalInstanceAction::class)->oldest();
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function currentStep(): HasMany
    {
        return $this->steps()->where('position', $this->current_step)->where('status', ApprovalStepStatus::Active->value);
    }

    public function isInProgress(): bool
    {
        return $this->status === ApprovalInstanceStatus::InProgress;
    }
}
