<div class="space-y-6 font-sans pb-16">
    <!-- Quick Module Switcher Navigation -->
    <x-guru-module-switcher active="bab-tp" />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Setup Bab & Tujuan Pembelajaran (Kurikulum Merdeka)"
        :steps="[
            ['title' => 'Pilih Mata Pelajaran', 'desc' => 'Pilih mapel yang Anda ampu dari dropdown filter di atas untuk mengelola data Bab dan TP.'],
            ['title' => 'Kelola Bab (Lingkup Materi)', 'desc' => 'Klik Tambah Bab Baru untuk membuat lingkup materi per semester beserta frasa deskripsinya.'],
            ['title' => 'Kelola Tujuan Pembelajaran (TP)', 'desc' => 'Tambahkan TP pada setiap Bab. Urutan TP akan menentukan prioritas narasi rapor otomatis.']
        ]"
        notes="Deskripsi TP yang singkat dan jelas akan mempermudah kalkulasi Auto-Narasi Capaian Pembelajaran pada lembar Rapor Siswa."
    />

    <!-- Header Section with Responsive Filter & Add Actions -->
    <x-page-header 
        title="Setup Bab & Tujuan Pembelajaran (TP)" 
        subtitle="Kelola Lingkup Materi (Bab), Tujuan Pembelajaran (TP), dan frasa deskripsi otomatis untuk pembuatan Rapor Digital Kurikulum Merdeka."
        badge="KURIKULUM MERDEKA"
        badgeVariant="emerald"
        icon="layers"
    >
        <x-slot:actions>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                <div class="w-full sm:w-64 bg-stone-50 border border-stone-200 p-2.5 rounded-xl">
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Pilih Mata Pelajaran</label>
                    <select wire:model.live="mapel_id" class="w-full bg-white border border-stone-300 rounded-lg text-stone-900 px-3 py-1.5 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <x-button variant="primary" size="md" icon="plus" wire:click="openLmModal" class="flex-1 sm:flex-none">
                        Tambah Bab
                    </x-button>
                    <x-button variant="secondary" size="md" icon="plus" wire:click="openTpModal" class="flex-1 sm:flex-none">
                        Tambah TP
                    </x-button>
                </div>
            </div>
        </x-slot:actions>
    </x-page-header>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 p-4 rounded-2xl text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <span class="px-2.5 py-0.5 bg-emerald-200 text-emerald-900 rounded-lg font-black text-[10px]">Tersimpan</span>
        </div>
    @endif

    <!-- Template Frasa Auto-Narasi Capaian -->
    <div class="bg-white border border-stone-200 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
        <form wire:submit.prevent="saveTemplate" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-stone-200">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-xs sm:text-sm font-extrabold text-stone-900 uppercase tracking-wider">Template Frasa Auto-Narasi Capaian Rapor</h2>
                        <p class="text-[11px] text-stone-500 font-medium">Kalimat pembuka yang digabung otomatis dengan TP tertinggi & terendah.</p>
                    </div>
                </div>
                <x-button type="submit" variant="primary" size="sm" icon="check" loadingTarget="saveTemplate" class="w-full sm:w-auto">
                    Simpan Template Frasa
                </x-button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-800">Frasa Pembuka Nilai Tertinggi</label>
                    <input type="text" wire:model="frasa_tertinggi" class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2 text-stone-900 text-xs font-semibold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-800">Frasa Pembuka Nilai Terendah</label>
                    <input type="text" wire:model="frasa_terendah" class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2 text-stone-900 text-xs font-semibold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                </div>
            </div>
        </form>

        <!-- Live Preview -->
        <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
            <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Simulasi Tampilan Pada Rapor Digital:</span>
            <p class="text-xs text-stone-800 italic leading-relaxed font-medium">
                "Ananda Siswa <span class="text-emerald-700 font-bold underline">{{ $frasa_tertinggi ?: 'menunjukkan penguasaan dalam' }}</span> [Deskripsi TP Nilai Tertinggi], namun <span class="text-amber-700 font-bold underline">{{ $frasa_terendah ?: 'membutuhkan penguatan dalam' }}</span> [Deskripsi TP Nilai Terendah]."
            </p>
        </div>
    </div>

    <!-- Registered Bab & TP Full-Width Card List -->
    <div class="space-y-5">
        @forelse($lingkupMateris as $lm)
            <div class="bg-white border border-stone-200 rounded-2xl p-4 sm:p-6 shadow-xs space-y-4">
                <!-- Header Bab: Responsif (Mobile: 2 baris atas-bawah | Desktop: 1 baris kiri-kanan rapi) -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-3.5 border-b border-stone-200">
                    <!-- Bagian 1: Materi & Identitas Bab (Kiri pada desktop, Atas pada mobile) -->
                    <div class="flex items-start sm:items-center gap-3 flex-1 min-w-0">
                        <div class="px-3 py-1.5 bg-emerald-600 text-white font-black text-xs rounded-xl shadow-2xs shrink-0 mt-0.5 sm:mt-0">
                            Bab {{ $lm->urutan }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-sm sm:text-base font-extrabold text-stone-900 leading-snug">
                                Bab {{ $lm->urutan }}: {{ $lm->nama_lingkup_materi }}
                            </h3>
                            <div class="flex items-center gap-2 mt-0.5 text-[11px] text-stone-500 font-semibold flex-wrap">
                                <span class="inline-flex items-center gap-1 text-emerald-700 font-bold">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Terdaftar {{ $lm->tujuanPembelajaran->count() }} Tujuan Pembelajaran (TP)
                                </span>
                                @if($lm->kategori)
                                    <span class="text-stone-300">•</span>
                                    <span class="bg-stone-100 text-stone-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase">{{ $lm->kategori }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Bagian 2: Tombol Aksi Bab (Kanan pada desktop, Bawah pada mobile) -->
                    <div class="flex items-center gap-2 pt-2 md:pt-0 border-t border-stone-100 md:border-t-0 w-full md:w-auto shrink-0">
                        <button 
                            type="button"
                            wire:click="openTpModal({{ $lm->id }})" 
                            class="flex-1 md:flex-none px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 active:bg-emerald-200 text-emerald-800 border border-emerald-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs whitespace-nowrap cursor-pointer"
                            title="Tambah Tujuan Pembelajaran pada Bab ini"
                        >
                            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah TP</span>
                        </button>
                        <button 
                            type="button"
                            wire:click="editLingkupMateri({{ $lm->id }})" 
                            class="flex-1 md:flex-none px-3.5 py-2 bg-amber-50 hover:bg-amber-100 active:bg-amber-200 text-amber-800 border border-amber-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 shadow-2xs whitespace-nowrap cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit Bab</span>
                        </button>
                        <button 
                            type="button" 
                            @click="window.showAlert('Konfirmasi Hapus Bab', 'Apakah Anda yakin ingin menghapus bab ini beserta seluruh Tujuan Pembelajaran di dalamnya?', () => $wire.deleteLingkupMateri({{ $lm->id }}))" 
                            class="flex-1 md:flex-none px-3.5 py-2 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 text-rose-800 border border-rose-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 shadow-2xs whitespace-nowrap cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>

                <!-- List of TPs inside Bab: Bagi 2 (Materi diatas, Tombol aksi dibawah pada mobile) -->
                <div class="space-y-2.5">
                    @forelse($lm->tujuanPembelajaran as $tp)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 bg-stone-50 hover:bg-emerald-50/40 p-3.5 rounded-xl border border-stone-200 hover:border-emerald-300 transition duration-150 shadow-2xs">
                            <!-- Materi / Deskripsi TP (ATAS) -->
                            <div class="flex items-start gap-2.5 min-w-0 flex-1">
                                <span class="px-2.5 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs font-black rounded-lg shrink-0 mt-0.5">
                                    TP {{ $lm->urutan }}.{{ $tp->urutan }}
                                </span>
                                <p class="text-xs text-stone-800 leading-relaxed font-semibold break-words flex-1">{{ $tp->deskripsi_tp }}</p>
                            </div>

                            <!-- Tombol Aksi TP (BAWAH pada mobile, KANAN pada desktop) -->
                            <div class="flex items-center justify-end gap-2 shrink-0 pt-2 sm:pt-0 border-t border-stone-200/70 sm:border-t-0">
                                <button 
                                    type="button"
                                    wire:click="editTp({{ $tp->id }})" 
                                    class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-lg text-xs font-bold transition shadow-2xs flex items-center gap-1.5 whitespace-nowrap cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Edit</span>
                                </button>
                                <button 
                                    type="button" 
                                    @click="window.showAlert('Konfirmasi Hapus TP', 'Apakah Anda yakin ingin menghapus Tujuan Pembelajaran (TP) ini?', () => $wire.deleteTp({{ $tp->id }}))" 
                                    class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 rounded-lg text-xs font-bold transition shadow-2xs flex items-center gap-1.5 whitespace-nowrap cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 bg-stone-50 border border-dashed border-stone-200 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                            <span class="text-xs text-stone-500 italic font-medium">Belum ada Tujuan Pembelajaran (TP) pada bab ini.</span>
                            <button 
                                type="button" 
                                wire:click="openTpModal({{ $lm->id }})" 
                                class="text-xs text-emerald-700 font-bold hover:underline inline-flex items-center gap-1 shrink-0"
                            >
                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambah TP Pertama</span>
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="bg-white border border-stone-200 rounded-2xl p-10 sm:p-12 text-center space-y-4 shadow-xs">
                <div class="w-14 h-14 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-center mx-auto shadow-2xs">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider">Belum Ada Bab Terdaftar</h3>
                    <p class="text-xs text-stone-500 max-w-sm mx-auto font-medium">Silakan daftarkan Lingkup Materi (Bab) pertama Anda dengan mengklik tombol "Tambah Bab".</p>
                </div>
                <x-button variant="primary" size="md" icon="plus" wire:click="openLmModal" class="inline-flex">
                    Tambah Bab Pertama
                </x-button>
            </div>
        @endforelse
    </div>

    <!-- MODAL 1: FORM LINGKUP MATERI (BAB) -->
    <x-floating-card 
        :show="$showLmModal"
        :title="$editingLmId ? 'Edit Lingkup Materi (Bab)' : 'Tambah Bab Baru'"
        subtitle="Definisikan nama bab dan urutan materi pokok pembelajaran."
        badge="KURIKULUM"
        badgeVariant="emerald"
        icon="book"
        maxWidth="max-w-md"
        closeAction="closeLmModal"
    >
        <form wire:submit.prevent="saveLingkupMateri" class="space-y-3 text-xs">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Nama Lingkup Materi / Bab</label>
                <input type="text" wire:model="nama_lingkup_materi" placeholder="misal: Bab 1 Bilangan Bulat" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-2xs">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Kategori</label>
                    <input type="text" wire:model="kategori_lm" placeholder="Opsional" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-semibold focus:bg-white shadow-2xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Urutan Bab</label>
                    <input type="number" wire:model="urutan_lm" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3 py-2 text-stone-900 text-xs font-black focus:bg-white shadow-2xs">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button type="button" variant="secondary" size="md" wire:click="closeLmModal">Batal</x-button>
                <x-button type="submit" variant="primary" size="md" icon="check" loadingTarget="saveLingkupMateri">
                    {{ $editingLmId ? 'Update Bab' : 'Simpan Bab' }}
                </x-button>
            </div>
        </form>
    </x-floating-card>

    <!-- MODAL 2: FORM TUJUAN PEMBELAJARAN (TP) -->
    <x-floating-card 
        :show="$showTpModal"
        :title="$editingTpId ? 'Edit Tujuan Pembelajaran (TP)' : 'Tambah Tujuan Pembelajaran (TP)'"
        subtitle="Rincikan indikator ketercapaian tujuan pembelajaran pada bab terpilih."
        badge="TUJUAN TP"
        badgeVariant="emerald"
        icon="target"
        maxWidth="max-w-md"
        closeAction="closeTpModal"
    >
        <form wire:submit.prevent="saveTp" class="space-y-3 text-xs">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Pilih Bab Target</label>
                <select wire:model="lingkup_materi_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-2xs">
                    <option value="">-- Pilih Bab --</option>
                    @foreach($lingkupMateris as $lm)
                        <option value="{{ $lm->id }}">Bab {{ $lm->urutan }}: {{ $lm->nama_lingkup_materi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Deskripsi Capaian TP</label>
                <textarea wire:model="deskripsi_tp" rows="3" placeholder="misal: membaca dan menulis bilangan bulat positif dan negatif" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-2xs resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Urutan TP</label>
                <input type="number" wire:model="urutan_tp" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3 py-2 text-stone-900 text-xs font-black focus:bg-white shadow-2xs">
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button type="button" variant="secondary" size="md" wire:click="closeTpModal">Batal</x-button>
                <x-button type="submit" variant="primary" size="md" icon="check" loadingTarget="saveTp">
                    {{ $editingTpId ? 'Update TP' : 'Simpan TP' }}
                </x-button>
            </div>
        </form>
    </x-floating-card>

</div>
