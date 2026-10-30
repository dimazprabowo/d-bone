        {{-- Status Overview: 4 count cards (semua status, cabang-scoped) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Pengadaan --}}
            <div class="dash-enter dash-card p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg" style="animation-delay:260ms">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-blue-600 dark:text-blue-400">Total Pengadaan</p>
                    <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <p class="mt-2 text-2xl font-bold text-blue-700 dark:text-blue-300"
                    x-data="{ n: 0, t: {{ (int) $pengadaanStats['pengadaan_total'] }} }" x-init="(()=>{const s=performance.now(),d=800,step=()=>{const p=Math.min(1,(performance.now()-s)/d);n=Math.round(t*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(step)};requestAnimationFrame(step)})()"
                    x-text="n.toLocaleString('id-ID')">{{ number_format($pengadaanStats['pengadaan_total']) }}</p>
                <p class="mt-0.5 text-xs text-blue-500/80 dark:text-blue-500/60">Semua status pengadaan</p>
            </div>

            {{-- Disetujui --}}
            <div class="dash-enter dash-card p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg" style="animation-delay:620ms">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Disetujui</p>
                    <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="mt-2 text-2xl font-bold text-emerald-700 dark:text-emerald-300"
                    x-data="{ n: 0, t: {{ (int) $pengadaanStats['total_pengadaan'] }} }" x-init="(()=>{const s=performance.now(),d=800,step=()=>{const p=Math.min(1,(performance.now()-s)/d);n=Math.round(t*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(step)};requestAnimationFrame(step)})()"
                    x-text="n.toLocaleString('id-ID')">{{ number_format($pengadaanStats['total_pengadaan']) }}</p>
                <p class="mt-0.5 text-xs text-emerald-500/80 dark:text-emerald-500/60">Pengadaan disetujui</p>
            </div>

            {{-- Menunggu --}}
            <div class="dash-enter dash-card p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg" style="animation-delay:420ms">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-amber-600 dark:text-amber-400">Menunggu</p>
                    <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="mt-2 text-2xl font-bold text-amber-700 dark:text-amber-300"
                    x-data="{ n: 0, t: {{ (int) $pengadaanStats['pengadaan_pending'] }} }" x-init="(()=>{const s=performance.now(),d=800,step=()=>{const p=Math.min(1,(performance.now()-s)/d);n=Math.round(t*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(step)};requestAnimationFrame(step)})()"
                    x-text="n.toLocaleString('id-ID')">{{ number_format($pengadaanStats['pengadaan_pending']) }}</p>
                <p class="mt-0.5 text-xs text-amber-500/80 dark:text-amber-500/60">Menunggu persetujuan</p>
            </div>

            {{-- Ditolak --}}
            <div class="dash-enter dash-card p-4 bg-red-50 dark:bg-red-900/20 rounded-lg" style="animation-delay:500ms">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-red-600 dark:text-red-400">Ditolak</p>
                    <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="mt-2 text-2xl font-bold text-red-700 dark:text-red-300"
                    x-data="{ n: 0, t: {{ (int) $pengadaanStats['pengadaan_rejected'] }} }" x-init="(()=>{const s=performance.now(),d=800,step=()=>{const p=Math.min(1,(performance.now()-s)/d);n=Math.round(t*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(step)};requestAnimationFrame(step)})()"
                    x-text="n.toLocaleString('id-ID')">{{ number_format($pengadaanStats['pengadaan_rejected']) }}</p>
                <p class="mt-0.5 text-xs text-red-500/80 dark:text-red-500/60">Pengadaan ditolak</p>
            </div>
        </div>
