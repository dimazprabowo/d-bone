<?php

namespace App\Models;

use App\Enums\ApprovalMode;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ApprovalFlowStep extends Model
{
    use Blameable, HasFactory;

    protected $fillable = [
        'approval_flow_id',
        'position',
        'name',
        'mode',
    ];

    protected function casts(): array
    {
        return [
            'mode' => ApprovalMode::class,
        ];
    }

    public function flow(): BelongsTo
    {
        return $this->belongsTo(ApprovalFlow::class, 'approval_flow_id');
    }

    public function approvers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'approval_flow_step_approvers', 'approval_flow_step_id', 'user_id')
            ->withTimestamps()
            ->orderBy('users.name');
    }
}
