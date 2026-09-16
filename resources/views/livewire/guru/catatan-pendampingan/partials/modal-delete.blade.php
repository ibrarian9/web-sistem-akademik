@if ($deletingId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-xs">
        <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-2xl max-w-sm w-full space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <x-lucide-alert-triangle class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-stone-900">Hapus Catatan Pengamatan?</h3>
                    <p class="text-xs text-stone-500 font-medium">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-100">
                <x-button type="button" variant="secondary" size="sm" wire:click="cancelDelete">
                    Batal
                </x-button>
                <x-button type="button" variant="danger-solid" size="sm" icon="trash-2" wire:click="deleteRecord({{ $deletingId }})">
                    Hapus Catatan
                </x-button>
            </div>
        </div>
    </div>
@endif
