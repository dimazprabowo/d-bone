<div wire:key="approval-flow-form" class="w-full">
    <form wire:submit="save">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="p-6 space-y-6">
                {{-- Info Dasar --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="flow-name" value="Nama Alur" :required="true" />
                        <x-text-input id="flow-name" wire:model="name" class="mt-1 block w-full" placeholder="mis. Alur Standar Pengadaan" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="flow-entity" value="Entity" :required="true" />
                        <select id="flow-entity" wire:model="entity_type"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                            <option value="pengadaan">Pengadaan</option>
                        </select>
                        <x-input-error :messages="$errors->get('entity_type')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="flow-desc" value="Deskripsi" />
                        <textarea id="flow-desc" wire:model="description" rows="2"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 px-3 py-2 text-sm"
                            placeholder="Deskripsi singkat alur ini..."></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="flow-priority" value="Prioritas (angka kecil dievaluasi dulu)" :required="true" />
                        <x-text-input id="flow-priority" type="number" min="1" wire:model.live="priority" class="mt-1 block w-full" />
                        @if($this->priorityConflict)
                            <p class="mt-1.5 text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                                Prioritas {{ $this->priority }} juga dipakai alur "{{ $this->priorityConflict->name }}" — urutan evaluasi antar keduanya ditentukan urutan dibuat (yang lebih dulu dibuat menang).
                            </p>
                        @endif
                        <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_active"
                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Alur aktif (dipakai untuk dokumen baru)</span>
                        </label>
                    </div>
                </div>

                {{-- Kondisi Pemilihan --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Kondisi Pemilihan Alur</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Alur ini dipakai jika <span class="font-medium text-gray-700 dark:text-gray-300">semua</span> kondisi terpenuhi (AND). Kosongkan agar menjadi fallback saat tidak ada alur lain yang cocok.</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        @forelse($conditions as $ci => $condition)
                            <div wire:key="cond-{{ $ci }}" class="flex gap-3">
                                {{-- Connector rail --}}
                                <div class="flex flex-col items-center flex-shrink-0 w-10">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-md
                                        {{ $loop->first ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                        {{ $loop->first ? 'Jika' : 'Dan' }}
                                    </span>
                                    <span class="w-px flex-1 bg-gray-200 dark:bg-gray-700"></span>
                                </div>

                                {{-- Rule card --}}
                                <div class="flex-1 min-w-0 mb-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors">
                                    <div class="flex flex-wrap items-center gap-2 p-3">
                                        <select wire:model.live="conditions.{{ $ci }}.field" aria-label="Field kondisi"
                                            class="flex-1 min-w-[9rem] border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih Field</option>
                                            @foreach($this->conditionFields as $f => $cfg)
                                                <option value="{{ $f }}">{{ $cfg['label'] }}</option>
                                            @endforeach
                                        </select>

                                        <select wire:model.live="conditions.{{ $ci }}.operator" aria-label="Operator"
                                            class="w-full sm:w-48 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                                            @if($condition['field'] !== '')
                                                @foreach($this->operatorsForField($condition['field']) as $op)
                                                    <option value="{{ $op->value }}">{{ $op->label() }}</option>
                                                @endforeach
                                            @else
                                                <option value="">—</option>
                                            @endif
                                        </select>

                                        @if(isset($this->conditionValueOptions[$condition['field']]))
                                            <select wire:model="conditions.{{ $ci }}.value" aria-label="Nilai kondisi"
                                                class="flex-1 min-w-[9rem] border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                                                <option value="">Pilih nilai</option>
                                                @foreach($this->conditionValueOptions[$condition['field']] as $vid => $vlabel)
                                                    <option value="{{ $vid }}">{{ $vlabel }}</option>
                                                @endforeach
                                            </select>
                                        @elseif(($this->conditionFields[$condition['field'] ?? '']['type'] ?? null) === 'number')
                                            <div class="flex-1 min-w-[9rem]">
                                                <x-rupiah-input wireModel="conditions.{{ $ci }}.value" placeholder="0" />
                                            </div>
                                        @else
                                            <div class="flex-1 min-w-[9rem]">
                                                <x-text-input wire:model="conditions.{{ $ci }}.value" class="block w-full" placeholder="Nilai" />
                                            </div>
                                        @endif

                                        @if(($condition['operator'] ?? '') === 'between')
                                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">s.d.</span>
                                            <div class="flex-1 min-w-[8rem]">
                                                @if(($this->conditionFields[$condition['field'] ?? '']['type'] ?? null) === 'number')
                                                    <x-rupiah-input wireModel="conditions.{{ $ci }}.value2" placeholder="0" />
                                                @else
                                                    <x-text-input wire:model="conditions.{{ $ci }}.value2" class="block w-full" placeholder="Nilai akhir" />
                                                @endif
                                            </div>
                                        @endif

                                        <button type="button" wire:click="removeCondition({{ $ci }})" wire:key="cond-del-{{ $ci }}"
                                            class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 dark:hover:text-red-400 transition-colors flex-shrink-0"
                                            title="Hapus kondisi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    @if($errors->has("conditions.$ci.field") || $errors->has("conditions.$ci.operator") || $errors->has("conditions.$ci.value") || $errors->has("conditions.$ci.value2"))
                                        <div class="px-3 pb-3 -mt-1 space-y-0.5">
                                            <x-input-error :messages="$errors->get("conditions.$ci.field")" />
                                            <x-input-error :messages="$errors->get("conditions.$ci.operator")" />
                                            <x-input-error :messages="$errors->get("conditions.$ci.value")" />
                                            <x-input-error :messages="$errors->get("conditions.$ci.value2")" />
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 px-4 py-5 text-center">
                                <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                <p class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400">Tanpa kondisi — alur ini menjadi <span class="text-gray-700 dark:text-gray-200">fallback</span> untuk semua dokumen.</p>
                            </div>
                        @endforelse

                        <button type="button" wire:click="addCondition" wire:key="btn-add-condition"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Tambah Kondisi
                        </button>
                    </div>
                </div>

                {{-- Designer: Pool + Steps --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Tahapan Persetujuan</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Seret user ke tahap, atau klik nama user lalu pilih tahapnya</span>
                    </div>
                    <x-input-error :messages="$errors->get('steps')" class="mb-3" />

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        {{-- User Pool --}}
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700 p-3 sticky top-4">
                                <div class="relative mb-3">
                                    <x-text-input type="text" wire:model.live.debounce.300ms="approverSearch" placeholder="Cari user..." class="block w-full pl-8" />
                                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <div class="space-y-1.5 max-h-96 overflow-y-auto custom-scrollbar">
                                    @forelse($this->userPool as $user)
                                        <div wire:key="pool-{{ $user->id }}" draggable="true"
                                            @dragstart="$event.dataTransfer.setData('text/plain', '{{ $user->id }}')"
                                            class="flex items-center gap-2 px-2.5 py-2 bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 cursor-grab active:cursor-grabbing hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-sm transition-all group"
                                            title="Seret ke tahap">
                                            <span class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs font-bold flex items-center justify-center flex-shrink-0">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                                                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                                            </div>
                                            <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 ml-auto flex-shrink-0 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                                        </div>
                                    @empty
                                        <p class="text-xs text-gray-400 dark:text-gray-500 text-center py-4">Tidak ada user.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Steps --}}
                        <div class="lg:col-span-3 space-y-4">
                            @foreach($steps as $i => $step)
                                <div wire:key="step-{{ $i }}"
                                    @dragover.prevent="$el.classList.add('ring-2', 'ring-blue-400')"
                                    @dragleave.prevent="$el.classList.remove('ring-2', 'ring-blue-400')"
                                    @drop.prevent="$wire.addApprover({{ $i }}, Number($event.dataTransfer.getData('text/plain'))); $el.classList.remove('ring-2', 'ring-blue-400')"
                                    class="bg-gray-50 dark:bg-gray-900/50 rounded-lg border {{ $errors->has("steps.$i.approver_ids") ? 'border-red-300 dark:border-red-700' : 'border-gray-200 dark:border-gray-700' }} transition-shadow">
                                    <div class="p-4">
                                        <div class="flex items-start gap-3">
                                            {{-- Step number + reorder --}}
                                            <div class="flex flex-col items-center gap-1 flex-shrink-0">
                                                <span class="w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                                                <div class="flex flex-col">
                                                    <button type="button" wire:click="moveStep({{ $i }}, -1)" wire:key="step-up-{{ $i }}" @disabled($i === 0)
                                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 disabled:opacity-30 p-0.5" title="Naikkan">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                                    </button>
                                                    <button type="button" wire:click="moveStep({{ $i }}, 1)" wire:key="step-down-{{ $i }}" @disabled($i === count($steps) - 1)
                                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 disabled:opacity-30 p-0.5" title="Turunkan">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="flex-1 min-w-0 space-y-3">
                                                <div class="flex flex-col sm:flex-row gap-3">
                                                    {{-- Nama tahap --}}
                                                    <div class="flex-1">
                                                        <x-text-input wire:model="steps.{{ $i }}.name" class="block w-full" placeholder="Nama tahap (mis. Review Manager)" />
                                                        <x-input-error :messages="$errors->get("steps.$i.name")" class="mt-1" />
                                                    </div>
                                                    {{-- Mode --}}
                                                    <div class="flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden text-xs font-medium self-start">
                                                        <button type="button" wire:click="$set('steps.{{ $i }}.mode', 'any')" wire:key="mode-any-{{ $i }}"
                                                            class="px-3 py-2 transition-colors {{ $step['mode'] === 'any' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                                                            title="Cukup satu approver menyetujui">Salah Satu</button>
                                                        <button type="button" wire:click="$set('steps.{{ $i }}.mode', 'all')" wire:key="mode-all-{{ $i }}"
                                                            class="px-3 py-2 transition-colors border-l border-gray-300 dark:border-gray-600 {{ $step['mode'] === 'all' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                                                            title="Semua approver harus menyetujui">Semua</button>
                                                    </div>
                                                </div>

                                                {{-- Approver chips (dropzone) --}}
                                                <div class="min-h-12 flex flex-wrap items-center gap-2 p-2 rounded-md border-2 border-dashed {{ empty($step['approver_ids']) ? 'border-gray-300 dark:border-gray-600' : 'border-transparent' }}">
                                                    @forelse($step['approver_ids'] as $uid)
                                                        @php $u = $this->usersById[$uid] ?? null; @endphp
                                                        @if($u)
                                                            <span wire:key="chip-{{ $i }}-{{ $uid }}" class="inline-flex items-center gap-1.5 pl-1.5 pr-2 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200 rounded-full text-xs font-medium">
                                                                <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                                                {{ $u->name }}
                                                                <button type="button" wire:click="removeApprover({{ $i }}, {{ $uid }})" wire:key="chip-x-{{ $i }}-{{ $uid }}"
                                                                    class="text-blue-500 hover:text-blue-800 dark:hover:text-blue-100 ml-0.5" title="Hapus">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                </button>
                                                            </span>
                                                        @endif
                                                    @empty
                                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic px-1">Seret user ke sini</span>
                                                    @endforelse
                                                </div>
                                                <x-input-error :messages="$errors->get("steps.$i.approver_ids")" class="mt-1" />
                                            </div>

                                            {{-- Delete step --}}
                                            <button type="button" wire:click="removeStep({{ $i }})" wire:key="step-del-{{ $i }}"
                                                class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 p-1 flex-shrink-0" title="Hapus tahap">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <button type="button" wire:click="addStep" wire:key="btn-add-step"
                                class="w-full py-3 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-500 dark:text-gray-400 hover:border-blue-400 hover:text-blue-600 dark:hover:border-blue-500 dark:hover:text-blue-400 transition-colors inline-flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tambah Tahap
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action bar --}}
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-end gap-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 px-5 py-4">
            <x-cancel-button wire:click="cancel" target="cancel" wire:key="btn-cancel" variant="secondary" size="lg" class="w-full sm:w-auto" />
            <x-loading-button type="submit" target="save" wire:key="btn-save" variant="primary" size="lg" loadingText="Menyimpan..." class="w-full sm:w-auto">
                {{ $editMode ? 'Update' : 'Simpan' }}
            </x-loading-button>
        </div>
    </form>
</div>
