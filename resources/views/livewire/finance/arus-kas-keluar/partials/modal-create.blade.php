<!-- Floating Card: Catat Kas Keluar Operasional Baru -->
<x-floating-card 
    :show="$showCreateModal" 
    title="Catat Kas Keluar Operasional" 
    subtitle="Dokumentasikan pengeluaran kas non-BOS (ATK, Sarpras, Listrik/Air, Konsumsi, dsb.)." 
    badge="KAS KELUAR YAYASAN" 
    badgeVariant="rose" 
    icon="arrow-up-right" 
    maxWidth="max-w-lg" 
    closeAction="closeCreateModal"
>
    <form wire:submit.prevent="saveExpense" class="space-y-4">
        <!-- Tanggal Pengeluaran -->
        <div>
            <label for="tanggal" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Tanggal Transaksi</label>
            <input type="date" id="tanggal" wire:model="tanggal" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
            @error('tanggal') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Kategori Pengeluaran -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="kategori_pengeluaran_id" class="block text-xs font-bold text-stone-600 uppercase tracking-wider">Kategori Pengeluaran</label>
                <button type="button" wire:click="$toggle('is_kategori_kustom')" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-900 hover:underline cursor-pointer">
                    {{ $is_kategori_kustom ? '← Pilih dari Daftar Kategori' : '+ Tambah Kategori Baru' }}
                </button>
            </div>
            @if(!$is_kategori_kustom)
                <select id="kategori_pengeluaran_id" wire:model="kategori_pengeluaran_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    @foreach ($categories as $c)
                        <option value="{{ $c['id'] }}">{{ $c['nama'] }}</option>
                    @endforeach
                </select>
                @error('kategori_pengeluaran_id') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            @else
                <input type="text" id="kategori_keluar_kustom" wire:model="kategori_keluar_kustom" placeholder="Ketik nama kategori pengeluaran baru..." class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
                @error('kategori_keluar_kustom') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            @endif
        </div>

        <!-- Nominal Pengeluaran -->
        <x-input-currency
            id="jumlah"
            name="jumlah"
            wire:model="jumlah"
            label="Nominal Pengeluaran (Rp)"
            placeholder="Contoh: 150.000"
            required
        />

        <!-- Keterangan / Deskripsi Beban -->
        <div>
            <label for="keterangan" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Keterangan / Uraian Belanja</label>
            <textarea id="keterangan" wire:model="keterangan" rows="3" placeholder="Tulis rincian pembelian ATK, perbaikan sarpras, konsumsi rapat..." class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs"></textarea>
            @error('keterangan') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Upload Foto Bukti Pengeluaran (Struk / Nota / TF) -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-stone-600 uppercase tracking-wider">
                    Foto Bukti Pengeluaran (Struk / Nota / TF)
                </label>
                <span class="text-[10px] text-stone-400 font-semibold">Opsional • Maks. 2MB • Foto/Gambar</span>
            </div>

            @if ($bukti_foto)
                <div class="p-3 bg-emerald-50/70 border border-emerald-300 rounded-xl flex items-center justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="{{ $bukti_foto->temporaryUrl() }}" alt="Preview Bukti" class="w-12 h-12 object-cover rounded-lg border border-emerald-200 shadow-2xs shrink-0" />
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-emerald-950 block truncate">{{ $bukti_foto->getClientOriginalName() }}</span>
                            <span class="text-[10px] text-emerald-700 font-semibold">{{ number_format($bukti_foto->getSize() / 1024, 1) }} KB</span>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('bukti_foto', null)" class="px-2.5 py-1 text-xs font-bold text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 rounded-lg shadow-2xs transition shrink-0 cursor-pointer">
                        Hapus
                    </button>
                </div>
            @else
                <div class="relative border-2 border-dashed border-stone-300 hover:border-emerald-500 rounded-xl p-3.5 bg-stone-50/60 hover:bg-emerald-50/20 text-center transition group cursor-pointer">
                    <input 
                        type="file" 
                        wire:model="bukti_foto" 
                        accept="image/jpeg,image/png,image/jpg,image/webp" 
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                    />
                    <div class="flex items-center justify-center gap-2 pointer-events-none text-stone-600 group-hover:text-emerald-800">
                        <x-lucide-camera class="w-4 h-4 text-emerald-600" />
                        <span class="text-xs font-bold">Pilih foto struk / bukti transfer (Maks. 2MB)</span>
                    </div>
                    <div wire:loading wire:target="bukti_foto" class="absolute inset-0 bg-white/90 backdrop-blur-xs rounded-xl flex items-center justify-center z-20">
                        <span class="text-xs font-bold text-emerald-800 flex items-center gap-1.5">
                            <span class="animate-spin inline-block w-3.5 h-3.5 border-2 border-emerald-600 border-t-transparent rounded-full"></span>
                            Mengunggah foto...
                        </span>
                    </div>
                </div>
            @endif
            @error('bukti_foto') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Modal Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
            <x-button type="button" variant="secondary" size="sm" wire:click="closeCreateModal">
                Batal
            </x-button>
            <x-button type="submit" variant="danger-solid" size="sm" icon="check" loadingTarget="saveExpense">
                Simpan Kas Keluar
            </x-button>
        </div>
    </form>
</x-floating-card>
