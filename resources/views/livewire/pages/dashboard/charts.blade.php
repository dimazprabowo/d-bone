        {{-- Grafik: Tren biaya per bulan (fullwidth) --}}
        <div class="dash-enter dash-card mt-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5" style="animation-delay:780ms">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Tren Biaya Pengadaan</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total biaya disetujui per bulan · 12 bulan terakhir</p>
                </div>
                <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded-md">
                    {{ collect($monthlyTrend ?? [])->sum('count') }} SPK
                </p>
            </div>
            <div class="flex items-end gap-1 sm:gap-2 h-40">
                @foreach($monthlyTrend ?? [] as $m)
                    <div class="flex-1 min-w-0 flex flex-col items-center justify-end h-full group relative">
                        <div class="hidden group-hover:block absolute -top-1 left-1/2 -translate-x-1/2 whitespace-nowrap z-10 px-2 py-1 rounded-md bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 text-[10px] font-medium shadow-lg pointer-events-none">
                            Rp {{ number_format($m['total'], 0, ',', '.') }}
                            <span class="opacity-70">· {{ $m['count'] }} SPK</span>
                        </div>
                        <div class="w-full max-w-12 rounded-t-md transition-all duration-300 group-hover:opacity-90 {{ $m['total'] > 0 ? 'bg-gradient-to-t from-blue-600 to-blue-400 dark:from-blue-500 dark:to-blue-300' : 'bg-gray-200 dark:bg-gray-700' }} {{ $m['key'] === now()->format('Y-m') && $m['total'] > 0 ? 'ring-2 ring-blue-500/40 dark:ring-blue-300/40' : '' }}"
                            style="height: {{ $m['total'] > 0 ? max(3, $m['total'] / $trendMax * 100) : 2 }}%"></div>
                        <p class="mt-1.5 text-[10px] {{ $m['key'] === now()->format('Y-m') ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }} uppercase">{{ $m['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
