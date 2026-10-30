        {{-- Top Vendor by Spend (dengan progress bar relatif) --}}
        @if(!empty($pengadaanPerVendor))
            @php($maxVendorSpend = $pengadaanPerVendor[0]['total_spend'] ?? 1)
            <div class="dash-enter mt-6 pt-6 border-t border-gray-200 dark:border-gray-700" style="animation-delay:840ms">
                <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">Top Vendor by Spend</h4>
                <div class="space-y-3">
                    @foreach($pengadaanPerVendor as $idx => $row)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="inline-flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold {{ $idx === 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">{{ $idx + 1 }}</span>
                                    {{ $row['vendor'] }}
                                </span>
                                <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($row['total_spend'], 0, ',', '.') }} <span class="text-gray-400 dark:text-gray-500 font-normal">({{ $row['total_pengadaan'] }}x)</span></span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="{{ $idx === 0 ? 'bg-gradient-to-r from-amber-400 to-amber-500' : 'bg-blue-500' }} h-1.5 rounded-full transition-all duration-1000 ease-out" x-data="{ w: 0, t: {{ min(100, ($row['total_spend'] / max($maxVendorSpend, 1)) * 100) }} }" x-init="setTimeout(() => w = t, {{ 300 + $idx * 120 }})" :style="'width:' + w + '%'"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
