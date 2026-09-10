{{-- 3. Edit Salary Modal dengan Format Titik Pemisah & Tanpa Panah --}}
<x-floating-card 
    :show="$showEditModal" 
    title="Ubah Rincian Honorarium Pegawai" 
    :subtitle="'Pegawai: ' . ($editGuruNama ?? '-')"
    badge="UBAH RINCIAN GAJI"
    badgeVariant="emerald"
    icon="file-edit"
    maxWidth="max-w-4xl"
    closeAction="closeEditModal"
>
    <div class="space-y-4 font-sans">
        @if ($editStatus === 'dibayar')
            <div class="p-3 bg-amber-50 border border-amber-300 rounded-xl text-xs text-amber-900 flex items-center gap-2">
                <x-lucide-alert-circle class="w-4 h-4 text-amber-700 shrink-0" />
                <span><strong>Gaji ini telah berstatus Dibayar:</strong> Mengubah nominal akan secara otomatis menyinkronkan nilai pengeluaran kas di Buku Kas Keuangan Yayasan.</span>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-6 gap-3 bg-stone-50 p-3.5 rounded-2xl border border-stone-200">
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Bulan Gaji</label>
                <select wire:model="editBulan" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                    @foreach ($listBulan as $bln)
                        <option value="{{ $bln }}">{{ $bln }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Tahun Gaji</label>
                <input type="number" wire:model="editTahun" min="2020" max="2035" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center" />
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Jabatan</label>
                <input type="text" wire:model="editJabatan" placeholder="Contoh: Mudir F3 / Guru" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Jam Kerja</label>
                <input type="text" wire:model="editJamKerja" placeholder="07.00-14.00 (Fleksibel)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Dibayar Oleh</label>
                <input type="text" wire:model="editSumberDana" placeholder="Yayasan" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Tanggal Bayar</label>
                <input type="date" wire:model="editTanggalBayar" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center" />
            </div>
        </div>

        <!-- Two-Column Breakdown: Penerimaan vs Potongan -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- A. PENERIMAAN (EARNINGS) -->
            <div class="bg-emerald-50/40 border border-emerald-200/80 rounded-2xl p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-emerald-200 pb-2">
                    <span class="text-xs font-extrabold text-emerald-900 uppercase tracking-wider">A. Penerimaan (Earnings)</span>
                    <span class="text-xs font-black text-emerald-800">Total: Rp {{ number_format($editTotalBruto, 0, ',', '.') }}</span>
                </div>

                <div class="space-y-2.5">
                    <x-input-currency
                        label="1. Gaji Pokok (Rp)"
                        wire:model.live.debounce.300ms="editGajiPokok"
                        placeholder="0"
                    />

                    <x-input-currency
                        label="2. Gaji Berkala (Rp)"
                        wire:model.live.debounce.300ms="editGajiBerkala"
                        placeholder="0"
                    />

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">3a. Pertemuan Ekskul</label>
                            <input type="number" min="0" wire:model.live.debounce.300ms="editJumlahEkskul" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center shadow-2xs" placeholder="0" />
                        </div>
                        <x-input-currency
                            label="3b. Honor Ekskul (Rp)"
                            wire:model.live.debounce.300ms="editHonorEkskul"
                            placeholder="0"
                        />
                    </div>

                    <x-input-currency
                        label="4. Incentive / Insentif Jabatan (Rp)"
                        wire:model.live.debounce.300ms="editInsentif"
                        placeholder="0"
                    />

                    <x-input-currency
                        label="5. Tunjangan BPJSTK (Rp)"
                        wire:model.live.debounce.300ms="editInsentifBpjs"
                        placeholder="0"
                    />

                    <x-input-currency
                        label="6. Insentif Maghrib Mengaji (Rp)"
                        wire:model.live.debounce.300ms="editInsentifMaghrib"
                        placeholder="0"
                    />
                </div>
            </div>

            <!-- B. POTONGAN (DEDUCTIONS) -->
            <div class="bg-rose-50/40 border border-rose-200/80 rounded-2xl p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-rose-200 pb-2">
                    <span class="text-xs font-extrabold text-rose-900 uppercase tracking-wider">B. Potongan (Deductions)</span>
                    <span class="text-xs font-black text-rose-800">Total: Rp {{ number_format($editTotalPotongan, 0, ',', '.') }}</span>
                </div>

                <div class="space-y-2.5">
                    <x-input-currency
                        label="1. Potongan Sosial Yayasan (Rp)"
                        wire:model.live.debounce.300ms="editPotonganSosial"
                        placeholder="0"
                    />

                    <x-input-currency
                        label="2. Potongan Hutang / Kasbon Pinjaman (Rp)"
                        wire:model.live.debounce.300ms="editPotonganPinjaman"
                        placeholder="0"
                    />

                    <x-input-currency
                        label="3. Potongan Iuran BPJSTK (Rp)"
                        wire:model.live.debounce.300ms="editPotonganBpjstk"
                        placeholder="0"
                    />

                    <x-input-currency
                        label="4. Potongan Lain-lain (Rp)"
                        wire:model.live.debounce.300ms="editPotonganLainnya"
                        placeholder="0"
                    />
                </div>
            </div>
        </div>

        <!-- Net Total Bar -->
        <div class="p-3.5 bg-emerald-700 text-white rounded-2xl flex items-center justify-between shadow-md">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-200 block">Total Gaji Bersih (Take Home Pay)</span>
                <span class="text-xs text-emerald-100">Formula: Total Penerimaan - Total Potongan</span>
            </div>
            <div class="text-right">
                <span class="text-xl sm:text-2xl font-black tracking-tight">Rp {{ number_format($editTotalDiterima, 0, ',', '.') }}</span>
            </div>
        </div>

        @if(auth()->user()->role?->nama === 'finance' && $editStatus === 'dibayar')
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-start gap-2">
                <x-lucide-alert-circle class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                <div>
                    <span class="font-bold">Persetujuan Diperlukan:</span> Perubahan rincian gaji yang sudah dibayar ini akan diajukan ke Super Admin atau Super Admin 2 untuk disetujui.
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Alasan Perubahan <span class="text-rose-500">*</span>
                </label>
                <textarea 
                    wire:model="edit_alasan" 
                    rows="2" 
                    placeholder="Jelaskan alasan perubahan rincian gaji..." 
                    class="w-full px-3.5 py-2.5 bg-white border @error('edit_alasan') border-rose-500 ring-1 ring-rose-500 @else border-stone-300 @enderror rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"
                ></textarea>
                @error('edit_alasan')
                    <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-200">
            <x-button variant="secondary" size="md" wire:click="closeEditModal">Batal</x-button>
            <x-button variant="primary" size="md" wire:click="saveEdit" loadingTarget="saveEdit">
                {{ (auth()->user()->role?->nama === 'finance' && $editStatus === 'dibayar') ? 'Ajukan Persetujuan' : 'Simpan Perubahan' }}
            </x-button>
        </div>
    </div>
</x-floating-card>
