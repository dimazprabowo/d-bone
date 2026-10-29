<x-app-layout title="Tambah Alur Persetujuan">
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('approval-flows.index') }}" wire:navigate class="hover:text-gray-700 dark:hover:text-gray-200">Alur Persetujuan</a>
            <span>/</span>
            <span class="text-gray-900 dark:text-white font-medium">Tambah</span>
        </div>
    </x-slot>

    <livewire:approval-flow.approval-flow-form />
</x-app-layout>
