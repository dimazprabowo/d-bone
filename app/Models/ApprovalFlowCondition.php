<?php

namespace App\Models;

use App\Enums\ApprovalConditionOperator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalFlowCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_flow_id',
        'field',
        'operator',
        'value',
        'value2',
    ];

    protected function casts(): array
    {
        return [
            'operator' => ApprovalConditionOperator::class,
        ];
    }

    public function flow(): BelongsTo
    {
        return $this->belongsTo(ApprovalFlow::class, 'approval_flow_id');
    }

    public function evaluate(Model $model): bool
    {
        return $this->operator->evaluate(data_get($model, $this->field), $this->value, $this->value2);
    }
}
