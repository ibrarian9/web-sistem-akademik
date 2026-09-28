<!-- MODAL: Catat Kas Masuk Yayasan -->
<x-floating-card 
    :show="$showIncomeModal" 
    title="Catat Kas Masuk Yayasan" 
    subtitle="Dokumentasikan penerimaan kas non-SPP seperti infaq, sedekah, donasi, dan bantuan secara tertib dan akuntabel."
    badge="KAS MASUK YAYASAN"
    badgeVariant="emerald"
    icon="arrow-down-left"
    maxWidth="max-w-xl"
    closeAction="closeIncomeModal"
>
    <form wire:submit.prevent="saveIncome" class="space-y-4 font-sans">
        <!-- Baris 1: Tanggal Penerimaan -->
        <div>
            <label for="income_tgl" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                Tanggal Penerimaan <span class="text-rose-500 font-bold">*</span>
            </label>
            <input 
                type="date" 
                id="income_tgl" 
                wire:model="tanggal_masuk" 
                class="w-full bg-white border @error('tanggal_masuk') border-rose-500 ring-1 ring-rose-500 @else border-stone-300 @enderror rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition shadow-2xs" 
            />
            @error('tanggal_masuk') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Baris 2: Kategori Penerimaan -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="income_kat" class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Kategori Penerimaan <span class="text-rose-500 font-bold">*</span>
                </label>
                <button 
                    type="button" 
                    wire:click="$toggle('is_kategori_masuk_kustom')" 
                    class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 transition cursor-pointer"
                >
                    {{ $is_kategori_masuk_kustom ? 'Pilih Kategori Terdaftar' : '+ Kategori Baru' }}
                </button>
            </div>
            @if(!$is_kategori_masuk_kustom)
                <select 
                    id="income_kat" 
                    wire:model="kategori_masuk" 
                    class="w-full bg-white border @error('kategori_masuk') border-rose-500 ring-1 ring-rose-500 @else border-stone-300 @enderror rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition shadow-2xs"
                >
                    @foreach ($kategoriMasukOptions as $kat)
                        <option value="{{ $kat }}">{{ $kat }}</option>
                    @endforeach
                </select>
                @error('kategori_masuk') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            @else
                <input 
                    type="text" 
                    id="income_kat_kustom" 
                    wire:model="kategori_masuk_kustom" 
                    placeholder="Nama kategori penerimaan baru" 
                    class="w-full bg-white border @error('kategori_masuk_kustom') border-rose-500 ring-1 ring-rose-500 @else border-stone-300 @enderror rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition shadow-2xs" 
                />
                @error('kategori_masuk_kustom') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            @endif
        </div>

        <!-- Baris 3: Nominal Penerimaan -->
        <div>
            <x-input-currency
                id="income_nom"
                name="jumlah_masuk"
                wire:model="jumlah_masuk"
                label="Nominal Penerimaan"
                placeholder="0"
                required
            />
        </div>

        <!-- Baris 4: Keterangan Penerimaan -->
        <div>
            <label for="income_ket" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                Keterangan Penerimaan <span class="text-rose-500 font-bold">*</span>
            </label>
            <textarea 
                id="income_ket" 
                wire:model="keterangan_masuk" 
                rows="3" 
                placeholder="Tuliskan nama donatur, sumber dana, atau keterangan infaq secara jelas..." 
                class="w-full bg-white border @error('keterangan_masuk') border-rose-500 ring-1 ring-rose-500 @else border-stone-300 @enderror rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition shadow-2xs resize-none"
            ></textarea>
            @error('keterangan_masuk') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Modal Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
            <x-button type="button" variant="secondary" size="sm" wire:click="closeIncomeModal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="sm" icon="check" loadingTarget="saveIncome">
                Simpan Kas Masuk
            </x-button>
        </div>
    </form>
</x-floating-card>
