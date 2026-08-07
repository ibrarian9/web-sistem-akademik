<div class="space-y-6 font-sans">
    @php
        $jGuru = strtolower(auth()->user()->guru?->jenis_guru ?? 'umum');
        if ($jGuru === 'tahfidz') $jGuru = 'tahfizh';
        $isTahfizh = $jGuru === 'tahfizh';
        $isUmum = $jGuru === 'umum';
        $isKeduanya = $jGuru === 'keduanya' || auth()->user()->role?->nama !== 'guru';
    @endphp

    <!-- Quick Module Switcher Header (Light Theme) -->
    <div class="flex items-center gap-2 bg-white border border-stone-200 p-2 rounded-2xl overflow-x-auto shadow-xs">
        @if($isUmum || $isKeduanya)
            <a href="{{ route('guru.kurikulum-merdeka') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 01-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Setup Bab &amp; TP</span>
            </a>
            <a href="{{ route('guru.input-sumatif') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Nilai Sumatif</span>
            </a>
        @endif
        @if($isTahfizh || $isKeduanya)
            <a href="{{ route('guru.input-tahfidz') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-700 text-white shadow-sm flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Setoran Tahfizh</span>
            </a>
        @endif
        @if($isUmum || $isKeduanya)
            <a href="{{ route('guru.penilaian-p5') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                <span>Penilaian P5</span>
            </a>
        @endif
        <a href="{{ route('guru.kelola-rapor') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Lihat Rapor Murid</span>
        </a>
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Mutaba'ah Guru Tahfizh SD TAHFIZH F3"
        :steps="[
            ['title' => 'Pilih Halaqah Tahfizh', 'desc' => 'Tentukan Halaqah / Kelas Tahfizh bimbingan Anda dan Semester aktif.'],
            ['title' => 'Klik Baris Santri / Tombol Aksi', 'desc' => 'Klik pada baris santri atau tombol + Isi Mutaba\'ah untuk memasukkan materi Tahsin, Muraja\'ah, Kitabah, & Ziyadah.'],
            ['title' => 'Otomatis Tersimpan Ke Rapor', 'desc' => 'Hasil nilai setoran langsung terakumulasi pada lembar Rapor Tahfizh Al-Qur\'an santri.']
        ]"
        notes="Format tabel mutaba'ah disesuaikan 100% dengan lembar fisik Mutaba'ah Guru Tahfizh SD TAHFIZH F3."
    />

    <!-- Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm space-y-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                    MUTABA'AH GURU TAHFIZH SD TAHFIZH F3
                </span>
                <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Lembar Mutaba'ah &amp; Setoran Hafalan Santri</h1>
                <p class="text-stone-600 text-xs font-semibold mt-1">Pencatatan Tahsin, Muraja'ah (Bersama &amp; Mandiri), Kitabah, dan Ziyadah hafalan Al-Qur'an.</p>
            </div>
            <button type="button" wire:click.prevent="openScoreModal" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-5 py-3 rounded-xl text-xs flex items-center gap-2 transition shadow-md self-start md:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Input Mutaba'ah Santri
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Halaqah / Kelas Tahfizh (Diatur Admin TU)</label>
                <select wire:model.live="kelas_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-4 py-2.5 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                    @foreach($kelases as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} (Pengampu: {{ $k->guruTahfidz->user->nama ?? 'Admin TU' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Semester &amp; Tahun Ajaran</label>
                <select wire:model.live="semester_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-4 py-2.5 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->tahunAjaran->nama ?? '' }} - {{ ucfirst($sem->semester) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-xl text-xs font-bold flex items-center justify-between shadow-xs">
            <span>{{ session('message') }}</span>
            <span class="px-2.5 py-0.5 bg-emerald-200 text-emerald-900 rounded font-black text-[10px]">Tersimpan</span>
        </div>
    @endif

    <!-- User-Friendly Table Container -->
    <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm space-y-4">
        <!-- Top Toolbar with Live Search & Progress Indicators -->
        <div class="p-4 bg-emerald-800 border-b border-emerald-900 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-xs font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                    MATRIKS MUTABA'AH GURU TAHFIZH
                </span>
                <span class="px-2.5 py-0.5 bg-emerald-900 text-emerald-100 rounded-full text-[11px] font-bold">
                    {{ $scores->count() }}/{{ count($siswas) }} Santri Terisi
                </span>
            </div>

            <!-- Quick Live Search Input -->
            <div class="relative min-w-[240px]">
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Cari santri / NISN..." 
                    class="w-full pl-9 pr-4 py-2 bg-emerald-900/90 border border-emerald-600 rounded-xl text-white placeholder-emerald-300 text-xs font-medium focus:ring-2 focus:ring-amber-400 focus:bg-emerald-900 shadow-inner"
                />
                <svg class="w-4 h-4 text-emerald-300 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <div class="overflow-x-auto relative">
            <table class="w-full text-left border-collapse text-xs text-stone-800">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <th rowspan="2" class="p-3 text-center border-r border-emerald-700 w-10 sticky left-0 bg-emerald-800 z-20">NO</th>
                        <th rowspan="2" class="p-3 border-r border-emerald-700 min-w-[200px] sticky left-10 bg-emerald-800 z-20">NISN &amp; NAMA SANTRI</th>
                        <th rowspan="2" class="p-3 text-center border-r border-emerald-700 w-12">L/P</th>
                        <th colspan="2" class="p-2 text-center border-r border-emerald-700 bg-emerald-900/60">TAHSIN</th>
                        <th colspan="3" class="p-2 text-center border-r border-emerald-700 bg-emerald-900/80">MURAJA'AH</th>
                        <th colspan="2" class="p-2 text-center border-r border-emerald-700 bg-emerald-900/60">KITABAH</th>
                        <th colspan="2" class="p-2 text-center border-r border-emerald-700 bg-emerald-900/80">ZIYADAH</th>
                        <th rowspan="2" class="p-3 text-center border-r border-emerald-700 min-w-[200px] bg-emerald-900/60 font-extrabold">TANGGAPAN ORANG TUA / WALI</th>
                        <th rowspan="2" class="p-3 text-center min-w-[130px]">AKSI</th>
                    </tr>

                    <tr>
                        <!-- Tahsin subheaders -->
                        <th class="p-2 text-center border-r border-emerald-700 min-w-[120px] font-bold">Materi/Ayat</th>
                        <th class="p-2 text-center border-r border-emerald-700 w-16 bg-emerald-900 font-extrabold">Nilai</th>
                        
                        <!-- Muraja'ah subheaders -->
                        <th class="p-2 text-center border-r border-emerald-700 min-w-[90px] font-bold">Bersama</th>
                        <th class="p-2 text-center border-r border-emerald-700 min-w-[120px] font-bold">Mandiri</th>
                        <th class="p-2 text-center border-r border-emerald-700 w-16 bg-emerald-900 font-extrabold">Nilai</th>

                        <!-- Kitabah subheaders -->
                        <th class="p-2 text-center border-r border-emerald-700 min-w-[120px] font-bold">Materi</th>
                        <th class="p-2 text-center border-r border-emerald-700 w-16 bg-emerald-900 font-extrabold">Nilai</th>

                        <!-- Ziyadah subheaders -->
                        <th class="p-2 text-center border-r border-emerald-700 min-w-[120px] font-bold">Materi</th>
                        <th class="p-2 text-center border-r border-emerald-700 w-16 bg-emerald-900 font-extrabold">Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
                    @forelse($siswas as $index => $s)
                        @php
                            $rec = $scores->get($s->id);
                            $gender = strtolower($s->jenis_kelamin ?? 'L') === 'p' ? 'P' : 'L';
                            $isFilled = $rec !== null;
                        @endphp
                        <!-- Row Clickable for User-Friendly Interaction -->
                        <tr 
                            wire:click="openScoreModal({{ $s->id }})" 
                            class="hover:bg-emerald-50/70 cursor-pointer transition group"
                            title="Klik untuk mengedit/mengisi data mutaba'ah {{ $s->user->nama ?? $s->nama_panggilan }}"
                        >
                            <!-- No (Sticky Left) -->
                            <td class="p-3 text-center font-bold text-stone-500 border-r border-stone-200 text-xs sticky left-0 bg-white group-hover:bg-emerald-50/90 z-10">
                                {{ $index + 1 }}
                            </td>

                            <!-- NISN & Nama Santri (Sticky Left with Status Pill) -->
                            <td class="p-3 border-r border-stone-200 sticky left-10 bg-white group-hover:bg-emerald-50/90 z-10">
                                <div class="font-extrabold text-stone-900 text-xs">
                                    {{ strtoupper($s->user->nama ?? $s->nama_panggilan) }}
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] text-stone-500 font-medium">NISN: {{ $s->nisn }}</span>
                                    @if($isFilled)
                                        <span class="px-1.5 py-0.5 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded text-[9px] font-bold inline-flex items-center gap-1">
                                            <span>✓ Terisi</span>
                                        </span>
                                    @else
                                        <span class="px-1.5 py-0.5 bg-amber-100 border border-amber-300 text-amber-900 rounded text-[9px] font-bold inline-flex items-center gap-1">
                                            <span>+ Belum</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="p-3 text-center font-bold text-stone-600 border-r border-stone-200">{{ $gender }}</td>
                            
                            <!-- Tahsin -->
                            <td class="p-2 border-r border-stone-200 text-stone-700 font-medium text-center">
                                {{ $rec?->materi_tahsin ?? '-' }}
                            </td>
                            <td class="p-2 text-center border-r border-stone-200 bg-emerald-50/40 font-black text-emerald-950">
                                {{ ($rec && $rec->nilai_tahsin !== null) ? round($rec->nilai_tahsin) : '-' }}
                            </td>

                            <!-- Muraja'ah -->
                            <td class="p-2 border-r border-stone-200 text-stone-700 font-medium text-center">
                                {{ $rec?->murajaah_bersama ?? '-' }}
                            </td>
                            <td class="p-2 border-r border-stone-200 text-stone-700 font-medium text-center">
                                {{ $rec?->murajaah_mandiri ?? '-' }}
                            </td>
                            <td class="p-2 text-center border-r border-stone-200 bg-emerald-50/40 font-black text-emerald-950">
                                {{ ($rec && $rec->nilai_murajaah !== null) ? round($rec->nilai_murajaah) : '-' }}
                            </td>

                            <!-- Kitabah -->
                            <td class="p-2 border-r border-stone-200 text-stone-700 font-medium text-center">
                                {{ $rec?->materi_kitabah ?? '-' }}
                            </td>
                            <td class="p-2 text-center border-r border-stone-200 bg-emerald-50/40 font-black text-emerald-950">
                                {{ ($rec && $rec->nilai_kitabah !== null) ? round($rec->nilai_kitabah) : '-' }}
                            </td>

                            <!-- Ziyadah -->
                            <td class="p-2 border-r border-stone-200 text-stone-700 font-medium text-center">
                                {{ $rec?->materi_ziyadah ?? '-' }}
                            </td>
                            <td class="p-2 text-center border-r border-stone-200 bg-emerald-50/40 font-black text-emerald-950">
                                {{ ($rec && $rec->nilai_ziyadah !== null) ? round($rec->nilai_ziyadah) : '-' }}
                            </td>

                            <!-- Tanggapan Orang Tua -->
                            <td class="p-2 border-r border-stone-200 text-stone-700 text-[11px]">
                                @if($rec && $rec->tanggapan_orang_tua)
                                    <div class="bg-emerald-50 p-2 rounded-lg border border-emerald-200 text-emerald-950 shadow-xs">
                                        <div class="font-medium italic text-[11px]">"{{ $rec->tanggapan_orang_tua }}"</div>
                                        <div class="text-[9px] text-emerald-800 font-bold mt-1">Oleh: {{ $rec->dikirim_oleh_nama ?: 'Orang Tua' }}</div>
                                    </div>
                                @else
                                    <span class="text-stone-400 italic block text-center">-</span>
                                @endif
                            </td>

                            <!-- AKSI / Edit & Delete -->
                            <td class="p-2 text-center" @click.stop>
                                @if($rec)
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" wire:click.prevent="editScore({{ $rec->id }})" class="px-2.5 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 rounded-lg font-bold text-[11px] flex items-center gap-1 transition shadow-xs" title="Edit Mutaba'ah">
                                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button" wire:click.prevent="deleteScore({{ $rec->id }})" data-confirm="Apakah Anda yakin ingin menghapus data mutaba'ah santri ini?" class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg font-bold text-[11px] flex items-center gap-1 transition shadow-xs" title="Hapus Mutaba'ah">
                                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                @else
                                    <button type="button" wire:click.prevent="openScoreModal({{ $s->id }})" class="px-3 py-1 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-bold text-[11px] shadow-xs transition">
                                        + Isi Mutaba'ah
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="p-8 text-center text-stone-500 italic font-medium">
                                Tidak ada santri ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM MUTABA'AH GURU TAHFIZH -->
    @if($showScoreModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-xs">
            <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-2xl max-w-2xl w-full space-y-4">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">★</span>
                        {{ $editingId ? 'Edit Mutaba\'ah Santri' : '+ Input Mutaba\'ah Santri (SD TAHFIZH F3)' }}
                    </h3>
                    <button type="button" wire:click.prevent="closeScoreModal" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold">✕</button>
                </div>

                <form wire:submit.prevent="saveScore" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Pilih Santri Target</label>
                        <select wire:model="siswa_id" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                            <option value="">-- Pilih Santri --</option>
                            @foreach($siswas as $s)
                                <option value="{{ $s->id }}">{{ strtoupper($s->user->nama ?? $s->nama_panggilan) }} (NISN: {{ $s->nisn }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Grid Mutaba'ah 4 Kategori -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                        <!-- TAHSIN -->
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                            <span class="text-xs font-black text-emerald-950 uppercase block">1. Tahsin</span>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block">Materi / Surah (Ayat)</label>
                                <input type="text" wire:model="materi_tahsin" placeholder="contoh: Al-Baqarah 4-5 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-semibold">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block">Nilai Tahsin (0-100)</label>
                                <input type="number" step="1" wire:model="nilai_tahsin" placeholder="0-100 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-bold text-emerald-900">
                            </div>
                        </div>

                        <!-- MURAJA'AH -->
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                            <span class="text-xs font-black text-emerald-950 uppercase block">2. Muraja'ah</span>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[10px] font-bold text-stone-600 block">Bersama</label>
                                    <input type="text" wire:model="murajaah_bersama" placeholder="Juz 30 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2 py-1.5 text-xs font-semibold">
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-stone-600 block">Mandiri</label>
                                    <input type="text" wire:model="murajaah_mandiri" placeholder="Al-Baqarah 1-30 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2 py-1.5 text-xs font-semibold">
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block">Nilai Muraja'ah (0-100)</label>
                                <input type="number" step="1" wire:model="nilai_murajaah" placeholder="0-100 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-bold text-emerald-900">
                            </div>
                        </div>

                        <!-- KITABAH -->
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                            <span class="text-xs font-black text-emerald-950 uppercase block">3. Tahfizh - Kitabah</span>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block">Materi Kitabah</label>
                                <input type="text" wire:model="materi_kitabah" placeholder="contoh: Al-Baqarah 39-40 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-semibold">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block">Nilai Kitabah (0-100)</label>
                                <input type="number" step="1" wire:model="nilai_kitabah" placeholder="0-100 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-bold text-emerald-900">
                            </div>
                        </div>

                        <!-- ZIYADAH -->
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                            <span class="text-xs font-black text-emerald-950 uppercase block">4. Tahfizh - Ziyadah</span>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block">Materi Ziyadah Baru</label>
                                <input type="text" wire:model="materi_ziyadah" placeholder="contoh: Al-Baqarah 11-20 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-semibold">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block">Nilai Ziyadah (0-100)</label>
                                <input type="number" step="1" wire:model="nilai_ziyadah" placeholder="0-100 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-bold text-emerald-900">
                            </div>
                        </div>

                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Catatan Ustadz Pembimbing</label>
                        <textarea wire:model="catatan_ustadz" rows="2" placeholder="Tuliskan catatan perkembangan hafalan dan motivasi santri..." class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-medium resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                        <button type="button" wire:click.prevent="closeScoreModal" class="px-4 py-2.5 bg-stone-100 text-stone-700 rounded-xl text-xs font-bold hover:bg-stone-200">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md">Simpan Mutaba'ah</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
