<!-- Floating Card: Edit Pengeluaran & Bukti Pembayaran -->
@if ($showEditModal)
    <x-floating-card 
        :show="true" 
        title="Edit Pengeluaran & Bukti Pembayaran" 
        subtitle="Perbarui data beban pengeluaran operasional serta lampiran foto bukti nota / struk / transfer." 
        badge="EDIT PENGELUARAN" 
        badgeVariant="rose" 
        icon="edit" 
        maxWidth="max-w-lg" 
        closeAction="closeEditModal"
    >
        <form wire:submit.prevent="updateExpense" class="space-y-4 text-xs">
            <!-- Tanggal Pengeluaran -->
            <div>
                <label class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Tanggal Transaksi</label>
                <input type="date" wire:model="edit_tanggal" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
                @error('edit_tanggal') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Kategori Pengeluaran -->
            <div>
                <label class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Kategori Pengeluaran</label>
                <select wire:model="edit_kategori_pengeluaran_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    @foreach ($categories as $c)
                        <option value="{{ $c['id'] }}">{{ $c['nama'] }}</option>
                    @endforeach
                </select>
                @error('edit_kategori_pengeluaran_id') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Nominal Pengeluaran -->
            <x-input-currency
                id="edit_jumlah"
                name="edit_jumlah"
                wire:model="edit_jumlah"
                label="Nominal Pengeluaran (Rp)"
                placeholder="Contoh: 150.000"
                required
            />

            <!-- Keterangan / Deskripsi Beban -->
            <div>
                <label class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Keterangan / Uraian Belanja</label>
                <textarea wire:model="edit_keterangan" rows="3" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs"></textarea>
                @error('edit_keterangan') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Foto Bukti Pengeluaran Existing -->
            @if ($edit_existing_bukti)
                <div class="space-y-1.5 p-3 bg-stone-50 border border-stone-200 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-stone-700">Foto Bukti Saat Ini</span>
                        <button type="button" wire:click="deleteEditBukti" wire:confirm="Hapus foto bukti pengeluaran ini?" class="text-[11px] text-rose-600 hover:text-rose-800 font-bold inline-flex items-center gap-1 cursor-pointer">
                            <x-lucide-trash-2 class="w-3 h-3" />
                            <span>Hapus Foto</span>
                        </button>
                    </div>
                    <div class="rounded-lg overflow-hidden border border-stone-300 bg-stone-900 p-1">
                        <img src="{{ asset('storage/' . $edit_existing_bukti) }}" alt="Bukti" class="w-full max-h-48 object-contain mx-auto rounded" />
                    </div>
                </div>
            @endif

            <!-- Upload Bukti Baru / Pengganti -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-600 uppercase tracking-wider">
                    {{ $edit_existing_bukti ? 'Ganti Foto Bukti Pengeluaran' : 'Unggah Foto Bukti Pengeluaran (Struk/TF)' }}
                </label>

                @if ($edit_bukti_foto)
                    <div class="p-2.5 bg-emerald-50 border border-emerald-300 rounded-xl flex items-center justify-between gap-2 shadow-2xs">
                        <div class="flex items-center gap-2 min-w-0">
                            <img src="{{ $edit_bukti_foto->temporaryUrl() }}" alt="Preview" class="w-10 h-10 object-cover rounded-lg border border-emerald-200 shadow-2xs shrink-0" />
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-emerald-950 block truncate">{{ $edit_bukti_foto->getClientOriginalName() }}</span>
                                <span class="text-[10px] text-emerald-700 font-semibold">{{ number_format($edit_bukti_foto->getSize() / 1024, 1) }} KB</span>
                            </div>
                        </div>
                        <button type="button" wire:click="$set('edit_bukti_foto', null)" class="px-2 py-1 text-[11px] font-bold text-rose-700 bg-white border border-rose-200 rounded-lg shrink-0 cursor-pointer">
                            Batal
                        </button>
                    </div>
                @else
                    <div class="relative border-2 border-dashed border-stone-300 hover:border-emerald-500 rounded-xl p-3 bg-stone-50 hover:bg-emerald-50/20 text-center transition group cursor-pointer">
                        <input 
                            type="file" 
                            wire:model="edit_bukti_foto" 
                            accept="image/jpeg,image/png,image/jpg,image/webp" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                        />
                        <div class="flex items-center justify-center gap-2 pointer-events-none text-stone-600 group-hover:text-emerald-800">
                            <x-lucide-camera class="w-4 h-4 text-emerald-600" />
                            <span class="text-xs font-bold">Pilih file foto baru (Maks. 2MB, JPG/PNG/WEBP)</span>
                        </div>
                    </div>
                @endif
                <div wire:loading wire:target="edit_bukti_foto" class="text-[11px] text-emerald-700 font-bold mt-1 flex items-center gap-1.5">
                    <span class="animate-spin inline-block w-3 h-3 border-2 border-emerald-600 border-t-transparent rounded-full"></span>
                    <span>Mengunggah foto...</span>
                </div>
                @error('edit_bukti_foto') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                <x-button type="button" variant="secondary" size="sm" wire:click="closeEditModal">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="sm" icon="check" loadingTarget="updateExpense">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </x-floating-card>
@endif
