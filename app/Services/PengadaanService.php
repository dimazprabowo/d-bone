<?php

namespace App\Services;

use App\Enums\PengadaanApprovalStatus;
use App\Enums\TipeBiaya;
use App\Helpers\ConfigHelper;
use App\Jobs\ProcessPengadaanEvidence;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Pengadaan;
use App\Models\PengadaanEvidence;
use App\Models\User;
use App\Traits\HasDynamicLike;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PengadaanService
{
    use HasDynamicLike;

    /**
     * Apply cabang scoping to a query based on the authenticated user.
     *
     * Users WITHOUT `access_all_cabang` permission only see pengadaan yang cabang_id-nya
     * cocok dengan cabang mereka sendiri. Users WITH the permission (Super Admin, Admin Pusat)
     * see all pengadaan — optionally narrowed by an explicit cabang filter.
     */
    protected function applyCabangScope($query, ?int $userCabangId, ?string $explicitCabangFilter = null): void
    {
        $canAccessAll = auth()->check() && auth()->user()->can('access_all_cabang');

        if (! $canAccessAll) {
            $query->where('cabang_id', $userCabangId);
        } elseif ($explicitCabangFilter !== null && $explicitCabangFilter !== '') {
            $query->where('cabang_id', $explicitCabangFilter);
        }
    }

    public function getFiltered(
        ?string $search = null,
        ?string $vendorFilter = null,
        ?string $cabangFilter = null,
        ?string $statusFilter = null,
        int $perPage = 15,
        string $sortField = 'tanggal_pesanan_spk',
        string $sortDirection = 'desc',
        array $filters = []
    ): LengthAwarePaginator {
        $query = Pengadaan::with(['vendor', 'cabang', 'bisnisArea', 'approver', 'invoices.payments'])->withCount(['items', 'evidences', 'invoices']);

        $this->applyListFilters($query, $search, $vendorFilter, $cabangFilter, $statusFilter, $filters);

        $direction = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        if ($sortField === 'saldo_belum_bayar' || $sortField === 'status_pembayaran') {
            $hasInv = 'EXISTS (SELECT 1 FROM invoices i0 WHERE i0.pengadaan_id = pengadaans.id)';
            $inv = '(SELECT COALESCE(SUM(i2.jumlah), 0) FROM invoices i2 WHERE i2.pengadaan_id = pengadaans.id)';
            $paid = '(SELECT COALESCE(SUM(ip.jumlah_bayar), 0) FROM invoice_payments ip INNER JOIN invoices i3 ON i3.id = ip.invoice_id WHERE i3.pengadaan_id = pengadaans.id)';
            $expr = $sortField === 'saldo_belum_bayar'
                ? "CASE WHEN NOT $hasInv THEN -1 ELSE ($inv - $paid) END"
                : "CASE WHEN NOT $hasInv THEN 0 WHEN $paid <= 0 THEN 1 WHEN $paid >= $inv THEN 3 ELSE 2 END";

            return $query->orderByRaw("$expr $direction")->orderByDesc('id')->paginate($perPage);
        }

        $sortable = ['no_pesanan_spk', 'tanggal_pesanan_spk', 'tanggal_jatuh_tempo', 'total_biaya', 'status_approval'];
        $field = in_array($sortField, $sortable, true) ? $sortField : 'tanggal_pesanan_spk';

        return $query->orderBy($field, $direction)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Terapkan seluruh filter list (scope cabang, search, vendor, status,
     * dan filter lanjutan) ke query — dipakai ulang oleh getFiltered
     * dan getFilteredStats.
     */
    protected function applyListFilters($query, ?string $search, ?string $vendorFilter, ?string $cabangFilter, ?string $statusFilter, array $filters): void
    {
        $userCabangId = auth()->check() ? auth()->user()->cabang_id : null;
        $this->applyCabangScope($query, $userCabangId, $cabangFilter);

        if ($search) {
            $operator = $this->getLikeOperator();
            $query->where(function ($q) use ($search, $operator) {
                $q->where('no_pesanan_spk', $operator, "%{$search}%")
                    ->orWhereHas('vendor', function ($v) use ($search, $operator) {
                        $v->where('name', $operator, "%{$search}%");
                    });
            });
        }

        if ($vendorFilter !== null && $vendorFilter !== '') {
            $query->where('vendor_id', $vendorFilter);
        }

        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('status_approval', $statusFilter);
        }

        $this->applyAdvancedFilters($query, $filters);
    }

    /**
     * Agregat ringkasan untuk stat cards di index — menghormati semua
     * filter/search yang sama dengan tabel.
     */
    public function getFilteredStats(
        ?string $search = null,
        ?string $vendorFilter = null,
        ?string $cabangFilter = null,
        ?string $statusFilter = null,
        array $filters = []
    ): array {
        $query = Pengadaan::query();
        $this->applyListFilters($query, $search, $vendorFilter, $cabangFilter, $statusFilter, $filters);

        $paid = '(SELECT COALESCE(SUM(ip.jumlah_bayar), 0) FROM invoice_payments ip INNER JOIN invoices i3 ON i3.id = ip.invoice_id WHERE i3.pengadaan_id = pengadaans.id)';
        $invoiced = '(SELECT COALESCE(SUM(i2.jumlah), 0) FROM invoices i2 WHERE i2.pengadaan_id = pengadaans.id)';

        // Financial stats: hanya untuk pengadaan APPROVED (konsisten dengan getDashboardStats).
        $row = $query->selectRaw('COUNT(*) as total')
            ->selectRaw("COALESCE(SUM(CASE WHEN status_approval = 'approved' THEN total_biaya ELSE 0 END), 0) as total_biaya")
            ->selectRaw("SUM(CASE WHEN status_approval = 'pending' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status_approval = 'approved' THEN 1 ELSE 0 END) as approved")
            ->selectRaw("SUM(CASE WHEN status_approval = 'rejected' THEN 1 ELSE 0 END) as rejected")
            // Alias diberi suffix _agg agar tidak tertimpa accessor model
            // (getSaldoBelumBayarAttribute / getTotalInvoiceAttribute).
            ->selectRaw("COALESCE(SUM(CASE WHEN status_approval = 'approved' THEN ($invoiced) ELSE 0 END), 0) as invoice_agg")
            ->selectRaw("COALESCE(SUM(CASE WHEN status_approval = 'approved' THEN ($paid) ELSE 0 END), 0) as dibayar_agg")
            ->selectRaw("COALESCE(SUM(CASE WHEN status_approval = 'approved' AND (total_biaya - ($paid)) > 0 THEN (total_biaya - ($paid)) ELSE 0 END), 0) as saldo_agg")
            ->selectRaw("COALESCE(SUM(CASE WHEN status_approval = 'approved' AND (total_biaya - ($invoiced)) > 0 THEN (total_biaya - ($invoiced)) ELSE 0 END), 0) as belum_ditagih_agg")
            ->first();

        $totalBiaya = (float) $row->total_biaya;
        $totalDibayar = (float) $row->dibayar_agg;

        return [
            'total' => (int) $row->total,
            'total_biaya' => $totalBiaya,
            'pending' => (int) $row->pending,
            'approved' => (int) $row->approved,
            'rejected' => (int) $row->rejected,
            'total_dibayar' => $totalDibayar,
            'belum_ditagih' => (float) $row->belum_ditagih_agg,
            'saldo_belum_bayar' => (float) $row->saldo_agg,
        ];
    }

    /**
     * Filter lanjutan: bisnis area, tipe biaya, status bayar, status jatuh
     * tempo, rentang tanggal SPK, dan rentang total biaya.
     */
    public function applyAdvancedFilters($query, array $filters): void
    {
        if (! empty($filters['bisnis_area_id'])) {
            $query->where('bisnis_area_id', $filters['bisnis_area_id']);
        }

        if (! empty($filters['tipe_biaya'])) {
            $query->where('tipe_biaya', $filters['tipe_biaya']);
        }

        if (! empty($filters['status_bayar'])) {
            $hasInv = 'EXISTS (SELECT 1 FROM invoices i0 WHERE i0.pengadaan_id = pengadaans.id)';
            $inv = '(SELECT COALESCE(SUM(i2.jumlah), 0) FROM invoices i2 WHERE i2.pengadaan_id = pengadaans.id)';
            $paid = '(SELECT COALESCE(SUM(ip.jumlah_bayar), 0) FROM invoice_payments ip INNER JOIN invoices i3 ON i3.id = ip.invoice_id WHERE i3.pengadaan_id = pengadaans.id)';
            $expr = "CASE WHEN NOT $hasInv THEN 0 WHEN $paid <= 0 THEN 1 WHEN $paid >= $inv THEN 3 ELSE 2 END";
            $map = ['belum_ditagih' => 0, 'belum_dibayar' => 1, 'sebagian' => 2, 'lunas' => 3];
            $query->whereRaw("$expr = ?", [$map[$filters['status_bayar']] ?? -1]);
        }

        if (! empty($filters['jatuh_tempo'])) {
            $warnDays = ConfigHelper::getJatuhTempoWarningDays();
            match ($filters['jatuh_tempo']) {
                'lewat' => $query->whereNotNull('tanggal_jatuh_tempo')->where('tanggal_jatuh_tempo', '<', today()),
                'proses' => $query->whereNotNull('tanggal_jatuh_tempo')->whereBetween('tanggal_jatuh_tempo', [today(), today()->addDays($warnDays)]),
                'aman' => $query->where(fn ($q) => $q->whereNull('tanggal_jatuh_tempo')->orWhere('tanggal_jatuh_tempo', '>', today()->addDays($warnDays))),
                default => null,
            };
        }

        if (! empty($filters['tanggal_dari'])) {
            $query->where('tanggal_pesanan_spk', '>=', $filters['tanggal_dari']);
        }

        if (! empty($filters['tanggal_sampai'])) {
            $query->where('tanggal_pesanan_spk', '<=', $filters['tanggal_sampai']);
        }

        if (! empty($filters['total_min'])) {
            $query->where('total_biaya', '>=', $filters['total_min']);
        }

        if (! empty($filters['total_max'])) {
            $query->where('total_biaya', '<=', $filters['total_max']);
        }
    }

    /**
     * Create a Pengadaan with nested items and evidences in a single transaction.
     *
     * @param  array<int, array{nama_item: string, kategori_item_id: ?int, qty: int, satuan_id: int, harga_satuan: float}>  $items
     * @param  array<int, array{name: string, temp_path: string, original_name: string}>  $evidences
     */
    public function create(array $data, array $items, array $evidences = []): Pengadaan
    {
        return DB::transaction(function () use ($data, $items, $evidences) {
            $data['no_pesanan_spk'] = strtoupper($data['no_pesanan_spk']);
            $isRab = ($data['tipe_biaya'] ?? null) === TipeBiaya::RabProject->value;
            $data['nama_project'] = $isRab ? ($data['nama_project'] ?? null) : null;
            $data['no_wbs'] = $isRab ? strtoupper($data['no_wbs'] ?? '') : null;
            $data['status_approval'] = PengadaanApprovalStatus::Pending->value;
            $data['total_biaya'] = 0;

            $pengadaan = Pengadaan::create($data);

            $total = $this->syncItems($pengadaan, $items);
            $pengadaan->update(['total_biaya' => $total]);

            foreach ($evidences as $evidence) {
                $this->createEvidence($pengadaan, $evidence);
            }

            app(ApprovalService::class)->submit($pengadaan);

            return $pengadaan->fresh(['items', 'evidences', 'vendor', 'cabang']);
        });
    }

    /**
     * Update a Pengadaan header + fully replace its items + apply evidence changes.
     *
     * @param  array<int, array{id: ?int, nama_item: string, kategori_item_id: ?int, qty: int, satuan_id: int, harga_satuan: float}>  $items
     * @param  array<int, array{name: string, temp_path: string, original_name: string}>  $newEvidences
     * @param  array<int>  $deletedEvidenceIds
     */
    public function update(Pengadaan $pengadaan, array $data, array $items, array $newEvidences = [], array $deletedEvidenceIds = []): Pengadaan
    {
        return DB::transaction(function () use ($pengadaan, $data, $items, $newEvidences, $deletedEvidenceIds) {
            if (isset($data['no_pesanan_spk'])) {
                $data['no_pesanan_spk'] = strtoupper($data['no_pesanan_spk']);
            }

            if (isset($data['tipe_biaya'])) {
                $isRab = $data['tipe_biaya'] === TipeBiaya::RabProject->value;
                $data['nama_project'] = $isRab ? ($data['nama_project'] ?? null) : null;
                $data['no_wbs'] = $isRab ? strtoupper($data['no_wbs'] ?? '') : null;
            }

            $pengadaan->update($data);

            $total = $this->syncItems($pengadaan, $items);
            $pengadaan->update(['total_biaya' => $total]);

            foreach ($deletedEvidenceIds as $evidenceId) {
                $this->deleteEvidence($evidenceId);
            }

            foreach ($newEvidences as $evidence) {
                $this->createEvidence($pengadaan, $evidence);
            }

            return $pengadaan->fresh(['items', 'evidences', 'vendor', 'cabang']);
        });
    }

    /**
     * Replace all items belonging to a Pengadaan with the given payload.
     * Computes subtotal per item and returns the new total_biaya.
     */
    protected function syncItems(Pengadaan $pengadaan, array $items): float
    {
        $pengadaan->items()->delete();

        $total = 0;
        foreach ($items as $item) {
            $qty = (int) ($item['qty'] ?? 1);
            $hargaSatuan = (float) ($item['harga_satuan'] ?? 0);
            $subtotal = $qty * $hargaSatuan;
            $total += $subtotal;

            $pengadaan->items()->create([
                'nama_item' => $item['nama_item'],
                'kategori_item_id' => $item['kategori_item_id'] ?? null,
                'qty' => $qty,
                'satuan_id' => $item['satuan_id'],
                'harga_satuan' => $hargaSatuan,
                'subtotal' => $subtotal,
            ]);
        }

        return $total;
    }

    public function delete(Pengadaan $pengadaan): void
    {
        DB::transaction(function () use ($pengadaan) {
            $fileStorage = app(FileStorageService::class);
            foreach ($pengadaan->evidences as $evidence) {
                $fileStorage->delete($evidence->file_path);
            }

            $pengadaan->evidences()->delete();
            $pengadaan->items()->delete();
            $pengadaan->delete();
        });
    }

    public function approve(Pengadaan $pengadaan, int $approverId, ?string $note = null): Pengadaan
    {
        $instance = $pengadaan->activeApprovalInstance();
        abort_unless($instance, 422, 'Tidak ada alur persetujuan aktif untuk pengadaan ini.');

        app(ApprovalService::class)->approve($instance, User::findOrFail($approverId), $note);

        return $pengadaan->fresh();
    }

    public function reject(Pengadaan $pengadaan, int $approverId, string $reason): Pengadaan
    {
        $instance = $pengadaan->activeApprovalInstance();
        abort_unless($instance, 422, 'Tidak ada alur persetujuan aktif untuk pengadaan ini.');

        app(ApprovalService::class)->reject($instance, User::findOrFail($approverId), $reason);

        return $pengadaan->fresh();
    }

    /**
     * Ajukan ulang pengadaan yang ditolak — snapshot alur terbaru dari master.
     */
    public function resubmit(Pengadaan $pengadaan, ?User $submitter = null): void
    {
        $instance = $pengadaan->approvalInstances()->latest()->first();

        if ($instance) {
            app(ApprovalService::class)->resubmit($instance, $submitter);
        } else {
            app(ApprovalService::class)->submit($pengadaan, $submitter);
        }

        $pengadaan->update([
            'status_approval' => PengadaanApprovalStatus::Pending->value,
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ]);
    }

    // ============================================================
    // Evidence Management
    // ============================================================

    protected function createEvidence(Pengadaan $pengadaan, array $evidence): PengadaanEvidence
    {
        $record = PengadaanEvidence::create([
            'pengadaan_id' => $pengadaan->id,
            'name' => $evidence['name'],
            'file_status' => 'processing',
        ]);

        ProcessPengadaanEvidence::dispatch(
            $record->id,
            $evidence['temp_path'],
            $evidence['original_name'],
            Str::slug($pengadaan->no_pesanan_spk)
        );

        return $record;
    }

    protected function deleteEvidence(int $evidenceId): void
    {
        $evidence = PengadaanEvidence::find($evidenceId);
        if (! $evidence) {
            return;
        }

        app(FileStorageService::class)->delete($evidence->file_path);
        $evidence->delete();
    }

    // ============================================================
    // Dashboard & Reporting
    // ============================================================

    /**
     * Ringkasan biaya, penagihan (invoice), dan pembayaran untuk dashboard.
     * Hanya Pengadaan berstatus Approved yang dihitung ke total biaya (data
     * belum-fix tidak mengotori laporan). Cabang-scoped.
     *
     * Field yang dikembalikan:
     * - total_pengadaan, total_biaya: agregat Pengadaan Approved.
     * - total_invoice, total_belum_ditagih: status penagihan.
     * - total_dibayar, total_outstanding, persentase_dibayar: status pembayaran.
     * - persentase_ditagih: progress penagihan terhadap total biaya.
     * - invoice_total, invoice_lunas, invoice_sebagian, invoice_belum_dibayar:
     *   breakdown status invoice.
     * - invoice_overdue, invoice_due_soon: invoice yang perlu perhatian.
     * - pengadaan_pending, pengadaan_rejected: action items approval.
     */
    public function getDashboardStats(): array
    {
        $today = now()->format('Y-m-d');
        $dueThreshold = now()->addDays(7)->format('Y-m-d');

        $userCabangId = auth()->check() ? auth()->user()->cabang_id : null;

        // Base query: approved pengadaan, cabang-scoped (untuk financial stats)
        $approvedQuery = function () use ($userCabangId) {
            $q = Pengadaan::query()->approved();
            $this->applyCabangScope($q, $userCabangId);

            return $q;
        };

        // Base query: ALL pengadaan, cabang-scoped (untuk status counts)
        $allQuery = function () use ($userCabangId) {
            $q = Pengadaan::query();
            $this->applyCabangScope($q, $userCabangId);

            return $q;
        };

        $totalBiaya = (float) $approvedQuery()->sum('total_biaya');
        $totalPengadaan = (int) $approvedQuery()->count();
        $pengadaanIds = $approvedQuery()->pluck('id');

        // Saldo & belum ditagih: clamp per-baris max(0, ...) agar konsisten
        // dengan accessor saldo_belum_bayar (overpay 1 dokumen tak mengurangi total).
        $paid = '(SELECT COALESCE(SUM(ip.jumlah_bayar), 0) FROM invoice_payments ip INNER JOIN invoices i3 ON i3.id = ip.invoice_id WHERE i3.pengadaan_id = pengadaans.id)';
        $invoiced = '(SELECT COALESCE(SUM(i2.jumlah), 0) FROM invoices i2 WHERE i2.pengadaan_id = pengadaans.id)';
        $saldoRow = $approvedQuery()
            ->selectRaw("COALESCE(SUM(CASE WHEN (total_biaya - ($paid)) > 0 THEN (total_biaya - ($paid)) ELSE 0 END), 0) as saldo")
            ->selectRaw("COALESCE(SUM(CASE WHEN (total_biaya - ($invoiced)) > 0 THEN (total_biaya - ($invoiced)) ELSE 0 END), 0) as belum_ditagih")
            ->first();

        // Invoice financial stats
        $totalInvoice = (float) Invoice::whereIn('pengadaan_id', $pengadaanIds)->sum('jumlah');
        $totalDibayar = (float) InvoicePayment::whereHas('invoice', function ($q) use ($pengadaanIds) {
            $q->whereIn('pengadaan_id', $pengadaanIds);
        })->sum('jumlah_bayar');

        // Invoice status breakdown (single query with subquery sum untuk efisiensi)
        $invoices = Invoice::whereIn('pengadaan_id', $pengadaanIds)
            ->withSum('payments as total_dibayar_approved', 'jumlah_bayar')
            ->get();

        $invoiceTotal = $invoices->count();
        $invoiceLunas = $invoices->filter(fn ($i) => (float) ($i->total_dibayar_approved ?? 0) >= (float) $i->jumlah && (float) ($i->total_dibayar_approved ?? 0) > 0)->count();
        $invoiceSebagian = $invoices->filter(fn ($i) => (float) ($i->total_dibayar_approved ?? 0) > 0 && (float) ($i->total_dibayar_approved ?? 0) < (float) $i->jumlah)->count();
        $invoiceBelumDibayar = $invoices->filter(fn ($i) => (float) ($i->total_dibayar_approved ?? 0) <= 0)->count();

        // Invoice due/overdue (dari collection yang sudah di-load, cegah N+1)
        $invoiceOverdue = $invoices->filter(fn ($i) => $i->jatuh_tempo && $i->jatuh_tempo->format('Y-m-d') < $today && (float) ($i->total_dibayar_approved ?? 0) < (float) $i->jumlah)->count();
        $invoiceDueSoon = $invoices->filter(fn ($i) => $i->jatuh_tempo && $i->jatuh_tempo->format('Y-m-d') >= $today && $i->jatuh_tempo->format('Y-m-d') <= $dueThreshold && (float) ($i->total_dibayar_approved ?? 0) < (float) $i->jumlah)->count();

        // Action items: pending approvals (cabang-scoped, semua status)
        $pengadaanTotal = (int) $allQuery()->count();
        $pengadaanPending = (int) $allQuery()->pending()->count();
        $pengadaanRejected = (int) $allQuery()->where('status_approval', PengadaanApprovalStatus::Rejected)->count();

        // Percentages (capped at 100 untuk progress bar)
        $persentaseDibayar = $totalInvoice > 0 ? min(100, round(($totalDibayar / $totalInvoice) * 100, 1)) : 0;
        $persentaseDitagih = $totalBiaya > 0 ? min(100, round(($totalInvoice / $totalBiaya) * 100, 1)) : 0;

        return [
            'total_pengadaan' => $totalPengadaan,
            'total_biaya' => $totalBiaya,
            'total_invoice' => $totalInvoice,
            'total_belum_ditagih' => (float) $saldoRow->belum_ditagih,
            'total_dibayar' => $totalDibayar,
            'total_outstanding' => (float) $saldoRow->saldo,
            'persentase_dibayar' => $persentaseDibayar,
            'persentase_ditagih' => $persentaseDitagih,
            'invoice_total' => $invoiceTotal,
            'invoice_lunas' => $invoiceLunas,
            'invoice_sebagian' => $invoiceSebagian,
            'invoice_belum_dibayar' => $invoiceBelumDibayar,
            'invoice_overdue' => $invoiceOverdue,
            'invoice_due_soon' => $invoiceDueSoon,
            'pengadaan_total' => $pengadaanTotal,
            'pengadaan_pending' => $pengadaanPending,
            'pengadaan_rejected' => $pengadaanRejected,
        ];
    }

    /**
     * Tren total biaya per bulan (Pengadaan Approved) untuk grafik dashboard.
     * Cabang-scoped, 12 bulan terakhir termasuk bulan berjalan.
     *
     * @return array<int, array{key: string, label: string, total: float, count: int}>
     */
    public function getMonthlyTrend(int $months = 12): array
    {
        $driver = DB::connection()->getDriverName();
        $monthExpr = match ($driver) {
            'sqlite' => "strftime('%Y-%m', tanggal_pesanan_spk)",
            'pgsql' => "to_char(tanggal_pesanan_spk, 'YYYY-MM')",
            default => "DATE_FORMAT(tanggal_pesanan_spk, '%Y-%m')",
        };

        $start = now()->startOfMonth()->subMonths($months - 1);

        $query = Pengadaan::query()->approved()
            ->where('tanggal_pesanan_spk', '>=', $start)
            ->selectRaw("{$monthExpr} as ym")
            ->selectRaw('SUM(total_biaya) as total')
            ->selectRaw('COUNT(*) as cnt')
            ->groupByRaw($monthExpr);

        $this->applyCabangScope($query, auth()->user()?->cabang_id);

        $rows = $query->get()->keyBy('ym');

        $result = [];
        for ($i = 0; $i < $months; $i++) {
            $period = $start->copy()->addMonths($i);
            $key = $period->format('Y-m');
            $row = $rows->get($key);

            $result[] = [
                'key' => $key,
                'label' => $period->translatedFormat('M'),
                'total' => (float) ($row->total ?? 0),
                'count' => (int) ($row->cnt ?? 0),
            ];
        }

        return $result;
    }

    /**
     * Top vendor by total spend (Pengadaan Approved), untuk dashboard.
     * Cabang-scoped seperti getDashboardStats().
     */
    public function getSpendPerVendor(int $limit = 5): array
    {
        $userCabangId = auth()->check() ? auth()->user()->cabang_id : null;

        $query = Pengadaan::query()->approved()
            ->join('vendors', 'pengadaans.vendor_id', '=', 'vendors.id')
            ->select('vendors.name as vendor_name')
            ->selectRaw('SUM(pengadaans.total_biaya) as total_spend')
            ->selectRaw('COUNT(*) as total_pengadaan');

        $this->applyCabangScope($query, $userCabangId);

        return $query->groupBy('vendors.id', 'vendors.name')
            ->orderByDesc('total_spend')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'vendor' => $row->vendor_name,
                'total_spend' => (float) $row->total_spend,
                'total_pengadaan' => (int) $row->total_pengadaan,
            ])
            ->toArray();
    }

    /**
     * Distribusi biaya pengadaan per cabang (Approved), untuk dashboard.
     * HANYA relevan untuk user dengan access_all_cabang.
     */
    public function getSpendPerCabang(): array
    {
        $canAccessAll = auth()->check() && auth()->user()->can('access_all_cabang');

        if (! $canAccessAll) {
            return [];
        }

        $rows = Pengadaan::query()->approved()
            ->join('cabangs', 'pengadaans.cabang_id', '=', 'cabangs.id')
            ->select('cabangs.name as cabang_name')
            ->selectRaw('SUM(pengadaans.total_biaya) as total_spend')
            ->selectRaw('COUNT(*) as total_pengadaan')
            ->groupBy('cabangs.id', 'cabangs.name')
            ->orderByDesc('total_spend')
            ->get();

        return $rows->map(fn ($row) => [
            'cabang' => $row->cabang_name,
            'total_spend' => (float) $row->total_spend,
            'total_pengadaan' => (int) $row->total_pengadaan,
        ])->toArray();
    }
}
