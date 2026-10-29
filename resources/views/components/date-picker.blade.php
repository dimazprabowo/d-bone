@props(['placeholder' => 'Pilih tanggal'])

@php
    $wireModel = $attributes->wire('model')->value();
    $isLive = $attributes->wire('model')->hasModifier('live');
    $wireKey = $attributes->get('wire:key');
@endphp

<div
    x-data="{
        open: false,
        dropUp: false,
        value: @entangle($wireModel){{ $isLive ? '.live' : '' }},
        viewYear: null,
        viewMonth: null,
        monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
        dayNames: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        placeholder: @js($placeholder),

        init() {
            this.resetView();
        },

        get parsed() {
            if (!this.value) return null;
            const [y, m, d] = String(this.value).split('-').map(Number);
            if (!y || !m || !d) return null;
            return { y, m, d };
        },

        get displayText() {
            const s = this.parsed;
            return s ? s.d + ' ' + this.monthNames[s.m - 1] + ' ' + s.y : this.placeholder;
        },

        get days() {
            const first = new Date(this.viewYear, this.viewMonth - 1, 1);
            const offset = (first.getDay() + 6) % 7;
            const count = new Date(this.viewYear, this.viewMonth, 0).getDate();
            const cells = [];
            for (let i = 0; i < offset; i++) cells.push(null);
            for (let d = 1; d <= count; d++) cells.push(d);
            return cells;
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.resetView();
                this.$nextTick(() => this.checkPosition());
            }
        },

        checkPosition() {
            const rect = this.$refs.trigger.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom;
            const calendarHeight = 380;
            this.dropUp = spaceBelow < calendarHeight && rect.top > spaceBelow;
        },

        resetView() {
            const s = this.parsed;
            const now = new Date();
            this.viewYear = s ? s.y : now.getFullYear();
            this.viewMonth = s ? s.m : now.getMonth() + 1;
        },

        prevMonth() {
            if (this.viewMonth === 1) { this.viewMonth = 12; this.viewYear--; } else { this.viewMonth--; }
        },

        nextMonth() {
            if (this.viewMonth === 12) { this.viewMonth = 1; this.viewYear++; } else { this.viewMonth++; }
        },

        pick(day) {
            if (!day) return;
            const mm = String(this.viewMonth).padStart(2, '0');
            const dd = String(day).padStart(2, '0');
            this.value = this.viewYear + '-' + mm + '-' + dd;
            this.open = false;
        },

        pickToday() {
            const now = new Date();
            this.viewYear = now.getFullYear();
            this.viewMonth = now.getMonth() + 1;
            this.pick(now.getDate());
        },

        clear() {
            this.value = null;
            this.open = false;
        },

        isSelected(day) {
            const s = this.parsed;
            return s && day === s.d && this.viewMonth === s.m && this.viewYear === s.y;
        },

        isToday(day) {
            const now = new Date();
            return day === now.getDate() && this.viewMonth === now.getMonth() + 1 && this.viewYear === now.getFullYear();
        },
    }"
    x-on:click.away="open = false"
    @scroll.window="open && checkPosition()"
    class="relative"
    @if($wireKey) wire:key="{{ $wireKey }}" @endif
    wire:ignore.self
>
    <button type="button" x-on:click="toggle()" x-ref="trigger" {{ $attributes->only('id') }}
        class="mt-1 flex w-full items-center justify-between gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-left text-sm text-gray-900 shadow-sm transition-colors hover:border-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:border-gray-500 dark:focus:ring-blue-400 dark:focus:ring-offset-gray-800">
        <span x-text="displayText" :class="parsed ? '' : 'text-gray-400 dark:text-gray-500'"></span>
        <svg class="h-4 w-4 flex-shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    </button>

    <div x-show="open" x-cloak x-transition.opacity.duration.150ms
        :class="dropUp ? 'bottom-full mb-2' : 'top-full mt-2'"
        class="absolute left-0 z-50 w-72 rounded-lg border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-3 flex items-center justify-between">
            <button type="button" x-on:click="prevMonth()" class="rounded-md p-1.5 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700" title="Bulan sebelumnya">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="monthNames[viewMonth - 1] + ' ' + viewYear"></span>
            <button type="button" x-on:click="nextMonth()" class="rounded-md p-1.5 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700" title="Bulan berikutnya">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        <div class="mb-1 grid grid-cols-7 text-center">
            <template x-for="name in dayNames" :key="name">
                <span class="text-xs font-medium text-gray-400 dark:text-gray-500" x-text="name"></span>
            </template>
        </div>

        <div class="grid grid-cols-7 gap-y-1 text-center">
            <template x-for="(day, i) in days" :key="i">
                <span>
                    <template x-if="day">
                        <button type="button" x-on:click="pick(day)"
                            class="mx-auto flex h-8 w-8 items-center justify-center rounded-md text-sm transition-colors"
                            :class="{
                                'bg-blue-600 font-semibold text-white hover:bg-blue-700': isSelected(day),
                                'text-blue-600 ring-1 ring-blue-500 dark:text-blue-400': !isSelected(day) && isToday(day),
                                'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700': !isSelected(day) && !isToday(day)
                            }"
                            x-text="day"></button>
                    </template>
                </span>
            </template>
        </div>

        <div class="mt-3 flex items-center justify-between border-t border-gray-200 pt-3 dark:border-gray-700">
            <button type="button" x-on:click="pickToday()" class="text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Hari ini</button>
            <button type="button" x-on:click="clear()" class="text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Hapus</button>
        </div>
    </div>
</div>
