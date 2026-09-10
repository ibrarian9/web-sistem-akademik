{{-- 2. Buat Gaji Manual Modal (Satuan) dengan Format Titik Pemisah & Tanpa Panah --}}
<x-floating-card 
    :show="$showCreateModal" 
    title="Buat Gaji Pegawai Manual" 
    subtitle="Input honorarium atau gaji baru untuk seorang guru/pegawai secara spesifik."
    badge="INPUT GAJI BARU"
    badgeVariant="emerald"
    icon="user-plus"
    maxWidth="max-w-4xl"
    closeAction="closeCreateModal"
>
    <div class="space-y-4 font-sans">
        <!-- Header Pegawai & Periode -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-emerald-50/50 p-3.5 rounded-2xl border border-emerald-200/80">
            <div class="sm:col-span-2">
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Pilih Pegawai / Guru *</label>
                <select wire:model.live="createGuruId" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                    <option value="">-- Pilih Guru --</option>
                    @foreach ($activeGurusList as $g)
                        <option value="{{ $g->id }}">{{ $g->user->nama ?? '-' }} ({{ $g->jabatan ?: ($g->jenis_guru === 'tahfidz' ? 'Tahfizh' : 'Guru') }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Bulan</label>
                <select wire:model="createBulan" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                    @foreach ($listBulan as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Tahun</label>
                <input type="number" wire:model="createTahun" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-stone-50 p-3.5 rounded-2xl border border-stone-200">
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Jabatan</label>
                <input type="text" wire:model="createJabatan" placeholder="Mudir F3 / Wali Tahfizh" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Jam Kerja</label>
                <input type="text" wire:model="createJamKerja" placeholder="07.00-14.00" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Dibayar Oleh</label>
                <input type="text" wire:model="createSumberDana" placeholder="Yayasan" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Status Awal</label>
                <select wire:model.live="createStatus" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                    <option value="draft">Draf (Belum Bayar)</option>
                    <option value="dibayar">Langsung Dibayar</option>
                </select>
            </div>
        </div>

        @if ($createStatus === 'dibayar')
            <div class="p-4 bg-emerald-50/60 border border-emerald-200 rounded-2xl space-y-2.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-lucide-camera class="w-4 h-4 text-emerald-700" />
                        <span class="text-xs font-black text-emerald-900 uppercase tracking-wider">Foto Bukti Transfer / Struk (Opsional)</span>
                    </div>
                    <span class="text-[10px] text-stone-500 font-semibold">Maks 2MB (JPG/PNG/WEBP)</span>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <input type="file" wire:model="createBuktiFoto" accept="image/jpeg,image/png,image/jpg,image/webp" 
                           class="w-full text-xs text-stone-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer border border-stone-300 rounded-xl bg-white p-1" />
                    @if ($createBuktiFoto)
                        <div class="flex items-center gap-2 shrink-0 bg-white p-1.5 rounded-xl border border-emerald-300">
                            <img src="{{ $createBuktiFoto->temporaryUrl() }}" alt="Pratinjau" class="w-12 h-12 rounded-lg object-cover" />
                            <button type="button" wire:click="$set('createBuktiFoto', null)" class="text-xs text-rose-600 font-bold hover:underline px-1 cursor-pointer">Hapus</button>
                        </div>
                    @endif
                </div>
                <div wire:loading wire:target="createBuktiFoto" class="text-xs text-emerald-600 font-semibold">
                    Mengunggah foto bukti...
                </div>
                @error('createBuktiFoto') <span class="text-xs text-rose-600 font-medium block">{{ $message }}</span> @enderror
            </div>
        @endif

        <!-- Two-Column Breakdown: Penerimaan vs Potongan -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- A. PENERIMAAN (EARNINGS) -->
            <div class="bg-emerald-50/40 border border-emerald-200/80 rounded-2xl p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-emerald-200 pb-2">
                    <span class="text-xs font-extrabold text-emerald-900 uppercase tracking-wider">A. Penerimaan (Earnings)</span>
                    <span class="text-xs font-black text-emerald-800">Total: Rp {{ number_format($createTotalBruto, 0, ',', '.') }}</span>
                </div>

                <div class="space-y-2.5">
                    <x-input-currency
                        label="1. Gaji Pokok (Rp)"
                        wire:model.live.debounce.300ms="createGajiPokok"
                        placeholder="0"
                    />

                    <x-input-currency
                        label="2. Gaji Berkala (Rp)"
                        wire:model.live.debounce.300ms="createGajiBerkala"
                        placeholder="0"
                    />

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">3a. Pertemuan Ekskul</label>
                            <input type="number" min="0" wire:model.live.debounce.300ms="createJumlahEkskul" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center shadow-2xs" placeholder="0" />
                        </div>
                        <x-input-currency
                            label="3b. Honor Ekskul (Rp)"
                            wire:model.live.debounce.300ms="createHonorEkskul"
                            placeholder="0"
                        />
                    </div>

                    <x-input-currency
                        label="4. Incentive / Insentif Jabatan (Rp)"
                        wire:model.live.debounce.300ms="createInsentif"
                        placeholder="0"
                    />

                    <x-input-currency
                        label="5. Tunjangan BPJSTK (Rp)"
                        wire:model.live.debounce.300ms="createInsentifBpjs"
                        placeholder="0"
                    />

                    <x-input-currency
                        label="6. Insentif Maghrib Mengaji (Rp)"
                        wire:model.live.debounce.300ms="createInsentifMaghrib"
                        placeholder="0"
                    />
                </div>
            </div>

            <!-- B. POTONGAN (DEDUCTIONS) -->
            <div class="bg-rose-50/40 border border-rose-200/80 rounded-2xl p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-rose-200 pb-2">
                    <span class="text-xs font-extrabold text-rose-900 uppercase tracking-wider">B. Potongan (Deductions)</span>
                    <span class="text-xs font-black text-rose-800">Total: Rp {{ number_format($createTotalPotongan, 0, ',', '.') }}</span>
                </div>

                <div class="space-y-2.5">
                    <x-input-currency
                        label="1. Potongan Sosial Yayasan (Rp)"
                        wire:model.live.debounce.300ms="createPotonganSosial"
                        placeholder="0"
                    />

                    <x-input-currency
                        label="2. Potongan Hutang / Kasbon Pinjaman (Rp)"
                        wire:model.live.debounce.300ms="createPotonganPinjaman"
                        placeholder="0"
                    />

                    <x-input-currency
                        label="3. Potongan Iuran BPJSTK (Rp)"
                        wire:model.live.debounce.300ms="createPotonganBpjstk"
                        placeholder="0"
                    />

                    <x-input-currency
                        label="4. Potongan Lain-lain (Rp)"
                        wire:model.live.debounce.300ms="createPotonganLainnya"
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
                <span class="text-xl sm:text-2xl font-black tracking-tight">Rp {{ number_format($createTotalDiterima, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-200">
            <x-button variant="secondary" size="md" wire:click="closeCreateModal">Batal</x-button>
            <x-button variant="primary" size="md" wire:click="saveCreate" loadingTarget="saveCreate">
                Simpan Gaji Pegawai
            </x-button>
        </div>
    </div>
</x-floating-card>
