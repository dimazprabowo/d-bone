<?php

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ApprovalFlow extends Model
{
    use Blameable, HasEncryptedRouteKey, HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'entity_type',
        'description',
        'is_active',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(ApprovalFlowCondition::class);
    }

    /**
     * Flow cocok jika SEMUA kondisi terpenuhi (AND). Flow tanpa kondisi = fallback.
     */
    public function matches(Model $model): bool
    {
        return $this->conditions->every(fn ($c) => $c->evaluate($model));
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalFlowStep::class)->orderBy('position');
    }

    public function instances(): HasMany
    {
        return $this->hasMany(ApprovalInstance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEntity($query, string $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('approval_flow')
            ->logOnly(['name', 'entity_type', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
