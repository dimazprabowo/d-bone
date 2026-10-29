@props(['field', 'sortField' => '', 'sortDirection' => 'asc', 'align' => 'left'])

<th class="px-6 py-3 text-{{ $align }} text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
    <button type="button" wire:click="sortBy('{{ $field }}')" class="inline-flex items-center gap-1 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-200">
        {{ $slot }}
        @if($sortField === $field)
            <svg class="w-3.5 h-3.5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
        @else
            <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/></svg>
        @endif
    </button>
</th>
