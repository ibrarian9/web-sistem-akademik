<!-- Modal Edit Tagihan -->
<x-floating-card 
    :show="$showEditModal"
    title="Edit Data Tagihan"
    subtitle="Ubah kategori, periode, jatuh tempo, atau nominal tagihan siswa."
    badge="EDIT TAGIHAN"
    badgeVariant="emerald"
    icon="edit-3"
    maxWidth="max-w-xl"
    closeAction="closeEditModal"
>
    <form wire:submit.prevent="updateTagihan" class="space-y-4 text-xs">
        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1">Jenis Kategori Tagihan <span class="text-rose-600">*</span></label>
            <select wire:model="edit_jenis_tagihan_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold p-2.5 focus:ring-2 focus:ring-emerald-600">
                @foreach ($jenisTagihans as $jt)
                    <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                @endforeach
            </select>
            @error('edit_jenis_tagihan_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Bulan Tagihan <span class="text-rose-600">*</span></label>
                <select wire:model="edit_bulan" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold p-2.5 focus:ring-2 focus:ring-emerald-600">
                    @foreach ($bulanOptions as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
                @error('edit_bulan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Jatuh Tempo <span class="text-rose-600">*</span></label>
                <input type="date" wire:model="edit_jatuh_tempo" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold p-2.5 focus:ring-2 focus:ring-emerald-600">
                @error('edit_jatuh_tempo') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <x-input-currency
            wire:model="edit_nominal"
            id="edit_nominal"
            name="edit_nominal"
            label="Nominal Tagihan (Rp)"
            placeholder="0"
            hint="Jika nominal diubah menjadi Rp 0, status tagihan otomatis menjadi Lunas."
            required
        />
        @if ($edit_total_dibayar > 0)
            <span class="text-[11px] text-amber-700 font-semibold block -mt-1 mb-2">
                Catatan: Siswa telah membayar Rp {{ number_format($edit_total_dibayar, 0, ',', '.') }}.
            </span>
        @endif

        @if (auth()->user()->role?->nama === 'finance')
            <div class="space-y-1.5 p-3.5 bg-amber-50/70 border border-amber-200 rounded-xl">
                <label class="block text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                    <x-lucide-shield-alert class="w-4 h-4 text-amber-600" />
                    Alasan Perubahan Tagihan (Wajib Persetujuan) <span class="text-rose-500">*</span>
                </label>
                <p class="text-[11px] text-amber-700">Perubahan oleh bagian Keuangan membutuhkan persetujuan dari Super Admin atau Super Admin 2.</p>
                <textarea wire:model="edit_alasan" rows="2" placeholder="Tuliskan alasan pengajuan perubahan nominal/tagihan ini..."
                          class="w-full px-3 py-2 text-xs bg-white border border-amber-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-stone-800"></textarea>
                @error('edit_alasan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>
        @endif

        <div class="flex justify-end gap-2 pt-3 border-t border-stone-200">
            <x-button type="button" variant="secondary" size="md" wire:click="closeEditModal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="md" icon="check" loadingTarget="updateTagihan">
                {{ auth()->user()->role?->nama === 'finance' ? 'Ajukan Perubahan (Butuh Approval)' : 'Simpan Perubahan' }}
            </x-button>
        </div>
    </form>
</x-floating-card>
