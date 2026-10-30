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
