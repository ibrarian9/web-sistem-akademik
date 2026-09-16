<!-- MODAL 1: Catat Kas Masuk Yayasan -->
<x-floating-card 
    :show="$showIncomeModal" 
    title="Catat Kas Masuk Yayasan" 
    subtitle="Dokumentasikan penerimaan non-SPP (Infaq, Sedekah Subuh, Maghrib Mengaji, Donatur, Hibah)."
    badge="KAS MASUK YAYASAN"
    badgeVariant="emerald"
    icon="arrow-down-left"
    maxWidth="max-w-lg"
    closeAction="closeIncomeModal"
>
    <form wire:submit.prevent="saveIncome" class="space-y-4">
        <div>
            <label for="income_tgl" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Tanggal Penerimaan</label>
            <input type="date" id="income_tgl" wire:model="tanggal_masuk" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
            @error('tanggal_masuk') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="income_kat" class="block text-xs font-bold text-stone-600 uppercase tracking-wider">Kategori Penerimaan</label>
                <button type="button" wire:click="$toggle('is_kategori_masuk_kustom')" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-900 hover:underline cursor-pointer">
                    {{ $is_kategori_masuk_kustom ? '← Pilih dari Daftar Kategori' : '+ Tambah Kategori Baru' }}
                </button>
            </div>
            @if(!$is_kategori_masuk_kustom)
                <select id="income_kat" wire:model="kategori_masuk" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    @foreach ($kategoriMasukOptions as $kat)
                        <option value="{{ $kat }}">{{ $kat }}</option>
                    @endforeach
                </select>
                @error('kategori_masuk') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            @else
                <input type="text" id="income_kat_kustom" wire:model="kategori_masuk_kustom" placeholder="Ketik nama kategori penerimaan baru..." class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
                @error('kategori_masuk_kustom') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            @endif
        </div>

        <x-input-currency
            id="income_nom"
            name="jumlah_masuk"
            wire:model="jumlah_masuk"
            label="Nominal Penerimaan (Rp)"
            placeholder="Contoh: 500.000"
            required
        />

        <div>
            <label for="income_ket" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Keterangan / Nama Donatur</label>
            <textarea id="income_ket" wire:model="keterangan_masuk" rows="3" placeholder="Tulis nama donatur, acara, atau keterangan infaq..." class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs"></textarea>
            @error('keterangan_masuk') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
            <x-button type="button" variant="secondary" size="sm" wire:click="closeIncomeModal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="sm" icon="check">
                Simpan Kas Masuk
            </x-button>
        </div>
    </form>
</x-floating-card>
