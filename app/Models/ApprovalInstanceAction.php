<?php

namespace App\Models;

use App\Enums\ApprovalActionType;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalInstanceAction extends Model
{
    use Blameable, HasFactory;

    protected $fillable = [
        'approval_instance_id',
        'approval_instance_step_id',
        'user_id',
        'action',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'action' => ApprovalActionType::class,
        ];
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(ApprovalInstance::class, 'approval_instance_id');
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(ApprovalInstanceStep::class, 'approval_instance_step_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
