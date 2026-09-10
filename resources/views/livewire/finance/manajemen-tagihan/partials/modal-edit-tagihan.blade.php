<!-- FLOATING CARD: FORM EDIT TAGIHAN SISWA (FOUNDER & FINANCE) -->
<x-floating-card 
    :show="$showEditModal" 
    title="Edit Tagihan Siswa" 
    :subtitle="'Ubah rincian tagihan untuk: ' . $edit_siswa_nama"
    badge="EDIT TAGIHAN"
    badgeVariant="indigo"
    icon="edit-3"
    maxWidth="max-w-lg"
    closeAction="closeEditModal"
>
    <form wire:submit.prevent="saveEditTagihan" class="space-y-4">
        @if ($edit_total_dibayar > 0)
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs font-medium flex items-center gap-2">
                <x-lucide-alert-triangle class="w-4 h-4 text-amber-600 shrink-0" />
                <span>Tagihan ini sudah dibayar sebesar <strong>Rp {{ number_format($edit_total_dibayar, 0, ',', '.') }}</strong>. Nominal baru tidak boleh lebih kecil dari jumlah yang sudah dibayar.</span>
            </div>
        @endif

        <!-- Jenis Tagihan -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Jenis Tagihan <span class="text-rose-500">*</span></label>
            <select wire:model="edit_jenis_tagihan_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">-- Pilih Kategori Tagihan --</option>
                @foreach ($jenisTagihans as $jt)
                    <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                @endforeach
            </select>
            @error('edit_jenis_tagihan_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Bulan Tagihan -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Bulan Tagihan <span class="text-rose-500">*</span></label>
                <select wire:model="edit_bulan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    @foreach ($bulanOptions as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
                @error('edit_bulan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Jatuh Tempo -->
            <x-input 
                type="date" 
                label="Jatuh Tempo" 
                name="edit_jatuh_tempo" 
                wire:model="edit_jatuh_tempo" 
                required 
            />
        </div>

        <!-- Nominal -->
        <x-input-currency 
            label="Nominal Tagihan Baru (Rp)" 
            name="edit_nominal" 
            wire:model="edit_nominal" 
            placeholder="Contoh: 350.000 (Isi 0 jika Bebas Biaya atau Beasiswa)" 
            hint="Jika diisi Rp 0, status tagihan otomatis menjadi Lunas." 
            required 
        />

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

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
            <x-button variant="secondary" size="md" wire:click="closeEditModal">
                Batal
            </x-button>
            <x-button variant="primary" size="md" type="submit" loadingTarget="saveEditTagihan">
                {{ auth()->user()->role?->nama === 'finance' ? 'Ajukan Perubahan (Butuh Approval)' : 'Simpan Perubahan' }}
            </x-button>
        </div>
    </form>
</x-floating-card>
