<!-- FLOATING CARD / MODAL: KELOLA KATEGORI TAGIHAN SISWA -->
<x-floating-card 
    :show="$showKategoriModal"
    title="Kelola Kategori Tagihan Siswa"
    subtitle="Tambah kategori baru, perbarui tarif standar, atau sesuaikan frekuensi tagihan operasional santri."
    badge="KATEGORI TAGIHAN"
    badgeVariant="emerald"
    icon="tags"
    maxWidth="max-w-5xl"
    closeAction="closeKategoriModal"
>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 text-xs">
        
        <!-- KOLOM KIRI: FORM TAMBAH / EDIT KATEGORI (5 Kolom) -->
        <div class="lg:col-span-5 bg-stone-50 border border-stone-200 rounded-2xl p-5 space-y-4 shadow-2xs">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg {{ $editingKategoriId ? 'bg-amber-600' : 'bg-emerald-600' }} text-white flex items-center justify-center shadow-2xs">
                        <x-dynamic-component :component="$editingKategoriId ? 'lucide-edit-3' : 'lucide-plus-circle'" class="w-4 h-4" />
                    </div>
                    <div>
                        <h3 class="font-extrabold text-stone-900 text-xs tracking-tight">
                            {{ $editingKategoriId ? 'Edit Kategori Tagihan' : 'Tambah Kategori Baru' }}
                        </h3>
                        <p class="text-[10px] text-stone-500 font-medium">
                            {{ $editingKategoriId ? 'Perbarui informasi dan nominal kategori' : 'Kategori akan langsung tersedia di opsi rilis tagihan' }}
                        </p>
                    </div>
                </div>

                @if ($editingKategoriId)
                    <button type="button" wire:click="resetKategoriForm" class="text-[11px] font-bold text-stone-500 hover:text-stone-800 transition cursor-pointer">
                        Batal Edit
                    </button>
                @endif
            </div>

            <form wire:submit.prevent="saveKategori" action="javascript:void(0);" class="space-y-4">
                <!-- Nama Kategori Tagihan -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                        Nama Kategori Tagihan <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model="kategori_nama" 
                        placeholder="Contoh: SPP, Uang Sekolah, Uang Gedung..." 
                        class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs"
                        required
                    />
                    @error('kategori_nama') 
                        <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Tipe Frekuensi Pembayaran -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                        Frekuensi / Tipe Tagihan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 gap-2">
                        <label class="p-2.5 border rounded-xl flex items-start gap-2.5 cursor-pointer transition {{ $kategori_tipe === 'rutin' ? 'border-emerald-600 bg-emerald-50/80 text-emerald-950 ring-1 ring-emerald-500/20' : 'border-stone-200 bg-white hover:bg-stone-50 text-stone-700' }}">
                            <input type="radio" wire:model.live="kategori_tipe" value="rutin" class="mt-0.5 text-emerald-600 focus:ring-emerald-500" />
                            <div>
                                <span class="font-extrabold text-xs block">Rutin (Bulanan / SPP)</span>
                                <span class="text-[10px] text-stone-500 font-medium block">Ditagihkan setiap bulan (Juli s.d. Juni).</span>
                            </div>
                        </label>
                        
                        <label class="p-2.5 border rounded-xl flex items-start gap-2.5 cursor-pointer transition {{ $kategori_tipe === 'one_time' ? 'border-emerald-600 bg-emerald-50/80 text-emerald-950 ring-1 ring-emerald-500/20' : 'border-stone-200 bg-white hover:bg-stone-50 text-stone-700' }}">
                            <input type="radio" wire:model.live="kategori_tipe" value="one_time" class="mt-0.5 text-emerald-600 focus:ring-emerald-500" />
                            <div>
                                <span class="font-extrabold text-xs block">Sekali Bayar (Non-Rutin)</span>
                                <span class="text-[10px] text-stone-500 font-medium block">Untuk pendaftaran, uang gedung, seragam, dsb.</span>
                            </div>
                        </label>

                        <label class="p-2.5 border rounded-xl flex items-start gap-2.5 cursor-pointer transition {{ $kategori_tipe === 'tahunan' ? 'border-emerald-600 bg-emerald-50/80 text-emerald-950 ring-1 ring-emerald-500/20' : 'border-stone-200 bg-white hover:bg-stone-50 text-stone-700' }}">
                            <input type="radio" wire:model.live="kategori_tipe" value="tahunan" class="mt-0.5 text-emerald-600 focus:ring-emerald-500" />
                            <div>
                                <span class="font-extrabold text-xs block">Tahunan (Per Tahun Ajaran)</span>
                                <span class="text-[10px] text-stone-500 font-medium block">Untuk uang buku, daftar ulang tahunan, kegiatan tahunan.</span>
                            </div>
                        </label>
                    </div>
                    @error('kategori_tipe') 
                        <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Tarif Standar (Default Nominal) -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                        Tarif Standar / Nominal Default <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-bold text-stone-500 text-xs pointer-events-none">Rp</span>
                        <input 
                            type="text" 
                            wire:model="kategori_nominal" 
                            placeholder="0" 
                            class="w-full pl-10 pr-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs font-mono"
                            required
                        />
                    </div>
                    <span class="text-[10px] text-stone-500 block">
                        Nominal ini akan otomatis terisi saat memilih kategori ini di formulir rilis tagihan (dapat disesuaikan per siswa).
                    </span>
                    @error('kategori_nominal') 
                        <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Pembatasan Rapor (is_blocking) -->
                <div class="p-3 bg-white border border-stone-200 rounded-xl">
                    <label class="flex items-start gap-2.5 cursor-pointer select-none">
                        <input 
                            type="checkbox" 
                            wire:model="kategori_is_blocking" 
                            class="mt-0.5 rounded border-stone-300 text-emerald-600 focus:ring-emerald-500" 
                        />
                        <div class="space-y-0.5">
                            <span class="font-bold text-xs text-stone-800 block">Batasi Akses Rapor Jika Menunggak</span>
                            <span class="text-[10px] text-stone-500 font-normal leading-relaxed block">
                                Jika diaktifkan, siswa yang memiliki tunggakan pada kategori ini tidak dapat mengunduh rapor sebelum lunas.
                            </span>
                        </div>
                    </label>
                </div>

                <!-- Tombol Aksi Form -->
                <div class="pt-2 flex items-center gap-2">
                    <x-button type="submit" variant="primary" size="md" class="w-full justify-center">
                        <x-dynamic-component :component="$editingKategoriId ? 'lucide-check' : 'lucide-save'" class="w-4 h-4 mr-1.5" />
                        <span>{{ $editingKategoriId ? 'Simpan Perubahan' : 'Tambah Kategori' }}</span>
                    </x-button>

                    @if ($editingKategoriId)
                        <x-button type="button" variant="secondary" size="md" wire:click="resetKategoriForm">
                            Batal
                        </x-button>
                    @endif
                </div>
            </form>
        </div>

        <!-- KOLOM KANAN: DAFTAR KATEGORI TAGIHAN AKTIF (7 Kolom) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-stone-200">
                <div>
                    <h3 class="font-extrabold text-stone-900 text-sm tracking-tight">Daftar Kategori Tagihan</h3>
                    <p class="text-xs text-stone-500">Seluruh kategori pembiayaan yang terdaftar dalam sistem akademik.</p>
                </div>
                
                <!-- Search Box Kategori -->
                <div class="relative w-full sm:w-48">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="searchKategori" 
                        placeholder="Cari kategori..." 
                        class="w-full px-3 py-1.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold focus:ring-2 focus:ring-emerald-600 shadow-2xs"
                    />
                </div>
            </div>

            <!-- List Kategori Cards -->
            <div class="space-y-2.5 max-h-[480px] overflow-y-auto custom-scrollbar pr-1">
                @forelse ($this->kategoriList as $kategori)
                    @php
                        $isCurrentEdit = $editingKategoriId === $kategori->id;
                        $tipeBadge = match($kategori->kategori) {
                            'rutin' => ['label' => 'Rutin (SPP)', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-300'],
                            'one_time' => ['label' => 'Sekali Bayar', 'class' => 'bg-amber-100 text-amber-900 border-amber-300'],
                            'tahunan' => ['label' => 'Tahunan', 'class' => 'bg-indigo-100 text-indigo-900 border-indigo-300'],
                            default => ['label' => ucfirst($kategori->kategori), 'class' => 'bg-stone-100 text-stone-700 border-stone-300'],
                        };
                    @endphp
                    <div class="p-3.5 bg-white border rounded-2xl transition flex items-center justify-between gap-3 shadow-2xs {{ $isCurrentEdit ? 'border-amber-400 ring-2 ring-amber-400/20 bg-amber-50/20' : 'border-stone-200 hover:border-emerald-300' }}">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl {{ $kategori->kategori === 'rutin' ? 'bg-emerald-100 text-emerald-700' : ($kategori->kategori === 'one_time' ? 'bg-amber-100 text-amber-700' : 'bg-indigo-100 text-indigo-700') }} flex items-center justify-center shrink-0 font-black text-xs">
                                <x-lucide-file-text class="w-4 h-4" />
                            </div>
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-black text-stone-900 text-xs truncate">
                                        {{ $kategori->nama }}
                                    </h4>
                                    <span class="px-2 py-0.2 rounded-md text-[10px] font-extrabold uppercase border {{ $tipeBadge['class'] }}">
                                        {{ $tipeBadge['label'] }}
                                    </span>
                                    @if ($kategori->is_blocking)
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200" title="Menunggak tagihan ini membatasi akses rapor">
                                            Kunci Rapor
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 text-[11px] text-stone-500 font-medium">
                                    <span>Tarif Standar: <strong class="text-stone-800 font-mono font-bold">Rp {{ number_format($kategori->default_nominal, 0, ',', '.') }}</strong></span>
                                    <span>•</span>
                                    <span class="{{ $kategori->tagihans_count > 0 ? 'text-emerald-700 font-bold' : 'text-stone-400' }}">
                                        {{ $kategori->tagihans_count > 0 ? 'Digunakan di ' . $kategori->tagihans_count . ' tagihan' : 'Belum digunakan' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-1 shrink-0">
                            <button 
                                type="button" 
                                wire:click="editKategori({{ $kategori->id }})" 
                                class="p-1.5 text-stone-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition border border-transparent hover:border-emerald-200 cursor-pointer"
                                title="Edit Kategori"
                            >
                                <x-lucide-pencil class="w-3.5 h-3.5" />
                            </button>

                            <button 
                                type="button" 
                                wire:click="deleteKategori({{ $kategori->id }})" 
                                data-confirm="Apakah Anda yakin ingin menghapus kategori tagihan '{{ $kategori->nama }}' ini?"
                                class="p-1.5 text-stone-400 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition border border-transparent hover:border-rose-200 cursor-pointer"
                                title="Hapus Kategori"
                            >
                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-stone-400 border border-dashed border-stone-200 rounded-2xl space-y-1">
                        <x-lucide-tags class="w-8 h-8 mx-auto text-stone-300 mb-1" />
                        <div class="font-bold text-xs text-stone-600">Tidak ada kategori tagihan ditemukan</div>
                        <p class="text-[11px] text-stone-400">Gunakan formulir di sebelah kiri untuk menambahkan kategori baru.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Footer Modal -->
    <div class="mt-6 pt-4 border-t border-stone-200 flex items-center justify-between text-xs">
        <span class="text-stone-500 font-medium">
            Kategori tagihan yang sudah tersimpan otomatis muncul pada menu filter pencarian dan opsi rilis tagihan siswa.
        </span>
        <x-button type="button" variant="secondary" size="sm" wire:click="closeKategoriModal">
            Tutup
        </x-button>
    </div>
</x-floating-card>
