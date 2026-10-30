        {{-- Perlu Tindakan: action items dengan badge count --}}
        @if($pengadaanStats['pengadaan_pending'] > 0 || $pengadaanStats['invoice_overdue'] > 0 || $pengadaanStats['invoice_due_soon'] > 0)
        <div class="dash-enter mt-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600" style="animation-delay:840ms">
            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                Perlu Tindakan
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                @if($pengadaanStats['pengadaan_pending'] > 0)
                <a href="{{ route('pengadaan.index') }}" wire:navigate class="dash-card flex items-center gap-3 p-2.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-700 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors group">
                    <span class="flex-shrink-0 px-2 py-0.5 text-xs font-bold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">{{ $pengadaanStats['pengadaan_pending'] }}</span>
                    <span class="flex-1 text-xs text-gray-600 dark:text-gray-400 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Pengadaan menunggu approval</span>
                    <svg class="flex-shrink-0 w-4 h-4 text-gray-400 group-hover:text-purple-500 transition-all group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endif
                @if($pengadaanStats['invoice_overdue'] > 0)
                <a href="{{ route('pengadaan.index') }}" wire:navigate class="dash-card flex items-center gap-3 p-2.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-red-300 dark:hover:border-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors group">
                    <span class="flex-shrink-0 px-2 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 animate-pulse">{{ $pengadaanStats['invoice_overdue'] }}</span>
                    <span class="flex-1 text-xs text-gray-600 dark:text-gray-400 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">Invoice sudah lewat jatuh tempo</span>
                    <svg class="flex-shrink-0 w-4 h-4 text-gray-400 group-hover:text-red-500 transition-all group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endif
                @if($pengadaanStats['invoice_due_soon'] > 0)
                <a href="{{ route('pengadaan.index') }}" wire:navigate class="dash-card flex items-center gap-3 p-2.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-amber-300 dark:hover:border-amber-700 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors group">
                    <span class="flex-shrink-0 px-2 py-0.5 text-xs font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">{{ $pengadaanStats['invoice_due_soon'] }}</span>
                    <span class="flex-1 text-xs text-gray-600 dark:text-gray-400 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Invoice jatuh tempo &le; 7 hari</span>
                    <svg class="flex-shrink-0 w-4 h-4 text-gray-400 group-hover:text-amber-500 transition-all group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endif
            </div>
        </div>
        @endif
