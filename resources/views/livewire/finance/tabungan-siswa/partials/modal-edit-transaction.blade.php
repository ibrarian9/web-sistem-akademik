<!-- Floating Card Form Edit Transaksi Tabungan (Founder & Finance) - Top-level Overlay -->
<x-floating-card 
    :show="$showEditTransactionModal" 
    title="Edit Transaksi Tabungan" 
    :subtitle="'Koreksi data mutasi untuk: ' . $edit_siswa_nama"
    badge="EDIT MUTASI TABUNGAN"
    badgeVariant="indigo"
    icon="edit-3"
    maxWidth="max-w-lg"
    closeAction="closeEditTransactionModal"
    zIndex="z-[99998]"
>
    <form wire:submit.prevent="saveEditTransaction" class="space-y-4 font-sans">
        <!-- Jenis Transaksi -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Jenis Transaksi</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer text-xs font-bold select-none transition {{ $edit_jenis === 'setor' ? 'bg-emerald-50 border-emerald-500 text-emerald-900 ring-2 ring-emerald-500/20 shadow-2xs' : 'bg-stone-50 border-stone-300 text-stone-600' }}">
                    <input type="radio" wire:model.live="edit_jenis" value="setor" class="hidden" />
                    <x-lucide-arrow-down-left class="w-4 h-4 text-emerald-600" />
                    <span>Setor Tabungan (+)</span>
                </label>
                <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer text-xs font-bold select-none transition {{ $edit_jenis === 'tarik' ? 'bg-amber-50 border-amber-500 text-amber-900 ring-2 ring-amber-500/20 shadow-2xs' : 'bg-stone-50 border-stone-300 text-stone-600' }}">
                    <input type="radio" wire:model.live="edit_jenis" value="tarik" class="hidden" />
                    <x-lucide-arrow-up-right class="w-4 h-4 text-amber-600" />
                    <span>Tarik Tabungan (-)</span>
                </label>
            </div>
        </div>

        <!-- Nominal -->
        <x-input-currency 
            label="Nominal Transaksi Baru (Rp)" 
            name="edit_nominal" 
            wire:model="edit_nominal" 
            placeholder="Contoh: 50.000" 
            required 
        />

        <!-- Tanggal Transaksi -->
        <x-input 
            type="date" 
            label="Tanggal Transaksi" 
            name="edit_tanggal" 
            wire:model="edit_tanggal" 
            required 
        />

        <!-- Keterangan -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Catatan / Keterangan</label>
            <textarea wire:model="edit_keterangan" rows="2" placeholder="Catatan transaksi tabungan (opsional)..." class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"></textarea>
        </div>

        @if(auth()->user()->role?->nama === 'finance')
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-start gap-2">
                <x-lucide-alert-circle class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                <div>
                    <span class="font-bold">Persetujuan Diperlukan:</span> Perubahan catatan mutasi ini akan diajukan ke Super Admin atau Super Admin 2 sebelum saldo dihitung ulang.
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Alasan Perubahan <span class="text-rose-500">*</span>
                </label>
                <textarea 
                    wire:model="edit_alasan" 
                    rows="2" 
                    placeholder="Jelaskan alasan koreksi transaksi tabungan..." 
                    class="w-full px-3.5 py-2.5 bg-white border @error('edit_alasan') border-rose-500 ring-1 ring-rose-500 @else border-stone-300 @enderror rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"
                ></textarea>
                @error('edit_alasan')
                    <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
            <x-button variant="secondary" size="md" wire:click="closeEditTransactionModal">
                Batal
            </x-button>
            <x-button variant="primary" size="md" type="submit" loadingTarget="saveEditTransaction">
                {{ auth()->user()->role?->nama === 'finance' ? 'Ajukan Persetujuan' : 'Simpan Perubahan' }}
            </x-button>
        </div>
    </form>
</x-floating-card>
