        {{-- Status Invoice: breakdown lunas/sebagian/belum --}}
        @if($pengadaanStats['invoice_total'] > 0)
        <div class="dash-enter mt-4 flex flex-wrap items-center gap-2 text-xs" style="animation-delay:800ms">
            <span class="font-medium text-gray-500 dark:text-gray-400">Status Invoice:</span>
            <span class="px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium">
                Total: {{ $pengadaanStats['invoice_total'] }}
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 font-medium">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                {{ $pengadaanStats['invoice_lunas'] }} Lunas
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400 font-medium">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM4 10a6 6 0 1012 0H4z"/></svg>
                {{ $pengadaanStats['invoice_sebagian'] }} Sebagian
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 font-medium">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                {{ $pengadaanStats['invoice_belum_dibayar'] }} Belum Dibayar
            </span>
        </div>
        @endif
