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
