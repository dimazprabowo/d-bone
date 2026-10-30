        {{-- Biaya Pengadaan per Cabang (dengan progress bar relatif, access_all_cabang only) --}}
        @can('access_all_cabang')
            @if(!empty($pengadaanPerCabang))
                @php($maxCabangSpend = $pengadaanPerCabang[0]['total_spend'] ?? 1)
                <div class="dash-enter mt-6 pt-6 border-t border-gray-200 dark:border-gray-700" style="animation-delay:880ms">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">Biaya Pengadaan per Cabang</h4>
                    <div class="space-y-3">
                        @foreach($pengadaanPerCabang as $row)
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="text-gray-700 dark:text-gray-300">{{ $row['cabang'] }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($row['total_spend'], 0, ',', '.') }} <span class="text-gray-400 dark:text-gray-500 font-normal">({{ $row['total_pengadaan'] }}x)</span></span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-1000 ease-out" x-data="{ w: 0, t: {{ min(100, ($row['total_spend'] / max($maxCabangSpend, 1)) * 100) }} }" x-init="setTimeout(() => w = t, {{ 300 + $loop->index * 120 }})" :style="'width:' + w + '%'"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endcan
