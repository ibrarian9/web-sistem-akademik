<div class="space-y-6 font-sans">
    @php
        $jGuru = strtolower(auth()->user()->guru?->jenis_guru ?? 'umum');
        if ($jGuru === 'tahfidz') $jGuru = 'tahfizh';
        $isTahfizh = $jGuru === 'tahfizh';
        $isUmum = $jGuru === 'umum';
        $isKeduanya = $jGuru === 'keduanya' || auth()->user()->role?->nama !== 'guru';
    @endphp

    <!-- Quick Module Switcher Navigation -->
    <div class="flex items-center gap-2 bg-white border border-stone-200 p-2 rounded-2xl overflow-x-auto shadow-xs">
        @if($isUmum || $isKeduanya)
            <a href="{{ route('guru.kurikulum-merdeka') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-sm flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 01-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Setup Bab &amp; TP</span>
            </a>
            <a href="{{ route('guru.input-sumatif') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Nilai Sumatif</span>
            </a>
        @endif

        @if($isTahfizh || $isKeduanya)
            <a href="{{ route('guru.input-tahfidz') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Setoran Tahfizh</span>
            </a>
        @endif

        @if($isUmum || $isKeduanya)
            <a href="{{ route('guru.penilaian-p5') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                <span>Penilaian P5</span>
            </a>
        @endif

        <a href="{{ route('guru.kelola-rapor') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Lihat Rapor Murid</span>
        </a>
    </div>


    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Setup Bab & Tujuan Pembelajaran (Kurikulum Merdeka)"
        :steps="[
            ['title' => 'Pilih Mata Pelajaran', 'desc' => 'Pilih mapel yang Anda ampu dari dropdown filter di atas untuk mengelola data Bab dan TP.'],
            ['title' => 'Kelola Bab (Lingkup Materi)', 'desc' => 'Klik + Tambah Bab Baru untuk membuat lingkup materi per semester beserta frasa deskripsinya.'],
            ['title' => 'Kelola Tujuan Pembelajaran (TP)', 'desc' => 'Tambahkan TP pada setiap Bab. Urutan TP akan menentukan prioritas narasi rapor otomatis.']
        ]"
        notes="Deskripsi TP yang singkat dan jelas akan mempermudah kalkulasi Auto-Narasi Capaian Pembelajaran pada lembar Rapor Siswa."
    />

    <div class="bg-white border border-stone-200 p-6 md:p-8 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-1.5">
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
                Kurikulum Merdeka
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Setup Bab &amp; Tujuan Pembelajaran (TP)</h1>
            <p class="text-stone-600 text-xs font-medium max-w-xl leading-relaxed">
                Kelola Lingkup Materi (Bab), Tujuan Pembelajaran (TP), dan frasa deskripsi otomatis untuk pembuatan Rapor Digital Kurikulum Merdeka.
            </p>
        </div>

        <!-- Subject Filter & Add Buttons -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <div class="w-full sm:w-64 bg-stone-50 border border-stone-200 p-3 rounded-xl">
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Pilih Mata Pelajaran</label>
                <select wire:model.live="mapel_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3 py-1.5 text-xs font-bold focus:ring-2 focus:ring-emerald-500">
                    @foreach($mapels as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button wire:click="openLmModal" class="flex-1 sm:flex-none px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Tambah Bab
                </button>
                <button wire:click="openTpModal" class="flex-1 sm:flex-none px-4 py-3 bg-stone-100 hover:bg-stone-200 text-stone-800 border border-stone-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Tambah TP
                </button>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 p-4 rounded-xl text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <span class="px-2.5 py-0.5 bg-emerald-200 text-emerald-900 rounded font-black text-[10px]">Tersimpan</span>
        </div>
    @endif

    <!-- Template Frasa Auto-Narasi Capaian -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-stone-200 pb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider">Template Frasa Auto-Narasi Capaian Rapor</h2>
                    <p class="text-xs text-stone-500 font-medium">Kalimat pembuka yang akan digabung otomatis dengan TP tertinggi &amp; terendah.</p>
                </div>
            </div>
            <button type="button" wire:click="saveTemplate" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition shadow-sm">
                Simpan Template Frasa
            </button>
        </div>

        <form wire:submit.prevent="saveTemplate" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-800">Frasa Pembuka Nilai Tertinggi</label>
                <input type="text" wire:model="frasa_tertinggi" class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-stone-900 text-xs font-semibold focus:ring-2 focus:ring-emerald-500 shadow-xs">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-800">Frasa Pembuka Nilai Terendah</label>
                <input type="text" wire:model="frasa_terendah" class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-stone-900 text-xs font-semibold focus:ring-2 focus:ring-emerald-500 shadow-xs">
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
    <div class="space-y-4">
        @forelse($lingkupMateris as $lm)
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-emerald-600 text-white font-black text-xs rounded-xl shadow-xs">
                            Bab {{ $lm->urutan }}
                        </span>
                        <div>
                            <h4 class="text-sm font-extrabold text-stone-900">{{ $lm->nama_lingkup_materi }}</h4>
                            <span class="text-[11px] text-stone-500 font-semibold">Terdaftar {{ $lm->tujuanPembelajaran->count() }} Tujuan Pembelajaran (TP)</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="openTpModal({{ $lm->id }})" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold transition flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            + Tambah TP
                        </button>
                        <button wire:click="editLingkupMateri({{ $lm->id }})" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-xl text-xs font-bold transition">
                            Edit Bab
                        </button>
                        <button type="button" @click="window.showAlert('Konfirmasi Hapus Bab', 'Apakah Anda yakin ingin menghapus bab ini beserta seluruh Tujuan Pembelajaran di dalamnya?', () => $wire.deleteLingkupMateri({{ $lm->id }}))" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 rounded-xl text-xs font-bold transition">
                            Hapus
                        </button>
                    </div>
                </div>

                <!-- List of TPs inside Bab -->
                <div class="space-y-2">
                    @forelse($lm->tujuanPembelajaran as $tp)
                        <div class="flex items-start justify-between bg-stone-50 p-3.5 rounded-xl border border-stone-200 hover:border-emerald-300 transition">
                            <div class="flex items-start gap-3">
                                <span class="px-2.5 py-0.5 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs font-black rounded-lg shrink-0 mt-0.5">
                                    TP {{ $lm->urutan }}.{{ $tp->urutan }}
                                </span>
                                <p class="text-xs text-stone-800 leading-relaxed font-semibold">{{ $tp->deskripsi_tp }}</p>
                            </div>
                            <div class="flex items-center gap-2 ml-4 shrink-0">
                                <button wire:click="editTp({{ $tp->id }})" class="text-xs text-amber-700 hover:underline font-bold">Edit</button>
                                <button type="button" @click="window.showAlert('Konfirmasi Hapus TP', 'Apakah Anda yakin ingin menghapus Tujuan Pembelajaran (TP) ini?', () => $wire.deleteTp({{ $tp->id }}))" class="text-xs text-rose-700 hover:underline font-bold">Hapus</button>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 bg-stone-50 border border-dashed border-stone-200 rounded-xl text-center flex items-center justify-between">
                            <span class="text-xs text-stone-500 italic font-medium">Belum ada Tujuan Pembelajaran (TP) pada bab ini.</span>
                            <button wire:click="openTpModal({{ $lm->id }})" class="text-xs text-emerald-700 font-bold hover:underline">+ Tambah TP Pertama</button>
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="bg-white border border-stone-200 rounded-2xl p-12 text-center space-y-3 shadow-sm">
                <div class="w-12 h-12 bg-emerald-100 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-center mx-auto font-black text-lg">
                    ?
                </div>
                <div class="space-y-1">
                    <h3 class="text-sm font-bold text-stone-900 uppercase tracking-wider">Belum Ada Bab Terdaftar</h3>
                    <p class="text-xs text-stone-500 max-w-sm mx-auto">Silakan daftarkan Lingkup Materi (Bab) pertama Anda dengan mengklik tombol "+ Tambah Bab".</p>
                </div>
                <button wire:click="openLmModal" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm inline-block">
                    + Tambah Bab Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- MODAL 1: FORM LINGKUP MATERI (BAB) -->
    @if($showLmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-xs">
            <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-2xl max-w-md w-full space-y-4">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-xs flex items-center justify-center font-black">1</span>
                        {{ $editingLmId ? 'Edit Lingkup Materi (Bab)' : 'Tambah Bab Baru' }}
                    </h3>
                    <button wire:click="closeLmModal" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold">✕</button>
                </div>

                <form wire:submit.prevent="saveLingkupMateri" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Nama Lingkup Materi / Bab</label>
                        <input type="text" wire:model="nama_lingkup_materi" placeholder="misal: Bab 1 Bilangan Bulat" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-semibold focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Kategori</label>
                            <input type="text" wire:model="kategori_lm" placeholder="Opsional" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-stone-900 text-xs font-semibold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Urutan Bab</label>
                            <input type="number" wire:model="urutan_lm" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-stone-900 text-xs font-extrabold">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                        <button type="button" wire:click="closeLmModal" class="px-4 py-2 bg-stone-100 text-stone-700 rounded-xl text-xs font-bold hover:bg-stone-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm">
                            {{ $editingLmId ? 'Update Bab' : 'Simpan Bab' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 2: FORM TUJUAN PEMBELAJARAN (TP) -->
    @if($showTpModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-xs">
            <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-2xl max-w-md w-full space-y-4">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-xs flex items-center justify-center font-black">2</span>
                        {{ $editingTpId ? 'Edit Tujuan Pembelajaran (TP)' : 'Tambah Tujuan Pembelajaran (TP)' }}
                    </h3>
                    <button wire:click="closeTpModal" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold">✕</button>
                </div>

                <form wire:submit.prevent="saveTp" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Pilih Bab Target</label>
                        <select wire:model="lingkup_materi_id" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-semibold focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Pilih Bab --</option>
                            @foreach($lingkupMateris as $lm)
                                <option value="{{ $lm->id }}">Bab {{ $lm->urutan }}: {{ $lm->nama_lingkup_materi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Deskripsi Capaian TP</label>
                        <textarea wire:model="deskripsi_tp" rows="3" placeholder="misal: membaca dan menulis bilangan bulat positif dan negatif" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Urutan TP</label>
                        <input type="number" wire:model="urutan_tp" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-stone-900 text-xs font-extrabold">
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                        <button type="button" wire:click="closeTpModal" class="px-4 py-2 bg-stone-100 text-stone-700 rounded-xl text-xs font-bold hover:bg-stone-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm">
                            {{ $editingTpId ? 'Update TP' : 'Simpan TP' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
