        {{-- Financial Overview: 4 stat cards dengan progress bar --}}
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Biaya Pengadaan --}}
            <div class="dash-enter dash-card p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg" style="animation-delay:560ms">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-blue-600 dark:text-blue-400">Total Biaya Pengadaan</p>
                    <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <p class="mt-2 text-2xl font-bold text-blue-700 dark:text-blue-300"
                    x-data="{ n: 0, t: {{ (float) $pengadaanStats['total_biaya'] }} }" x-init="(()=>{const s=performance.now(),d=900,step=()=>{const p=Math.min(1,(performance.now()-s)/d);n=Math.round(t*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(step)};requestAnimationFrame(step)})()"
                    x-text="'Rp ' + n.toLocaleString('id-ID')">Rp {{ number_format($pengadaanStats['total_biaya'], 0, ',', '.') }}</p>
                <p class="mt-0.5 text-xs text-blue-500/80 dark:text-blue-500/60">{{ number_format($pengadaanStats['total_pengadaan']) }} pengadaan disetujui</p>
                <div class="mt-3">
                    <div class="flex justify-between text-xs text-blue-500/80 dark:text-blue-500/60 mb-1">
                        <span>Sudah ditagihkan</span>
                        <span>{{ $pengadaanStats['persentase_ditagih'] }}%</span>
                    </div>
                    <div class="w-full bg-blue-200/50 dark:bg-blue-900/40 rounded-full h-1.5">
                        <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-1000 ease-out" x-data="{ w: 0, t: {{ $pengadaanStats['persentase_ditagih'] }} }" x-init="setTimeout(() => w = t, 150)" :style="'width:' + w + '%'"></div>
                    </div>
                </div>
            </div>

            {{-- Sudah Dibayar --}}
            <div class="dash-enter dash-card p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg" style="animation-delay:620ms">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Sudah Dibayar</p>
                    <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <p class="mt-2 text-2xl font-bold text-emerald-700 dark:text-emerald-300"
                    x-data="{ n: 0, t: {{ (float) $pengadaanStats['total_dibayar'] }} }" x-init="(()=>{const s=performance.now(),d=900,step=()=>{const p=Math.min(1,(performance.now()-s)/d);n=Math.round(t*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(step)};requestAnimationFrame(step)})()"
                    x-text="'Rp ' + n.toLocaleString('id-ID')">Rp {{ number_format($pengadaanStats['total_dibayar'], 0, ',', '.') }}</p>
                <p class="mt-0.5 text-xs text-emerald-500/80 dark:text-emerald-500/60">Dari Rp {{ number_format($pengadaanStats['total_invoice'], 0, ',', '.') }} invoice</p>
                <div class="mt-3">
                    <div class="flex justify-between text-xs text-emerald-500/80 dark:text-emerald-500/60 mb-1">
                        <span>Terealisasi</span>
                        <span>{{ $pengadaanStats['persentase_dibayar'] }}%</span>
                    </div>
                    <div class="w-full bg-emerald-200/50 dark:bg-emerald-900/40 rounded-full h-1.5">
                        <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-1000 ease-out" x-data="{ w: 0, t: {{ $pengadaanStats['persentase_dibayar'] }} }" x-init="setTimeout(() => w = t, 250)" :style="'width:' + w + '%'"></div>
                    </div>
                </div>
            </div>

            {{-- Outstanding / Sisa --}}
            <div class="dash-enter dash-card p-4 bg-red-50 dark:bg-red-900/20 rounded-lg" style="animation-delay:680ms">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-red-600 dark:text-red-400">Sisa Pembayaran</p>
                    <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="mt-2 text-2xl font-bold text-red-700 dark:text-red-300"
                    x-data="{ n: 0, t: {{ (float) $pengadaanStats['total_outstanding'] }} }" x-init="(()=>{const s=performance.now(),d=900,step=()=>{const p=Math.min(1,(performance.now()-s)/d);n=Math.round(t*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(step)};requestAnimationFrame(step)})()"
                    x-text="'Rp ' + n.toLocaleString('id-ID')">Rp {{ number_format($pengadaanStats['total_outstanding'], 0, ',', '.') }}</p>
                <p class="mt-0.5 text-xs text-red-500/80 dark:text-red-500/60">Sisa dari total biaya</p>
            </div>

            {{-- Belum Ditagih --}}
            <div class="dash-enter dash-card p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg" style="animation-delay:740ms">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-amber-600 dark:text-amber-400">Belum Ditagih</p>
                    <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <p class="mt-2 text-2xl font-bold text-amber-700 dark:text-amber-300"
                    x-data="{ n: 0, t: {{ (float) $pengadaanStats['total_belum_ditagih'] }} }" x-init="(()=>{const s=performance.now(),d=900,step=()=>{const p=Math.min(1,(performance.now()-s)/d);n=Math.round(t*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(step)};requestAnimationFrame(step)})()"
                    x-text="'Rp ' + n.toLocaleString('id-ID')">Rp {{ number_format($pengadaanStats['total_belum_ditagih'], 0, ',', '.') }}</p>
                <p class="mt-0.5 text-xs text-amber-500/80 dark:text-amber-500/60">Belum ada invoice diterbitkan</p>
            </div>
        </div>
