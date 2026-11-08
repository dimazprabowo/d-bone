<?php

namespace App\Models;

use App\Enums\BisnisAreaStatus;
use App\Traits\Blameable;
use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BisnisArea extends Model
{
    use Blameable, HasEncryptedRouteKey, HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => BisnisAreaStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('bisnis_area');
    }

    public function pengadaans(): HasMany
    {
        return $this->hasMany(Pengadaan::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bisnis_area_user');
    }

    /**
     * Local-part email akun BA (sm.{slug}@ / admin.{slug}@): token pertama
     * nama di-lowercase — 'TRL'->'trl', 'TKDN & Pemerintahan'->'tkdn'.
     * Dipakai UserSeeder & ApprovalFlowSeeder agar mapping email konsisten.
     */
    public function emailLocalPart(): string
    {
        return Str::of($this->name)->before(' ')->lower()->ascii()->toString();
    }

    public function scopeActive($query)
    {
        return $query->where('status', BisnisAreaStatus::Aktif);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === BisnisAreaStatus::Aktif;
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
