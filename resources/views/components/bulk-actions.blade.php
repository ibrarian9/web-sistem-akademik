@props([
    'selectedCount' => 0,
    'deleteAction' => 'bulkDelete',
    'cancelAction' => 'resetSelection',
    'confirmText' => 'Apakah Anda yakin ingin menghapus seluruh data yang dipilih?',
])

@if ($selectedCount > 0)
    <div class="fixed bottom-6 inset-x-0 z-[99980] flex justify-center px-4 pointer-events-none animate-bounce-short">
        <div class="bg-stone-900 text-white px-5 py-3 rounded-2xl shadow-2xl border border-stone-700 flex items-center justify-between gap-6 pointer-events-auto max-w-xl w-full">
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-xl bg-emerald-500 text-stone-950 font-black text-xs flex items-center justify-center shrink-0">
                    {{ $selectedCount }}
                </span>
                <div>
                    <span class="text-xs font-bold text-white block">{{ $selectedCount }} item dipilih</span>
                    <span class="text-[10px] text-stone-400">Pilih tindakan massal untuk data ini</span>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                {{ $slot }}

                @if ($deleteAction)
                    <x-button 
                        variant="danger-solid" 
                        size="xs" 
                        icon="trash-2" 
                        wire:click="{{ $deleteAction }}" 
                        data-confirm="{{ $confirmText }}"
                        loadingTarget="{{ $deleteAction }}">
                        Hapus Terpilih
                    </x-button>
                @endif

                <button 
                    type="button" 
                    wire:click="{{ $cancelAction }}" 
                    class="p-1.5 text-stone-400 hover:text-white rounded-lg transition"
                    title="Batalkan Pilihan">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>
    </div>
@endif
