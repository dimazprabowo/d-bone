<div class="w-full">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        {{-- Toolbar --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="relative w-full sm:w-72">
                <x-text-input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari alur..." class="block w-full pl-10" />
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            @can('approval_flow_create')
                <a href="{{ route('approval-flows.create') }}" wire:navigate
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Alur
                </a>
            @endcan
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Alur</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prioritas</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Entity</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tahap</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($flows as $flow)
                        <tr wire:key="flow-{{ $flow->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $flow->name }}</div>
                                @if($flow->description)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($flow->description, 60) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 text-xs font-bold rounded-lg bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300" title="Dievaluasi ke-{{ $flow->priority }}">{{ $flow->priority }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">{{ ucfirst($flow->entity_type) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">{{ $flow->steps_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $flow->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ $flow->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-3">
                                @can('approval_flow_update')
                                    <button wire:click="toggleActive({{ $flow->id }})"
                                        wire:loading.attr="disabled" wire:target="toggleActive({{ $flow->id }})"
                                        wire:key="btn-toggle-{{ $flow->id }}"
                                        class="disabled:opacity-50 {{ $flow->is_active ? 'text-amber-600 hover:text-amber-800 dark:text-amber-400' : 'text-emerald-600 hover:text-emerald-800 dark:text-emerald-400' }}"
                                        title="{{ $flow->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <svg wire:loading.class="hidden" wire:target="toggleActive({{ $flow->id }})" class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $flow->is_active ? 'M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z' : 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }}"/></svg>
                                        <svg wire:loading wire:target="toggleActive({{ $flow->id }})" class="animate-spin w-5 h-5 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </button>
                                    <a href="{{ route('approval-flows.edit', $flow) }}" wire:navigate
                                       x-data="{ loading: false }" @click="loading = true" wire:key="btn-edit-{{ $flow->id }}"
                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 inline-block" title="Edit">
                                        <svg x-show="!loading" class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <svg x-show="loading" x-cloak class="animate-spin w-5 h-5 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </a>
                                @endcan
                                @can('approval_flow_create')
                                    <button wire:click="duplicate({{ $flow->id }})"
                                        wire:loading.attr="disabled" wire:target="duplicate({{ $flow->id }})"
                                        wire:key="btn-copy-{{ $flow->id }}"
                                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 disabled:opacity-50" title="Duplikasi Alur">
                                        <svg wire:loading.class="hidden" wire:target="duplicate({{ $flow->id }})" class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <svg wire:loading wire:target="duplicate({{ $flow->id }})" class="animate-spin w-5 h-5 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </button>
                                @endcan
                                @can('approval_flow_delete')
                                    <button wire:click="confirmDelete({{ $flow->id }})"
                                        wire:loading.attr="disabled" wire:target="confirmDelete({{ $flow->id }})"
                                        wire:key="btn-delete-{{ $flow->id }}"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 disabled:opacity-50" title="Hapus">
                                        <svg wire:loading.class="hidden" wire:target="confirmDelete({{ $flow->id }})" class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <svg wire:loading wire:target="confirmDelete({{ $flow->id }})" class="animate-spin w-5 h-5 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="h-12 w-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Belum ada alur persetujuan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($flows->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $flows->links() }}
            </div>
        @endif
    </div>

    <x-delete-modal wire:model="showDeleteModal" title="Hapus Alur Persetujuan" message="Yakin ingin menghapus alur persetujuan ini? Tindakan ini tidak dapat dibatalkan." confirmMethod="delete" />
</div>
