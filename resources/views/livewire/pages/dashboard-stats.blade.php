{{-- Partial: Dashboard Statistics View (requires dashboard_view permission) --}}
<div class="space-y-6">
    <style>
        @keyframes dashFadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes dashHeroShift { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        @keyframes dashFloat { 0%, 100% { transform: translateY(0) rotate(-8deg); } 50% { transform: translateY(-12px) rotate(-8deg); } }
        .dash-enter { animation: dashFadeUp .55s cubic-bezier(.22,.9,.35,1) backwards; }
        .dash-hero { background-size: 200% auto; animation: dashHeroShift 9s ease-in-out infinite; }
        .dash-float { animation: dashFloat 6s ease-in-out infinite; }
        .dash-card { transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
        .dash-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -5px rgb(0 0 0 / .1), 0 4px 10px -3px rgb(0 0 0 / .08); }
        .dark .dash-card:hover { box-shadow: 0 10px 25px -5px rgb(0 0 0 / .45), 0 4px 10px -3px rgb(0 0 0 / .35); }
        @media (prefers-reduced-motion: reduce) {
            .dash-enter { opacity: 1; animation: none; }
            .dash-hero, .dash-float { animation: none; }
        }
    </style>

    {{-- Header Banner (animated gradient) --}}
    <div class="dash-enter relative overflow-hidden rounded-xl shadow-lg text-white dash-hero bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-800" style="animation-delay:0ms">
        <div class="relative p-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Selamat Datang, {{ $authUser->name }}!</h2>
                <p class="mt-1 text-blue-100 text-sm">
                    {{ config('app.name') }} &mdash; Panel Administrasi
                </p>
                <div class="mt-3 inline-flex items-center whitespace-nowrap gap-2 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1 text-xs font-semibold">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    {{ $authUserRole }}
                </div>
            </div>
            <div class="hidden md:block">
                <svg class="w-20 h-20 text-white opacity-20 dash-float" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                </svg>
            </div>
        </div>
        <div class="pointer-events-none absolute -right-16 -top-24 w-64 h-64 rounded-full bg-white/10 blur-2xl"></div>
        <div class="pointer-events-none absolute -left-10 -bottom-24 w-56 h-56 rounded-full bg-indigo-400/20 blur-2xl"></div>
    </div>

    {{-- Stat Cards: User & Role (gated by respective entity permissions) --}}
    @php($statCardCount = (int) Gate::allows('users_view') + (int) Gate::allows('roles_view'))
    @if($statCardCount > 0)
    <div class="grid grid-cols-1 {{ $statCardCount > 1 ? 'md:grid-cols-2' : '' }} gap-6">

        @can('users_view')
        <div class="dash-enter dash-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" style="animation-delay:80ms">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Pengguna</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white"
                        x-data="{ n: 0, t: {{ (int) $totalUsers }} }" x-init="(()=>{const s=performance.now(),d=800,step=()=>{const p=Math.min(1,(performance.now()-s)/d);n=Math.round(t*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(step)};requestAnimationFrame(step)})()"
                        x-text="n.toLocaleString('id-ID')">{{ number_format($totalUsers) }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Pengguna terdaftar</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/30 rounded-xl shadow-inner">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        @endcan

        @can('roles_view')
        <div class="dash-enter dash-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" style="animation-delay:160ms">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Role</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white"
                        x-data="{ n: 0, t: {{ (int) $totalRoles }} }" x-init="(()=>{const s=performance.now(),d=800,step=()=>{const p=Math.min(1,(performance.now()-s)/d);n=Math.round(t*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(step)};requestAnimationFrame(step)})()"
                        x-text="n.toLocaleString('id-ID')">{{ number_format($totalRoles) }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Role yang dikonfigurasi</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-violet-50 to-violet-100 dark:from-violet-900/20 dark:to-violet-800/30 rounded-xl shadow-inner">
                    <svg class="w-8 h-8 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
        </div>
        @endcan

    </div>
    @endif

    {{-- Pengadaan Barang & Jasa (gated by pengadaan_view; cabang-scoped via service) --}}
    @can('pengadaan_view')
    <div class="dash-enter bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" style="animation-delay:200ms">
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

        {{-- Perlu Tindakan: action items dengan badge count --}}
        @if($pengadaanStats['pengadaan_pending'] > 0 || $pengadaanStats['invoice_overdue'] > 0 || $pengadaanStats['invoice_due_soon'] > 0)
        <div class="dash-enter mt-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600" style="animation-delay:780ms">
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
    </div>
    @endcan

    {{-- Quick Actions (only render if user has at least one relevant permission) --}}
    @canany(['users_view', 'roles_view', 'configuration_view'])
    <div class="dash-enter bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" style="animation-delay:700ms">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

            @can('users_view')
            <a href="{{ route('settings.users') }}" wire:navigate
               class="dash-card flex items-center gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all group">
                <div class="flex-shrink-0 p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-lg group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 group-hover:scale-105 transition-all">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Manajemen User</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kelola akun pengguna</p>
                </div>
            </a>
            @endcan

            @can('roles_view')
            <a href="{{ route('settings.roles') }}" wire:navigate
               class="dash-card flex items-center gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-violet-400 dark:hover:border-violet-500 hover:bg-violet-50 dark:hover:bg-violet-900/10 transition-all group">
                <div class="flex-shrink-0 p-2.5 bg-violet-100 dark:bg-violet-900/30 rounded-lg group-hover:bg-violet-200 dark:group-hover:bg-violet-900/50 group-hover:scale-105 transition-all">
                    <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Roles & Permissions</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kelola hak akses</p>
                </div>
            </a>
            @endcan

            @can('configuration_view')
            <a href="{{ route('settings.system') }}" wire:navigate
               class="dash-card flex items-center gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-emerald-400 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-all group">
                <div class="flex-shrink-0 p-2.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg group-hover:bg-emerald-200 dark:group-hover:bg-emerald-900/50 group-hover:scale-105 transition-all">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Konfigurasi System</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pengaturan aplikasi</p>
                </div>
            </a>
            @endcan

        </div>
    </div>
    @endcanany

    {{-- System Info (gated by configuration_view — internal app info) --}}
    @can('configuration_view')
    <div class="dash-enter bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" style="animation-delay:760ms">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">Informasi System</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="dash-card flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full"></span>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Laravel {{ app()->version() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Framework</p>
                </div>
            </div>
            <div class="dash-card flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full"></span>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">PHP {{ phpversion() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Runtime</p>
                </div>
            </div>
            <div class="dash-card flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full"></span>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ config('app.env') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Environment</p>
                </div>
            </div>
            <div class="dash-card flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full"></span>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ config('app.timezone') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Timezone</p>
                </div>
            </div>
        </div>
    </div>
    @endcan

</div>
