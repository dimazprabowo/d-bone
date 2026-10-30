        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Pengadaan Barang & Jasa</h3>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Ringkasan biaya, penagihan & pembayaran</p>
            </div>
            <a href="{{ route('pengadaan.index') }}" wire:navigate
               class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors group/link">
                Lihat semua
                <svg class="w-3.5 h-3.5 transition-transform group-hover/link:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
