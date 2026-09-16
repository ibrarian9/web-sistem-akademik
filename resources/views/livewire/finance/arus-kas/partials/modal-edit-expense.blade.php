<!-- MODAL 3: Edit Pengeluaran & Bukti Pembayaran -->
<x-floating-card 
    :show="$showEditExpenseModal" 
    title="Edit Pengeluaran & Bukti Pembayaran" 
    subtitle="Perbarui nominal, keterangan, atau lampirkan foto bukti transaksi baru."
    badge="EDIT PENGELUARAN"
    badgeVariant="amber"
    icon="edit-3"
    maxWidth="max-w-lg"
    closeAction="closeEditExpenseModal"
>
    <form wire:submit.prevent="updateExpense" class="space-y-4">
        <div>
            <label for="edit_exp_tgl" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Tanggal Transaksi</label>
            <input type="date" id="edit_exp_tgl" wire:model="edit_tanggal" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
            @error('edit_tanggal') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="edit_exp_kat" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Kategori Pengeluaran</label>
            <select id="edit_exp_kat" wire:model="edit_kategori_pengeluaran_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                @foreach ($kategoriKeluarOptions as $c)
                    <option value="{{ $c['id'] }}">{{ $c['nama'] }}</option>
                @endforeach
            </select>
            @error('edit_kategori_pengeluaran_id') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <x-input-currency
            id="edit_exp_nom"
            name="edit_jumlah"
            wire:model="edit_jumlah"
            label="Nominal Pengeluaran (Rp)"
            placeholder="Contoh: 150.000"
            required
        />

        <div>
            <label for="edit_exp_ket" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Keterangan / Uraian Belanja</label>
            <textarea id="edit_exp_ket" wire:model="edit_keterangan" rows="3" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs"></textarea>
            @error('edit_keterangan') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Existing Photo or Upload New -->
        <div>
            <label class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                <span>Foto Bukti Transaksi (Struk / Bon)</span>
                <span class="text-[10px] text-stone-500 normal-case font-semibold">Opsional • Maks 2MB (JPG, PNG, WEBP)</span>
            </label>

            @if ($edit_existing_bukti && !$edit_bukti_keluar)
                <div class="p-3 bg-stone-50 rounded-xl border border-stone-200 flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('storage/' . $edit_existing_bukti) }}" alt="Bukti Tersimpan" class="w-12 h-12 object-cover rounded-lg border border-stone-300 shadow-2xs" />
                        <div>
                            <span class="text-xs font-bold text-stone-900 block">Bukti Foto Tersimpan</span>
                            <a href="{{ asset('storage/' . $edit_existing_bukti) }}" target="_blank" class="text-[11px] text-emerald-700 hover:underline font-semibold flex items-center gap-1">
                                <x-lucide-external-link class="w-3 h-3" /> Buka Resolusi Penuh
                            </a>
                        </div>
                    </div>
                    <button type="button" wire:click="deleteEditBukti" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus foto ini">
                        <x-lucide-trash-2 class="w-4 h-4" />
                    </button>
                </div>
            @endif

            <input type="file" wire:model="edit_bukti_keluar" accept="image/jpeg,image/png,image/jpg,image/webp" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-medium file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100 transition shadow-2xs cursor-pointer" />

            <div wire:loading wire:target="edit_bukti_keluar" class="text-xs text-amber-700 font-bold mt-1.5 flex items-center gap-1.5">
                <x-lucide-loader-2 class="w-3.5 h-3.5 animate-spin" />
                <span>Sedang mengunggah file foto baru...</span>
            </div>
            @error('edit_bukti_keluar') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror

            @if ($edit_bukti_keluar)
                <div class="mt-2.5 p-2.5 bg-amber-50/60 rounded-xl border border-amber-200 flex items-center justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3">
                        <img src="{{ $edit_bukti_keluar->temporaryUrl() }}" alt="Pratinjau Foto Baru" class="w-12 h-12 object-cover rounded-lg border border-amber-300 shadow-2xs" />
                        <div class="text-xs">
                            <span class="font-bold text-stone-800 block">Foto Baru Terpilih</span>
                            <span class="text-stone-500 text-[11px]">Akan menggantikan foto saat disimpan</span>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('edit_bukti_keluar', null)" class="px-2.5 py-1 text-xs font-bold text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 rounded-lg shadow-2xs transition shrink-0 cursor-pointer">
                        Batal
                    </button>
                </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
            <x-button type="button" variant="secondary" size="sm" wire:click="closeEditExpenseModal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="sm" icon="check">
                Simpan Perubahan
            </x-button>
        </div>
    </form>
</x-floating-card>
