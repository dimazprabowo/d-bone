{{-- Partial: Dashboard Statistics View (requires dashboard_view permission) --}}
<div class="space-y-6">
    @include('livewire.pages.dashboard._styles')

    {{-- Header Banner --}}
    @include('livewire.pages.dashboard.header')

    {{-- Stat Cards: User & Role --}}
    @include('livewire.pages.dashboard.admin-stat-cards')

    {{-- Pengadaan Barang & Jasa (gated by pengadaan_view; cabang-scoped via service) --}}
    @can('pengadaan_view')
    <div class="dash-enter bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" style="animation-delay:200ms">
        @include('livewire.pages.dashboard.pengadaan-header')
        @include('livewire.pages.dashboard.status-overview')
        @include('livewire.pages.dashboard.financial-overview')
        @include('livewire.pages.dashboard.charts')
        @include('livewire.pages.dashboard.action-items')
        @include('livewire.pages.dashboard.invoice-status')
        @include('livewire.pages.dashboard.top-vendors')
        @include('livewire.pages.dashboard.spend-per-cabang')
    </div>
    @endcan

    {{-- Quick Actions --}}
    @include('livewire.pages.dashboard.quick-actions')

    {{-- System Info --}}
    @include('livewire.pages.dashboard.system-info')
</div>
