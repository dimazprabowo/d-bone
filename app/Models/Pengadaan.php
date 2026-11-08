<?php

namespace App\Models;

use App\Enums\ApprovalInstanceStatus;
use App\Enums\JatuhTempoStatus;
use App\Enums\PengadaanApprovalStatus;
use App\Enums\StatusInvoicePengadaan;
use App\Enums\StatusPembayaranPengadaan;
use App\Enums\TipeBiaya;
use App\Helpers\ConfigHelper;
use App\Services\ApprovalService;
use App\Traits\Approvable;
use App\Traits\Blameable;
use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pengadaan extends Model
{
    use Approvable, Blameable, HasEncryptedRouteKey, HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'no_pesanan_spk',
        'nama_pemohon',
        'tipe_biaya',
        'nama_project',
        'no_wbs',
        'vendor_id',
        'bisnis_area_id',
        'cabang_id',
        'tanggal_pesanan_spk',
        'total_biaya',
        'status_approval',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pesanan_spk' => 'date',
        'total_biaya' => 'decimal:2',
        'status_approval' => PengadaanApprovalStatus::class,
        'tipe_biaya' => TipeBiaya::class,
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Jaga marker unique (no_pesanan_spk, deleted_spk_marker): record aktif
     * selalu '0' sehingga index menolak nomor duplikat; saat soft-delete diisi
     * id agar nomor bebas dipakai ulang. Dikelola via model event (bukan
     * generated column) karena MySQL melarang referensi kolom auto_increment.
     * Catatan: delete harus lewat model event ($pengadaan->delete()), bukan
     * bulk query-builder delete yang bypass event.
     */
    protected static function booted(): void
    {
        static::deleted(function (Pengadaan $pengadaan) {
            if (! $pengadaan->isForceDeleting()) {
                $pengadaan->newModelQuery()
                    ->whereKey($pengadaan->getKey())
                    ->update(['deleted_spk_marker' => (string) $pengadaan->getKey()]);
            }
        });

        static::restoring(function (Pengadaan $pengadaan) {
            $pengadaan->deleted_spk_marker = '0';
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'no_pesanan_spk', 'nama_pemohon', 'tipe_biaya', 'nama_project', 'no_wbs',
                'vendor_id',
                'bisnis_area_id', 'cabang_id', 'tanggal_pesanan_spk',
                'total_biaya', 'status_approval', 'rejection_reason',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('pengadaan');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class)->withTrashed();
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class)->withTrashed();
    }

    public function bisnisArea(): BelongsTo
    {
        return $this->belongsTo(BisnisArea::class)->withTrashed();
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Slug no pesanan/SPK untuk segmen path file.
     * Fallback ke id bila nomor kosong agar folder file tetap unik.
     */
    public function getSpkSlugAttribute(): string
    {
        return Str::slug($this->no_pesanan_spk ?? '') ?: 'pengadaan-'.$this->id;
    }

    public static function approvalEntityType(): string
    {
        return 'pengadaan';
    }

    public function onApprovalFinalized(ApprovalInstance $instance): void
    {
        if ($instance->status === ApprovalInstanceStatus::Approved) {
            $lastApprover = $instance->actions()->where('action', 'approved')->latest('id')->first();

            $this->update([
                'status_approval' => PengadaanApprovalStatus::Approved->value,
                'approved_by' => $lastApprover?->user_id,
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);
        } elseif ($instance->status === ApprovalInstanceStatus::Rejected) {
            $rejectAction = $instance->actions()->where('action', 'rejected')->latest('id')->first();

            $this->update([
                'status_approval' => PengadaanApprovalStatus::Rejected->value,
                'approved_by' => $rejectAction?->user_id,
                'approved_at' => now(),
                'rejection_reason' => $rejectAction?->note,
            ]);
        }
    }

    public function activeApprovalInstance(): ?ApprovalInstance
    {
        return $this->approvalInstances()
            ->where('status', ApprovalInstanceStatus::InProgress->value)
            ->latest()
            ->first();
    }

    /**
     * Apakah user bisa approve/reject sekarang.
     * Wajib cek engine langsung — Gate::before (super admin) bypass policy
     * sehingga @can('approve') saja tidak cukup untuk menyembunyikan tombol.
     */
    public function userCanActOnApproval(User $user): bool
    {
        $instance = $this->activeApprovalInstance();

        return $instance
            && $user->can('approve', $this)
            && app(ApprovalService::class)->canActOnCurrentStep($instance, $user);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PengadaanItem::class);
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(PengadaanEvidence::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->orderBy('tanggal_invoice', 'desc');
    }

    public function scopeApproved($query)
    {
        return $query->where('status_approval', PengadaanApprovalStatus::Approved);
    }

    public function scopePending($query)
    {
        return $query->where('status_approval', PengadaanApprovalStatus::Pending);
    }

    public function isPending(): bool
    {
        return $this->status_approval === PengadaanApprovalStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status_approval === PengadaanApprovalStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status_approval === PengadaanApprovalStatus::Rejected;
    }

    /**
     * Total invoice yang sudah diterbitkan vendor untuk pengadaan ini.
     */
    public function getTotalInvoiceAttribute(): float
    {
        if ($this->relationLoaded('invoices')) {
            return (float) $this->invoices->sum('jumlah');
        }

        return (float) $this->invoices()->sum('jumlah');
    }

    /**
     * Status invoice derived: belum ditagih / ditagih sebagian / sudah ditagih penuh.
     */
    public function getStatusInvoiceAttribute(): StatusInvoicePengadaan
    {
        $totalInvoice = $this->total_invoice;

        if ($totalInvoice <= 0) {
            return StatusInvoicePengadaan::BelumDitagih;
        }

        if ($totalInvoice < (float) $this->total_biaya) {
            return StatusInvoicePengadaan::DitagihSebagian;
        }

        return StatusInvoicePengadaan::SudahDitagihPenuh;
    }

    /**
     * Status pembayaran derived, agregat dari status pembayaran semua invoice
     * milik pengadaan ini.
     */
    public function getSaldoBelumBayarAttribute(): float
    {
        $invoices = $this->relationLoaded('invoices') ? $this->invoices : $this->invoices()->with('payments')->get();

        return max(0.0, (float) $this->total_biaya - $invoices->sum('total_dibayar'));
    }

    /**
     * Status jatuh tempo terburuk dari invoice yang belum lunas
     * (Lewat > Proses > Aman). Null bila tidak ada invoice aktif
     * atau alert dinonaktifkan di konfigurasi.
     */
    public function getJatuhTempoStatusAttribute(): ?JatuhTempoStatus
    {
        if (! ConfigHelper::isJatuhTempoWarningEnabled()) {
            return null;
        }

        $invoices = $this->relationLoaded('invoices')
            ? $this->invoices
            : $this->invoices()->with('payments')->get();

        $statuses = $invoices->map->jatuh_tempo_status->filter();

        if ($statuses->isEmpty()) {
            return null;
        }

        if ($statuses->contains(JatuhTempoStatus::Lewat)) {
            return JatuhTempoStatus::Lewat;
        }

        return $statuses->contains(JatuhTempoStatus::Proses)
            ? JatuhTempoStatus::Proses
            : JatuhTempoStatus::Aman;
    }

    public function getStatusPembayaranAttribute(): StatusPembayaranPengadaan
    {
        $invoices = $this->relationLoaded('invoices') ? $this->invoices : $this->invoices()->with('payments')->get();

        if ($invoices->isEmpty()) {
            return StatusPembayaranPengadaan::BelumDibayar;
        }

        $totalJumlah = $invoices->sum('jumlah');
        $totalDibayar = $invoices->sum('total_dibayar');

        if ($totalDibayar <= 0) {
            return StatusPembayaranPengadaan::BelumDibayar;
        }

        if ($totalDibayar >= $totalJumlah) {
            return StatusPembayaranPengadaan::Lunas;
        }

        return StatusPembayaranPengadaan::Sebagian;
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
