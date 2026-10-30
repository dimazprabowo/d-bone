<div>
    {{-- Stat Cards: ringkasan mengikuti filter/search aktif --}}
    <div class="mb-6 space-y-4">
        {{-- Baris 1: Status pengadaan --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Pengadaan</p>
                    <p class="mt-1.5 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Dokumen terfilter</p>
                </div>
                <div class="p-2.5 bg-blue-50 dark:bg-blue-900/20 rounded-xl shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Disetujui</p>
                    <p class="mt-1.5 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['approved']) }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Pengadaan disetujui</p>
                </div>
                <div class="p-2.5 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl shrink-0">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Menunggu</p>
                    <p class="mt-1.5 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ number_format($stats['pending']) }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Menunggu persetujuan</p>
                </div>
                <div class="p-2.5 bg-amber-50 dark:bg-amber-900/20 rounded-xl shrink-0">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ditolak</p>
                    <p class="mt-1.5 text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['rejected']) }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Pengadaan ditolak</p>
                </div>
                <div class="p-2.5 bg-red-50 dark:bg-red-900/20 rounded-xl shrink-0">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        </div>

        {{-- Baris 2: Finansial --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Biaya</p>
                        <p class="mt-1.5 text-xl font-bold text-gray-900 dark:text-white truncate" title="Rp {{ number_format($stats['total_biaya'], 0, ',', '.') }}">Rp {{ number_format($stats['total_biaya'], 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Akumulasi biaya disetujui</p>
                    </div>
                    <div class="p-2.5 bg-blue-50 dark:bg-blue-900/20 rounded-xl shrink-0">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sudah Dibayar</p>
                        <p class="mt-1.5 text-xl font-bold text-emerald-600 dark:text-emerald-400 truncate" title="Rp {{ number_format($stats['total_dibayar'], 0, ',', '.') }}">Rp {{ number_format($stats['total_dibayar'], 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Akumulasi pembayaran</p>
                    </div>
                    <div class="p-2.5 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl shrink-0">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sisa Pembayaran</p>
                        <p class="mt-1.5 text-xl font-bold text-orange-600 dark:text-orange-400 truncate" title="Rp {{ number_format($stats['saldo_belum_bayar'], 0, ',', '.') }}">Rp {{ number_format($stats['saldo_belum_bayar'], 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Saldo belum dibayar</p>
                    </div>
                    <div class="p-2.5 bg-orange-50 dark:bg-orange-900/20 rounded-xl shrink-0">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Belum Ditagih</p>
                        <p class="mt-1.5 text-xl font-bold text-amber-600 dark:text-amber-400 truncate" title="Rp {{ number_format($stats['belum_ditagih'], 0, ',', '.') }}">Rp {{ number_format($stats['belum_ditagih'], 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Belum ada invoice</p>
                    </div>
                    <div class="p-2.5 bg-amber-50 dark:bg-amber-900/20 rounded-xl shrink-0">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex-1 w-full sm:w-auto">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari no. pesanan/SPK / vendor..."
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
        </div>

        <x-filter-popover width="w-72 sm:w-[30rem] lg:w-[42rem]" columns="3" :filters="['vendorFilter', 'cabangFilter', 'statusFilter', 'bisnisAreaFilter', 'tipeBiayaFilter', 'statusBayarFilter', 'jatuhTempoFilter', 'tanggalDari', 'tanggalSampai', 'totalMin', 'totalMax']">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Vendor</label>
                <x-searchable-select wire:model.live="vendorFilter" :options="$this->vendorOptions" placeholder="Semua Vendor" searchPlaceholder="Cari vendor..." />
            </div>
            @can('access_all_cabang')
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Cabang</label>
                    <x-searchable-select wire:model.live="cabangFilter" :options="$this->cabangOptions" placeholder="Semua Cabang" searchPlaceholder="Cari cabang..." />
                </div>
            @endcan
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Bisnis Area</label>
                <x-searchable-select wire:model.live="bisnisAreaFilter" :options="$this->bisnisAreaOptions" placeholder="Semua Bisnis Area" searchPlaceholder="Cari bisnis area..." />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tipe Biaya</label>
                <x-searchable-select wire:model.live="tipeBiayaFilter" :options="$this->tipeBiayaOptions" placeholder="Semua Tipe" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status Approval</label>
                <x-searchable-select wire:model.live="statusFilter" :options="$this->statusOptions" placeholder="Semua Status" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status Pembayaran</label>
                <x-searchable-select wire:model.live="statusBayarFilter" :options="$this->statusBayarOptions" placeholder="Semua Status" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Jatuh Tempo</label>
                <x-searchable-select wire:model.live="jatuhTempoFilter" :options="$this->jatuhTempoOptions" placeholder="Semua" />
            </div>
            <div class="sm:col-span-2 lg:col-span-3 border-t border-gray-200 dark:border-gray-700 pt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Rentang Tanggal SPK</label>
                    <div class="grid grid-cols-2 gap-2">
                        <x-date-picker wire:model.live="tanggalDari" placeholder="Dari" />
                        <x-date-picker wire:model.live="tanggalSampai" placeholder="Sampai" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Rentang Total Biaya</label>
                    <div class="grid grid-cols-2 gap-2">
                        <x-rupiah-input wireModel="totalMin" placeholder="Min" />
                        <x-rupiah-input wireModel="totalMax" placeholder="Maks" />
                    </div>
                </div>
            </div>
        </x-filter-popover>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            @can('pengadaan_export_excel')
                <x-loading-button wire:click="exportExcel" target="exportExcel" wire:key="btn-export-excel" variant="success" size="md" loadingText="Exporting..." title="Export Excel">
                    <x-slot:icon><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></x-slot:icon>
                    Excel
                </x-loading-button>
            @endcan
            @can('pengadaan_export_pdf')
                <x-loading-button wire:click="exportPdf" target="exportPdf" wire:key="btn-export-pdf" variant="danger" size="md" loadingText="Exporting..." title="Export PDF">
                    <x-slot:icon><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></x-slot:icon>
                    PDF
                </x-loading-button>
            @endcan
            @can('pengadaan_create')
                <x-loading-button wire:click="create" target="create" wire:key="btn-create" variant="primary" size="md" loadingText="Memuat..." class="flex-1 sm:flex-none">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></x-slot:icon>
                    Tambah Pengadaan
                </x-loading-button>
            @endcan
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <x-sortable-th field="status_approval" :sort-field="$sortField" :sort-direction="$sortDirection">Status</x-sortable-th>
                        <x-sortable-th field="no_pesanan_spk" :sort-field="$sortField" :sort-direction="$sortDirection">No. Pesanan/SPK</x-sortable-th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bisnis Area</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cabang</th>
                        <x-sortable-th field="tanggal_pesanan_spk" :sort-field="$sortField" :sort-direction="$sortDirection">Tgl. Pesanan/SPK</x-sortable-th>
                        <x-sortable-th field="tanggal_jatuh_tempo" :sort-field="$sortField" :sort-direction="$sortDirection">Jatuh Tempo</x-sortable-th>
                        <x-sortable-th field="total_biaya" :sort-field="$sortField" :sort-direction="$sortDirection" align="right">Total Biaya</x-sortable-th>
                        <x-sortable-th field="saldo_belum_bayar" :sort-field="$sortField" :sort-direction="$sortDirection" align="right">Saldo Belum Bayar</x-sortable-th>
                        <x-sortable-th field="status_pembayaran" :sort-field="$sortField" :sort-direction="$sortDirection">Status Bayar</x-sortable-th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pengadaans as $pengadaan)
                        <tr wire:key="pengadaan-{{ $pengadaan->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="openStatusHistory({{ $pengadaan->id }})"
                                    wire:loading.attr="disabled" wire:target="openStatusHistory({{ $pengadaan->id }})"
                                    wire:key="btn-status-{{ $pengadaan->id }}"
                                    class="px-2 py-1 text-xs font-medium rounded-full {{ $pengadaan->status_approval->badgeClass() }} cursor-pointer hover:ring-2 hover:ring-offset-1 hover:ring-gray-400 dark:hover:ring-gray-500 transition-all disabled:opacity-50"
                                    title="Lihat riwayat persetujuan">
                                    {{ $pengadaan->status_approval->label() }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $pengadaan->no_pesanan_spk }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $pengadaan->items_count }} item, {{ $pengadaan->evidences_count }} evidence</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $pengadaan->vendor->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">{{ $pengadaan->bisnisArea->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $pengadaan->cabang->name ?? 'Pusat' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $pengadaan->tanggal_pesanan_spk->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm {{ $pengadaan->jatuh_tempo_alert?->textClass() ?? 'text-gray-900 dark:text-white' }}">{{ $pengadaan->tanggal_jatuh_tempo?->format('d/m/Y') ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($pengadaan->total_biaya, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if($pengadaan->invoices_count === 0)
                                    <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                @else
                                    <div class="text-sm font-semibold {{ $pengadaan->saldo_belum_bayar > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                        Rp {{ number_format($pengadaan->saldo_belum_bayar, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($pengadaan->invoices_count === 0)
                                    <span class="text-sm text-gray-400 dark:text-gray-500">Belum ada invoice</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $pengadaan->status_pembayaran->badgeClass() }}">
                                        {{ $pengadaan->status_pembayaran->label() }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="show({{ $pengadaan->id }})"
                                        wire:loading.attr="disabled" wire:target="show({{ $pengadaan->id }})"
                                        wire:key="btn-show-{{ $pengadaan->id }}"
                                        class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 disabled:opacity-50"
                                        title="Detail">
                                        <svg wire:loading.class="hidden" wire:target="show({{ $pengadaan->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg wire:loading wire:target="show({{ $pengadaan->id }})" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </button>
                                    @if($pengadaan->userCanActOnApproval(auth()->user()))
                                        <button wire:click="confirmApprove({{ $pengadaan->id }})"
                                            wire:loading.attr="disabled" wire:target="confirmApprove({{ $pengadaan->id }})"
                                            wire:key="btn-approve-{{ $pengadaan->id }}"
                                            class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300 disabled:opacity-50"
                                            title="Setujui">
                                            <svg wire:loading.class="hidden" wire:target="confirmApprove({{ $pengadaan->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <svg wire:loading wire:target="confirmApprove({{ $pengadaan->id }})" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </button>
                                        <button wire:click="confirmReject({{ $pengadaan->id }})"
                                            wire:loading.attr="disabled" wire:target="confirmReject({{ $pengadaan->id }})"
                                            wire:key="btn-reject-{{ $pengadaan->id }}"
                                            class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300 disabled:opacity-50"
                                            title="Tolak">
                                            <svg wire:loading.class="hidden" wire:target="confirmReject({{ $pengadaan->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <svg wire:loading wire:target="confirmReject({{ $pengadaan->id }})" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </button>
                                    @endif
                                    @can('update', $pengadaan)
                                        <button wire:click="edit({{ $pengadaan->id }})"
                                            wire:loading.attr="disabled" wire:target="edit({{ $pengadaan->id }})"
                                            wire:key="btn-edit-{{ $pengadaan->id }}"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 disabled:opacity-50"
                                            title="Edit">
                                            <svg wire:loading.class="hidden" wire:target="edit({{ $pengadaan->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <svg wire:loading wire:target="edit({{ $pengadaan->id }})" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </button>
                                    @endcan
                                    @can('delete', $pengadaan)
                                        @if($pengadaan->invoices_count === 0)
                                        <button wire:click="confirmDelete({{ $pengadaan->id }})"
                                            wire:loading.attr="disabled" wire:target="confirmDelete({{ $pengadaan->id }})"
                                            wire:key="btn-delete-{{ $pengadaan->id }}"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 disabled:opacity-50"
                                            title="Hapus">
                                            <svg wire:loading.class="hidden" wire:target="confirmDelete({{ $pengadaan->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <svg wire:loading wire:target="confirmDelete({{ $pengadaan->id }})" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9zM4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/></svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tidak ada pengadaan ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $pengadaans->links() }}
        </div>
    </div>

    <x-delete-modal
        :show="$showDeleteModal"
        wire:model="showDeleteModal"
        title="Hapus Pengadaan"
        message="Apakah Anda yakin ingin menghapus pengadaan"
        :itemName="$deletingPengadaanName ?? ''"
        confirmMethod="delete"
    />

    @if($showApproveModal)
        <div class="fixed inset-0 z-50 overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="$wire.set('showApproveModal', false)"></div>
                <div class="inline-block align-bottom w-full bg-white dark:bg-gray-800 rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/20 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="mt-3 text-left sm:mt-0 sm:ml-4 flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Setujui Pengadaan</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Apakah Anda yakin ingin menyetujui pengadaan <strong>{{ $reviewingPengadaanName }}</strong>?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button wire:click="approve"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-base font-medium rounded-lg shadow-sm transition-all w-full sm:w-auto disabled:opacity-70 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-not-allowed"
                            wire:target="approve">
                            <svg wire:loading wire:target="approve" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Setujui
                        </button>
                        <x-cancel-button wire:click="$set('showApproveModal', false)" target="closeApproveModal" wire:key="btn-approve-cancel" variant="secondary" size="lg" class="mt-3 sm:mt-0 w-full sm:w-auto" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showRejectModal)
        <div class="fixed inset-0 z-50 overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="$wire.set('showRejectModal', false)"></div>
                <div class="inline-block align-bottom w-full bg-white dark:bg-gray-800 rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="reject">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tolak Pengadaan</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Pengadaan: {{ $reviewingPengadaanName }}</p>
                            <x-input-label for="rejectionReason" value="Alasan Penolakan" :required="true" />
                            <textarea wire:model="rejectionReason" id="rejectionReason" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 px-3 py-2 text-sm" placeholder="Jelaskan alasan penolakan..."></textarea>
                            <x-input-error :messages="$errors->get('rejectionReason')" class="mt-2" />
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-base font-medium rounded-lg shadow-sm transition-all w-full sm:w-auto disabled:opacity-70 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-70 cursor-not-allowed"
                                wire:target="reject">
                                <svg wire:loading wire:target="reject" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Tolak
                            </button>
                            <x-cancel-button wire:click="$set('showRejectModal', false)" target="closeRejectModal" wire:key="btn-reject-cancel" variant="secondary" size="lg" class="mt-3 sm:mt-0 w-full sm:w-auto" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Riwayat Status --}}
    @if($showHistoryModal && $historyPengadaan)
        <div class="fixed inset-0 z-50 overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="$wire.closeHistoryModal()"></div>
                <div class="inline-block align-bottom w-full bg-white dark:bg-gray-800 rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Riwayat Status</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">{{ $historyPengadaan->no_pesanan_spk }}</p>

                        {{-- Pembuatan --}}
                        <div class="flex gap-3 pb-4 border-b border-gray-100 dark:border-gray-700">
                            <span class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </span>
                            <div class="text-sm">
                                <p class="font-medium text-gray-900 dark:text-white">Dibuat oleh {{ $historyPengadaan->creator->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $historyPengadaan->created_at?->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        {{-- Timeline instance --}}
                        @php $instances = $historyPengadaan->approvalInstances->sortByDesc('id'); @endphp
                        @forelse($instances as $inst)
                            <div class="mt-4">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $inst->flow_name }}</p>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $inst->status->badgeClass() }}">{{ $inst->status->label() }}</span>
                                </div>
                                <div class="space-y-0">
                                    @foreach($inst->steps as $step)
                                        <div wire:key="hist-step-{{ $step->id }}" class="flex gap-3 py-2">
                                            <div class="flex flex-col items-center flex-shrink-0">
                                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold
                                                    {{ $step->status->value === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' :
                                                       ($step->status->value === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300' :
                                                       ($step->status->value === 'active' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400')) }}">
                                                    @if($step->status->value === 'approved') ✓
                                                    @elseif($step->status->value === 'rejected') ✕
                                                    @else {{ $step->position }} @endif
                                                </span>
                                                @if(!$loop->last)
                                                    <span class="w-px flex-1 bg-gray-200 dark:bg-gray-700"></span>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0 pb-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $step->name }}
                                                    <span class="text-xs font-normal text-gray-400 dark:text-gray-500">({{ $step->mode->label() }})</span>
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $step->approvers->pluck('name')->join(', ') }}</p>
                                                @foreach($step->actions as $action)
                                                    <p class="text-xs mt-1 {{ $action->action->value === 'approved' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                        {{ $action->action->label() }} oleh {{ $action->user->name ?? '-' }} — {{ $action->created_at->format('d/m/Y H:i') }}
                                                        @if($action->note) · "{{ $action->note }}" @endif
                                                    </p>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400 italic">Belum ada alur persetujuan untuk dokumen ini.</p>
                        @endforelse

                        @if($historyPengadaan->updater)
                            <p class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400">
                                Terakhir diubah oleh {{ $historyPengadaan->updater->name }} — {{ $historyPengadaan->updated_at?->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <x-cancel-button wire:click="closeHistoryModal" target="closeHistoryModal" wire:key="btn-history-close" variant="secondary" size="lg" class="w-full sm:w-auto" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
