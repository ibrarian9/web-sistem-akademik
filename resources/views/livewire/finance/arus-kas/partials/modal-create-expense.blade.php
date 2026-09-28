<!-- MODAL: Catat Kas Keluar Operasional -->
<x-floating-card 
    :show="$showExpenseModal" 
    title="Catat Kas Keluar Operasional" 
    subtitle="Dokumentasikan beban transaksi belanja dan operasional yayasan secara tertib dan akuntabel."
    badge="KAS KELUAR YAYASAN"
    badgeVariant="rose"
    icon="arrow-up-right"
    maxWidth="max-w-xl"
    closeAction="closeExpenseModal"
>
    <form wire:submit.prevent="saveExpense" class="space-y-4 font-sans">
        <!-- Baris 1: Tanggal Transaksi -->
        <div>
            <label for="expense_tgl" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                Tanggal Transaksi <span class="text-rose-500 font-bold">*</span>
            </label>
            <input 
                type="date" 
                id="expense_tgl" 
                wire:model="tanggal_keluar" 
                class="w-full bg-white border @error('tanggal_keluar') border-rose-500 ring-1 ring-rose-500 @else border-stone-300 @enderror rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition shadow-2xs" 
            />
            @error('tanggal_keluar') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Baris 2: Kategori Pengeluaran -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="expense_kat" class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Kategori Pengeluaran <span class="text-rose-500 font-bold">*</span>
                </label>
                <button 
                    type="button" 
                    wire:click="$toggle('is_kategori_kustom')" 
                    class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 transition cursor-pointer"
                >
                    {{ $is_kategori_kustom ? 'Pilih Kategori Terdaftar' : '+ Kategori Baru' }}
                </button>
            </div>
            @if(!$is_kategori_kustom)
                <select 
                    id="expense_kat" 
                    wire:model="kategori_pengeluaran_id" 
                    class="w-full bg-white border @error('kategori_pengeluaran_id') border-rose-500 ring-1 ring-rose-500 @else border-stone-300 @enderror rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition shadow-2xs"
                >
                    @foreach ($kategoriKeluarOptions as $c)
                        <option value="{{ $c['id'] }}">{{ $c['nama'] }}</option>
                    @endforeach
                </select>
                @error('kategori_pengeluaran_id') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            @else
                <input 
                    type="text" 
                    id="expense_kat_kustom" 
                    wire:model="kategori_keluar_kustom" 
                    placeholder="Nama kategori pengeluaran baru" 
                    class="w-full bg-white border @error('kategori_keluar_kustom') border-rose-500 ring-1 ring-rose-500 @else border-stone-300 @enderror rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition shadow-2xs" 
                />
                @error('kategori_keluar_kustom') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            @endif
        </div>

        <!-- Nominal Pengeluaran -->
        <div>
            <x-input-currency
                id="expense_nom"
                name="jumlah_keluar"
                wire:model="jumlah_keluar"
                label="Nominal Pengeluaran"
                placeholder="0"
                required
            />
        </div>

        <!-- Keterangan Pengeluaran -->
        <div>
            <label for="expense_ket" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                Keterangan Pengeluaran <span class="text-rose-500 font-bold">*</span>
            </label>
            <textarea 
                id="expense_ket" 
                wire:model="keterangan_keluar" 
                rows="3" 
                placeholder="Tuliskan rincian kebutuhan belanja operasional secara jelas..." 
                class="w-full bg-white border @error('keterangan_keluar') border-rose-500 ring-1 ring-rose-500 @else border-stone-300 @enderror rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition shadow-2xs resize-none"
            ></textarea>
            @error('keterangan_keluar') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Upload Foto Bukti Transaksi -->
        <div>
            <label for="expense_bukti" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                <span>Foto Bukti Transaksi</span>
                <span class="text-[10px] text-stone-500 normal-case font-semibold">Opsional : Maksimal 2MB format JPG, PNG, atau WEBP</span>
            </label>
            <div class="relative">
                <input 
                    type="file" 
                    id="expense_bukti" 
                    wire:model="bukti_keluar" 
                    accept="image/jpeg,image/png,image/jpg,image/webp" 
                    class="w-full bg-stone-50 hover:bg-stone-100/80 border border-dashed border-stone-300 rounded-xl px-3.5 py-2 text-stone-800 text-xs font-medium file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 transition shadow-2xs cursor-pointer" 
                />
            </div>
            <div wire:loading wire:target="bukti_keluar" class="text-xs text-emerald-700 font-bold mt-1.5 flex items-center gap-1.5">
                <x-lucide-loader-2 class="w-3.5 h-3.5 animate-spin" />
                <span>Sedang memproses file foto...</span>
            </div>
            @error('bukti_keluar') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror

            @if ($bukti_keluar)
                <div class="mt-2.5 p-3 bg-stone-50 rounded-xl border border-stone-200 flex items-center justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3">
                        <img src="{{ $bukti_keluar->temporaryUrl() }}" alt="Pratinjau Foto Bukti" class="w-12 h-12 object-cover rounded-lg border border-stone-300 shadow-2xs" />
                        <div class="text-xs">
                            <span class="font-bold text-stone-800 block">Pratinjau Foto Terpilih</span>
                            <span class="text-stone-500 text-[11px]">Foto siap disimpan ke sistem</span>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        wire:click="$set('bukti_keluar', null)" 
                        class="px-3 py-1.5 text-xs font-bold text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 rounded-lg shadow-2xs transition shrink-0 cursor-pointer"
                    >
                        Hapus Foto
                    </button>
                </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
            <x-button type="button" variant="secondary" size="sm" wire:click="closeExpenseModal">
                Batal
            </x-button>
            <x-button type="submit" variant="danger-solid" size="sm" icon="check" loadingTarget="saveExpense">
                Simpan Pengeluaran
            </x-button>
        </div>
    </form>
</x-floating-card>
