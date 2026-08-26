<div class="space-y-6 font-sans">
    <!-- Quick Module Switcher Navigation -->
    <x-guru-module-switcher active="tahfidz" />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengisian Mutaba'ah Harian Guru Tahfizh SD TAHFIZH F3"
        :steps="[
            ['title' => 'Pilih Tanggal Sekolah', 'desc' => 'Tentukan tanggal pengisian setoran harian (default: Hari Ini). Anda juga dapat melihat rekap Mingguan & Bulanan.'],
            ['title' => 'Isi Mutaba\'ah Santri', 'desc' => 'Klik tombol + Input Mutaba\'ah Santri atau klik baris nama santri untuk menginput Tahsin, Muraja\'ah, Kitabah, & Ziyadah.'],
            ['title' => 'Otomatis Terintegrasi', 'desc' => 'Setoran harian otomatis tersimpan per tanggal dan terakumulasi ke lembar Rapor Tahfizh & Portal Wali Murid.']
        ]"
        notes="Gunakan Tab Breakdown (Harian, Mingguan, Bulanan) untuk melihat rekapitulasi keaktifan setoran santri."
    />

    <!-- Main Control Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm space-y-6">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                    MUTABA'AH HARIAN GURU TAHFIZH
                </span>
                <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Lembar Mutaba'ah & Setoran Hafalan Santri</h1>
                <p class="text-stone-600 text-xs font-semibold mt-1">Pencatatan setoran harian per tanggal sekolah: Tahsin, Muraja'ah, Kitabah, dan Ziyadah.</p>
            </div>
            <x-button variant="primary" size="md" icon="plus" wire:click.prevent="openScoreModal" class="self-start lg:self-auto">
                Input Mutaba'ah Santri
            </x-button>
        </div>

        <!-- Filter Controls & Date Selector -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-stone-200">
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Halaqah Tahfizh / Kelas Bimbingan</label>
                <select wire:model.live="kelas_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-4 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                    @foreach($kelases as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} {{ strtolower($k->jenis_kelas) === 'tahfidz' ? '(Halaqah Tahfizh)' : '(Kelas Akademik)' }} - Pengampu: {{ $k->guruTahfidz->user->nama ?? 'Admin TU' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Semester & Tahun Ajaran</label>
                <select wire:model.live="semester_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-4 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->tahunAjaran->nama ?? '' }} - {{ ucfirst($sem->semester) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Tanggal Setoran Sekolah</label>
                <div class="flex items-center gap-2">
                    <input type="date" wire:model.live="tanggal" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                    <x-button variant="secondary" size="xs" wire:click="setTanggalToday" title="Set Tanggal Hari Ini" class="whitespace-nowrap shrink-0">
                        Hari Ini
                    </x-button>
                    <x-button variant="secondary" size="xs" wire:click="setTanggalYesterday" title="Set Tanggal Kemarin" class="whitespace-nowrap shrink-0">
                        Kemarin
                    </x-button>
                </div>
            </div>
        </div>

        <!-- Filter Livewire Loading Bar Indicator -->
        <div wire:loading.delay wire:target="kelas_id, semester_id, tanggal, search, selectedMonth, viewTab" class="w-full">
            <x-loading-state type="bar" target="kelas_id, semester_id, tanggal, search, selectedMonth, viewTab" />
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-xl text-xs font-bold flex items-center justify-between shadow-xs">
            <span>{{ session('message') }}</span>
            <span class="px-2.5 py-0.5 bg-emerald-200 text-emerald-900 rounded font-black text-[10px]">Tersimpan</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-300 text-rose-800 rounded-xl text-xs font-bold shadow-xs">
            {{ session('error') }}
        </div>
    @endif

    <!-- Breakdown View Tabs: Per Hari, Per Minggu, Per Bulan -->
    <div class="flex items-center justify-between border-b border-stone-200 pb-2">
        <div class="flex items-center gap-2 overflow-x-auto">
            <button wire:click="selectTab('daily')" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 border {{ $viewTab === 'daily' ? 'bg-emerald-700 text-white border-emerald-700 shadow-sm' : 'bg-white text-stone-600 border-stone-200 hover:bg-stone-50' }}">
                <svg class="w-4 h-4 {{ $viewTab === 'daily' ? 'text-white' : 'text-stone-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="{{ $viewTab === 'daily' ? 'text-white' : 'text-stone-700' }}">Tampilan Per Hari</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $viewTab === 'daily' ? 'bg-emerald-900 text-white' : 'bg-stone-100 text-stone-600' }}">
                    {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y') }}
                </span>
            </button>

            <button wire:click="selectTab('weekly')" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 border {{ $viewTab === 'weekly' ? 'bg-emerald-700 text-white border-emerald-700 shadow-sm' : 'bg-white text-stone-600 border-stone-200 hover:bg-stone-50' }}">
                <svg class="w-4 h-4 {{ $viewTab === 'weekly' ? 'text-white' : 'text-stone-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                <span class="{{ $viewTab === 'weekly' ? 'text-white' : 'text-stone-700' }}">Tampilan Per Minggu</span>
            </button>

            <button wire:click="selectTab('monthly')" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 border {{ $viewTab === 'monthly' ? 'bg-emerald-700 text-white border-emerald-700 shadow-sm' : 'bg-white text-stone-600 border-stone-200 hover:bg-stone-50' }}">
                <svg class="w-4 h-4 {{ $viewTab === 'monthly' ? 'text-white' : 'text-stone-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="{{ $viewTab === 'monthly' ? 'text-white' : 'text-stone-700' }}">Tampilan Per Bulan</span>
            </button>
        </div>

        @if($viewTab === 'monthly')
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-stone-600">Pilih Bulan:</label>
                <input type="month" wire:model.live="selectedMonth" class="px-3 py-1.5 bg-white border border-stone-300 rounded-xl text-xs font-bold text-stone-900 shadow-xs">
            </div>
        @endif
    </div>

    <!-- TAB 1: TAMPILAN PER HARI (DAILY VIEW) -->
    @if ($viewTab === 'daily')
        <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm space-y-4">
            <!-- Daily Toolbar Header -->
            <div class="p-4 bg-emerald-800 border-b border-emerald-900 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                        SETORAN HARIAN: {{ strtoupper(\Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y')) }}
                    </span>
                    <span class="px-2.5 py-0.5 bg-emerald-900 text-emerald-100 rounded-full text-[11px] font-bold">
                        {{ $dailyScores->count() }}/{{ count($siswas) }} Santri Terisi
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

            <div class="overflow-x-auto relative custom-scrollbar">
                <table class="w-full text-left border-separate border-spacing-0 text-xs text-stone-800">
                    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider select-none">
                        <tr>
                            <th rowspan="2" class="hidden md:table-cell p-3 text-center border-b border-r border-emerald-700 w-12 min-w-[48px] sticky left-0 bg-emerald-800 text-white z-20">NO</th>
                            <th rowspan="2" class="p-2.5 md:p-3 border-b border-r border-emerald-700 min-w-[130px] max-w-[145px] md:min-w-[220px] md:max-w-none sticky left-0 md:left-12 bg-emerald-800 text-white z-20 shadow-[3px_0_5px_-2px_rgba(0,0,0,0.25)] md:shadow-none">NISN & NAMA SANTRI</th>
                            <th rowspan="2" class="p-2.5 md:p-3 text-center border-b border-r border-emerald-700 w-12 text-white">L/P</th>
                            <th colspan="2" class="p-2 text-center border-b border-r border-emerald-700 bg-emerald-900/60 text-white">TAHSIN</th>
                            <th colspan="3" class="p-2 text-center border-b border-r border-emerald-700 bg-emerald-900/80 text-white">MURAJA'AH</th>
                            <th colspan="2" class="p-2 text-center border-b border-r border-emerald-700 bg-emerald-900/60 text-white">KITABAH</th>
                            <th colspan="2" class="p-2 text-center border-b border-r border-emerald-700 bg-emerald-900/80 text-white">ZIYADAH</th>
                            <th rowspan="2" class="p-3 text-center border-b border-r border-emerald-700 min-w-[200px] bg-emerald-900/60 text-white font-extrabold">TANGGAPAN ORANG TUA / WALI</th>
                            <th rowspan="2" class="p-3 text-center border-b min-w-[130px] text-white">AKSI</th>
                        </tr>

                        <tr>
                            <!-- Tahsin subheaders -->
                            <th class="p-2 text-center border-b border-r border-emerald-700 min-w-[120px] font-bold text-white">Materi/Ayat</th>
                            <th class="p-2 text-center border-b border-r border-emerald-700 w-16 bg-emerald-900 font-extrabold text-white">Nilai</th>
                            
                            <!-- Muraja'ah subheaders -->
                            <th class="p-2 text-center border-b border-r border-emerald-700 min-w-[90px] font-bold text-white">Bersama</th>
                            <th class="p-2 text-center border-b border-r border-emerald-700 min-w-[120px] font-bold text-white">Mandiri</th>
                            <th class="p-2 text-center border-b border-r border-emerald-700 w-16 bg-emerald-900 font-extrabold text-white">Nilai</th>

                            <!-- Kitabah subheaders -->
                            <th class="p-2 text-center border-b border-r border-emerald-700 min-w-[120px] font-bold text-white">Materi</th>
                            <th class="p-2 text-center border-b border-r border-emerald-700 w-16 bg-emerald-900 font-extrabold text-white">Nilai</th>

                            <!-- Ziyadah subheaders -->
                            <th class="p-2 text-center border-b border-r border-emerald-700 min-w-[120px] font-bold text-white">Materi</th>
                            <th class="p-2 text-center border-b border-r border-emerald-700 w-16 bg-emerald-900 font-extrabold text-white">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($siswas as $index => $s)
                            @php
                                $rec = $dailyScores->get($s->id);
                                $gender = strtolower($s->jenis_kelamin ?? 'L') === 'p' ? 'P' : 'L';
                                $isFilled = $rec !== null;
                            @endphp
                            <!-- Row Clickable for User-Friendly Interaction -->
                            <tr 
                                wire:click="openScoreModal({{ $s->id }})" 
                                class="hover:bg-emerald-50/70 cursor-pointer transition group"
                                title="Klik untuk mengedit/mengisi mutaba'ah tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }} untuk {{ $s->user->nama ?? $s->nama_panggilan }}"
                            >
                                <!-- No (Desktop Only) -->
                                <td class="hidden md:table-cell p-3 text-center font-bold text-stone-500 border-b border-r border-stone-200 text-xs sticky left-0 bg-white group-hover:bg-emerald-50/90 z-10">
                                    {{ $index + 1 }}
                                </td>

                                <!-- NISN & Nama Santri (Sticky Left with Status Pill + Kelas Badge) -->
                                <td class="p-2.5 md:p-3 border-b border-r-2 md:border-r border-stone-200 sticky left-0 md:left-12 bg-white group-hover:bg-emerald-50/90 z-10 shadow-[3px_0_6px_-2px_rgba(0,0,0,0.12)] md:shadow-none min-w-[130px] max-w-[145px] md:min-w-[220px] md:max-w-none">
                                    <div class="font-extrabold text-stone-900 text-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                        <span class="truncate">{{ strtoupper($s->user->nama ?? $s->nama_panggilan) }}</span>
                                        <span class="px-1.5 py-0.5 bg-emerald-100 border border-emerald-300 text-emerald-900 text-[9px] sm:text-[10px] rounded-md font-bold shrink-0 self-start sm:self-auto">
                                            {{ $s->kelas->nama_kelas ?? 'Kelas -' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="text-[10px] text-stone-500 font-medium">NISN: {{ $s->nisn }}</span>
                                        @if($isFilled)
                                            <span class="px-1.5 py-0.5 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded text-[9px] font-bold inline-flex items-center gap-1">
                                                <svg class="w-2.5 h-2.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                <span>Terisi</span>
                                            </span>
                                        @else
                                            <span class="px-1.5 py-0.5 bg-amber-100 border border-amber-300 text-amber-900 rounded text-[9px] font-bold inline-flex items-center gap-1">
                                                <span>Belum</span>
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="p-3 text-center font-bold text-stone-600 border-b border-r border-stone-200">{{ $gender }}</td>
                                
                                <!-- Tahsin -->
                                <td class="p-2 border-b border-r border-stone-200 text-stone-700 font-medium text-center">
                                    {{ $rec?->materi_tahsin ?? '-' }}
                                </td>
                                <td class="p-2 text-center border-b border-r border-stone-200 bg-emerald-50/40 font-black text-emerald-950">
                                    {{ ($rec && $rec->nilai_tahsin !== null) ? round($rec->nilai_tahsin) : '-' }}
                                </td>

                                <!-- Muraja'ah -->
                                <td class="p-2 border-b border-r border-stone-200 text-stone-700 font-medium text-center">
                                    {{ $rec?->murajaah_bersama ?? '-' }}
                                </td>
                                <td class="p-2 border-b border-r border-stone-200 text-stone-700 font-medium text-center">
                                    {{ $rec?->murajaah_mandiri ?? '-' }}
                                </td>
                                <td class="p-2 text-center border-b border-r border-stone-200 bg-emerald-50/40 font-black text-emerald-950">
                                    {{ ($rec && $rec->nilai_murajaah !== null) ? round($rec->nilai_murajaah) : '-' }}
                                </td>

                                <!-- Kitabah -->
                                <td class="p-2 border-b border-r border-stone-200 text-stone-700 font-medium text-center">
                                    {{ $rec?->materi_kitabah ?? '-' }}
                                </td>
                                <td class="p-2 text-center border-b border-r border-stone-200 bg-emerald-50/40 font-black text-emerald-950">
                                    {{ ($rec && $rec->nilai_kitabah !== null) ? round($rec->nilai_kitabah) : '-' }}
                                </td>

                                <!-- Ziyadah -->
                                <td class="p-2 border-b border-r border-stone-200 text-stone-700 font-medium text-center">
                                    {{ $rec?->materi_ziyadah ?? '-' }}
                                </td>
                                <td class="p-2 text-center border-b border-r border-stone-200 bg-emerald-50/40 font-black text-emerald-950">
                                    {{ ($rec && $rec->nilai_ziyadah !== null) ? round($rec->nilai_ziyadah) : '-' }}
                                </td>

                                <!-- Tanggapan Orang Tua -->
                                <td class="p-2 border-b border-r border-stone-200 text-stone-700 text-[11px]">
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
                                <td class="p-2 border-b text-center" @click.stop>
                                    @if($rec)
                                        <div class="flex items-center justify-center gap-1.5">
                                            <x-button type="button" variant="secondary" size="xs" icon="edit-3" wire:click.prevent="editScore({{ $rec->id }})" title="Edit Mutaba'ah">
                                                Edit
                                            </x-button>
                                            <x-button variant="danger" size="xs" icon="trash-2" wire:click.prevent="deleteScore({{ $rec->id }})" data-confirm="Apakah Anda yakin ingin menghapus data mutaba'ah santri ini?" title="Hapus Mutaba'ah">
                                                Hapus
                                            </x-button>
                                        </div>
                                    @else
                                        <x-button variant="primary" size="xs" icon="plus" wire:click.prevent="openScoreModal({{ $s->id }})">
                                            Isi Mutaba'ah
                                        </x-button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-table.empty :colspan="14" title="Tidak ada santri ditemukan" message="Pastikan Anda telah memilih kelas tahfizh yang aktif atau sesuaikan kata kunci pencarian." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 2: TAMPILAN PER MINGGU (WEEKLY SUMMARY VIEW) -->
    @if ($viewTab === 'weekly')
        <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm space-y-4">
            <div class="p-4 bg-emerald-800 border-b border-emerald-900 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xs font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                        <span>MATRIKS REKAP MINGGUAN (SENIN – SABTU)</span>
                    </h3>
                    <p class="text-[11px] text-emerald-100 font-medium mt-0.5">
                        Minggu Ke-{{ \Carbon\Carbon::parse($tanggal)->weekOfMonth }} Bulan {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('F Y') }}
                    </p>
                </div>
                <div class="text-xs text-emerald-100 font-semibold bg-emerald-900/80 px-3 py-1.5 rounded-xl border border-emerald-700">
                    Pilih tanggal di atas untuk berpindah minggu
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs text-stone-800">
                    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                        <tr>
                            <th class="p-3 text-center border-r border-emerald-700 w-10 text-white">NO</th>
                            <th class="p-3 border-r border-emerald-700 min-w-[200px] text-white">NAMA SANTRI</th>
                            @foreach ($weekDays as $wd)
                                <th class="p-2.5 text-center border-r border-emerald-700 min-w-[120px] text-white {{ $wd['is_selected'] ? 'bg-emerald-950 ring-2 ring-amber-400 ring-inset' : ($wd['is_today'] ? 'bg-emerald-700' : 'bg-emerald-800') }}">
                                    <div class="font-extrabold text-white">{{ $wd['day_name'] }}</div>
                                    <div class="text-[10px] text-emerald-100">{{ $wd['short_date'] }}</div>
                                </th>
                            @endforeach
                            <th class="p-3 text-center w-24 text-white">TOTAL SETORAN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse($siswas as $index => $s)
                            @php
                                $sWeekly = $weeklyScores[$s->id] ?? [];
                                $totalWeeklyCount = count($sWeekly);
                            @endphp
                            <tr class="hover:bg-stone-50 transition">
                                <td class="p-3 text-center font-bold text-stone-500 border-r border-stone-200">{{ $index + 1 }}</td>
                                <td class="p-3 border-r border-stone-200">
                                    <div class="font-extrabold text-stone-900 text-xs">{{ strtoupper($s->user->nama ?? $s->nama_panggilan) }}</div>
                                    <div class="text-[10px] text-stone-500 font-medium">NISN: {{ $s->nisn }}</div>
                                </td>

                                @foreach ($weekDays as $wd)
                                    @php
                                        $dRec = $sWeekly[$wd['date']] ?? null;
                                    @endphp
                                    <td class="p-2 border-r border-stone-200 text-center align-top {{ $wd['is_selected'] ? 'bg-amber-50/50' : '' }}">
                                        @if($dRec)
                                            <div class="bg-emerald-50 border border-emerald-200 p-2 rounded-lg space-y-1">
                                                <span class="px-1.5 py-0.5 bg-emerald-700 text-white rounded text-[9px] font-bold block">Terisi</span>
                                                @if($dRec->materi_ziyadah)
                                                    <div class="text-[10px] font-bold text-stone-800 truncate" title="Ziyadah: {{ $dRec->materi_ziyadah }}">
                                                        Z: {{ $dRec->materi_ziyadah }}
                                                    </div>
                                                @endif
                                                @if($dRec->nilai_ziyadah !== null || $dRec->nilai_tahsin !== null)
                                                    <div class="text-[10px] font-black text-emerald-900">
                                                        Nilai: {{ round($dRec->nilai_ziyadah ?? $dRec->nilai_tahsin) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-stone-300 font-medium italic text-[10px] block py-3">Belum</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="p-3 text-center font-black text-stone-900 text-xs bg-stone-50">
                                    {{ $totalWeeklyCount }}/6 Hari
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-stone-500 italic font-medium">Tidak ada santri ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 3: TAMPILAN PER BULAN (MONTHLY SUMMARY VIEW) -->
    @if ($viewTab === 'monthly')
        <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm space-y-4">
            <div class="p-4 bg-emerald-800 border-b border-emerald-900 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xs font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                        <span>REKAPITULASI CAPAIAN BULANAN ({{ strtoupper(\Carbon\Carbon::parse(($selectedMonth ?: date('Y-m')) . '-01')->translatedFormat('F Y')) }})</span>
                    </h3>
                    <p class="text-[11px] text-emerald-100 font-medium mt-0.5">
                        Ringkasan total frekuensi setoran harian, nilai rata-rata, dan capaian surah terakhir santri.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs text-stone-800">
                    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                        <tr>
                            <th class="p-3 text-center border-r border-emerald-700 w-10 text-white">NO</th>
                            <th class="p-3 border-r border-emerald-700 min-w-[220px] text-white">NAMA SANTRI</th>
                            <th class="p-3 text-center border-r border-emerald-700 w-36 text-white">FREKUENSI SETORAN</th>
                            <th class="p-3 text-center border-r border-emerald-700 w-32 text-white">RATA-RATA NILAI</th>
                            <th class="p-3 border-r border-emerald-700 min-w-[180px] text-white">SURAH TERAKHIR / ZIYADAH</th>
                            <th class="p-3 text-center border-r border-emerald-700 w-24 text-white">JUZ TERTINGGI</th>
                            <th class="p-3 text-center w-28 text-white">PREDIKAT</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse($siswas as $index => $s)
                            @php
                                $mSummary = $monthlyScores[$s->id] ?? [
                                    'total_entries' => 0,
                                    'avg_score' => '-',
                                    'latest_ziyadah' => '-',
                                    'max_juz' => 1,
                                ];
                                $avgVal = is_numeric($mSummary['avg_score']) ? (float)$mSummary['avg_score'] : null;
                                $pred = $avgVal ? ($avgVal >= 85 ? 'Sangat Baik' : ($avgVal >= 75 ? 'Baik' : 'Cukup')) : '-';
                            @endphp
                            <tr class="hover:bg-stone-50 transition">
                                <td class="p-3 text-center font-bold text-stone-500 border-r border-stone-200">{{ $index + 1 }}</td>
                                <td class="p-3 border-r border-stone-200">
                                    <div class="font-extrabold text-stone-900 text-xs">{{ strtoupper($s->user->nama ?? $s->nama_panggilan) }}</div>
                                    <div class="text-[10px] text-stone-500 font-medium">NISN: {{ $s->nisn }}</div>
                                </td>
                                <td class="p-3 text-center border-r border-stone-200">
                                    <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full font-extrabold text-xs">
                                        {{ $mSummary['total_entries'] }} Kali Setoran
                                    </span>
                                </td>
                                <td class="p-3 text-center font-black text-emerald-950 border-r border-stone-200 text-xs">
                                    {{ $mSummary['avg_score'] }}
                                </td>
                                <td class="p-3 border-r border-stone-200 font-bold text-stone-800">
                                    {{ $mSummary['latest_ziyadah'] }}
                                </td>
                                <td class="p-3 text-center border-r border-stone-200 font-extrabold text-stone-700">
                                    Juz {{ $mSummary['max_juz'] }}
                                </td>
                                <td class="p-3 text-center">
                                    @if($pred === 'Sangat Baik')
                                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-full font-bold text-[10px]">Sangat Baik</span>
                                    @elseif($pred === 'Baik')
                                        <span class="px-2.5 py-1 bg-blue-100 text-blue-900 border border-blue-300 rounded-full font-bold text-[10px]">Baik</span>
                                    @elseif($pred === 'Cukup')
                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-900 border border-amber-300 rounded-full font-bold text-[10px]">Cukup</span>
                                    @else
                                        <span class="text-stone-400 font-medium text-[11px] italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-stone-500 italic font-medium">Tidak ada santri ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- MODAL FORM MUTABA'AH GURU TAHFIZH -->
    @if($showScoreModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center lg:pl-64 p-4 lg:p-8 bg-stone-900/60 backdrop-blur-xs">
            <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-2xl max-w-2xl w-full space-y-4">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-900 text-xs flex items-center justify-center font-black">
                            <svg class="w-3.5 h-3.5 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </span>
                        <span>{{ $editingId ? 'Edit Mutaba\'ah Santri' : 'Input Mutaba\'ah Santri (SD TAHFIZH F3)' }}</span>
                    </h3>
                    <button type="button" wire:click.prevent="closeScoreModal" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold">✕</button>
                </div>

                <form wire:submit.prevent="saveScore" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Pilih Santri Target <span class="text-rose-600">*</span></label>
                            <select wire:model="siswa_id" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                                <option value="">-- Pilih Santri --</option>
                                @foreach($siswas as $s)
                                    <option value="{{ $s->id }}">{{ strtoupper($s->user->nama ?? $s->nama_panggilan) }} (NISN: {{ $s->nisn }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Tanggal Setoran <span class="text-rose-600">*</span></label>
                            <input type="date" wire:model="tanggal" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                        </div>
                    </div>

                    <!-- Grid Mutaba'ah 4 Kategori -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                        <!-- TAHSIN -->
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                            <span class="text-xs font-black text-emerald-950 uppercase block">1. Tahsin</span>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block mb-1">Materi / Surah (Ayat)</label>
                                <x-surah-autocomplete wireModel="materi_tahsin" placeholder="Ketik surah, contoh: Al-Baqarah 4-5" />
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block mb-1">Nilai Tahsin (0-100)</label>
                                <input type="number" step="1" wire:model="nilai_tahsin" placeholder="0-100 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-bold text-emerald-900 focus:ring-2 focus:ring-emerald-600 shadow-xs">
                            </div>
                        </div>

                        <!-- MURAJA'AH -->
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                            <span class="text-xs font-black text-emerald-950 uppercase block">2. Muraja'ah</span>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[10px] font-bold text-stone-600 block mb-1">Bersama</label>
                                    <x-surah-autocomplete wireModel="murajaah_bersama" :includeJuz="true" placeholder="Juz 30 / Al-Baqarah" />
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-stone-600 block mb-1">Mandiri</label>
                                    <x-surah-autocomplete wireModel="murajaah_mandiri" placeholder="Al-Baqarah 1-30" />
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block mb-1">Nilai Muraja'ah (0-100)</label>
                                <input type="number" step="1" wire:model="nilai_murajaah" placeholder="0-100 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-bold text-emerald-900 focus:ring-2 focus:ring-emerald-600 shadow-xs">
                            </div>
                        </div>

                        <!-- KITABAH -->
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                            <span class="text-xs font-black text-emerald-950 uppercase block">3. Tahfizh - Kitabah</span>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block mb-1">Materi Kitabah</label>
                                <x-surah-autocomplete wireModel="materi_kitabah" placeholder="Ketik surah, contoh: Al-Baqarah 39-40" />
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block mb-1">Nilai Kitabah (0-100)</label>
                                <input type="number" step="1" wire:model="nilai_kitabah" placeholder="0-100 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-bold text-emerald-900 focus:ring-2 focus:ring-emerald-600 shadow-xs">
                            </div>
                        </div>

                        <!-- ZIYADAH -->
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                            <span class="text-xs font-black text-emerald-950 uppercase block">4. Tahfizh - Ziyadah</span>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block mb-1">Materi Ziyadah Baru</label>
                                <x-surah-autocomplete wireModel="materi_ziyadah" placeholder="Ketik surah, contoh: Al-Baqarah 11-20" />
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block mb-1">Nilai Ziyadah (0-100)</label>
                                <input type="number" step="1" wire:model="nilai_ziyadah" placeholder="0-100 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-bold text-emerald-900 focus:ring-2 focus:ring-emerald-600 shadow-xs">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Catatan Ustadz Pembimbing</label>
                        <textarea wire:model="catatan_ustadz" rows="2" placeholder="Tuliskan catatan perkembangan hafalan dan motivasi santri..." class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-medium resize-none focus:ring-2 focus:ring-emerald-600 shadow-xs"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                        <x-button variant="secondary" size="md" wire:click.prevent="closeScoreModal">
                            Batal
                        </x-button>
                        <x-button variant="primary" size="md" type="submit" loadingTarget="saveScore">
                            Simpan Mutaba'ah
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Alpine.js Auto-Complete Component Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            if (!window.QURAN_SURAHS_DATA) {
                window.QURAN_SURAHS_DATA = [
                    { no: 1, name: "Al-Fatihah", info: "7 Ayat" },
                    { no: 2, name: "Al-Baqarah", info: "286 Ayat" },
                    { no: 3, name: "Ali 'Imran", info: "200 Ayat" },
                    { no: 4, name: "An-Nisa'", info: "176 Ayat" },
                    { no: 5, name: "Al-Ma'idah", info: "120 Ayat" },
                    { no: 6, name: "Al-An'am", info: "165 Ayat" },
                    { no: 7, name: "Al-A'raf", info: "206 Ayat" },
                    { no: 8, name: "Al-Anfal", info: "75 Ayat" },
                    { no: 9, name: "At-Taubah", info: "129 Ayat" },
                    { no: 10, name: "Yunus", info: "109 Ayat" },
                    { no: 11, name: "Hud", info: "123 Ayat" },
                    { no: 12, name: "Yusuf", info: "111 Ayat" },
                    { no: 13, name: "Ar-Ra'd", info: "43 Ayat" },
                    { no: 14, name: "Ibrahim", info: "52 Ayat" },
                    { no: 15, name: "Al-Hijr", info: "99 Ayat" },
                    { no: 16, name: "An-Nahl", info: "128 Ayat" },
                    { no: 17, name: "Al-Isra'", info: "111 Ayat" },
                    { no: 18, name: "Al-Kahf", info: "110 Ayat" },
                    { no: 19, name: "Maryam", info: "98 Ayat" },
                    { no: 20, name: "Ta-Ha", info: "135 Ayat" },
                    { no: 21, name: "Al-Anbiya'", info: "112 Ayat" },
                    { no: 22, name: "Al-Hajj", info: "78 Ayat" },
                    { no: 23, name: "Al-Mu'minun", info: "118 Ayat" },
                    { no: 24, name: "An-Nur", info: "64 Ayat" },
                    { no: 25, name: "Al-Furqan", info: "77 Ayat" },
                    { no: 26, name: "Asy-Syu'ara'", info: "227 Ayat" },
                    { no: 27, name: "An-Naml", info: "93 Ayat" },
                    { no: 28, name: "Al-Qasas", info: "88 Ayat" },
                    { no: 29, name: "Al-'Ankabut", info: "69 Ayat" },
                    { no: 30, name: "Ar-Rum", info: "60 Ayat" },
                    { no: 31, name: "Luqman", info: "34 Ayat" },
                    { no: 32, name: "As-Sajdah", info: "30 Ayat" },
                    { no: 33, name: "Al-Ahzab", info: "73 Ayat" },
                    { no: 34, name: "Saba'", info: "54 Ayat" },
                    { no: 35, name: "Fatir", info: "45 Ayat" },
                    { no: 36, name: "Ya-Sin", info: "83 Ayat" },
                    { no: 37, name: "As-Saffat", info: "182 Ayat" },
                    { no: 38, name: "Sad", info: "88 Ayat" },
                    { no: 39, name: "Az-Zumar", info: "75 Ayat" },
                    { no: 40, name: "Ghafir", info: "85 Ayat" },
                    { no: 41, name: "Fussilat", info: "54 Ayat" },
                    { no: 42, name: "Asy-Syura", info: "53 Ayat" },
                    { no: 43, name: "Az-Zukhruf", info: "89 Ayat" },
                    { no: 44, name: "Ad-Dukhan", info: "59 Ayat" },
                    { no: 45, name: "Al-Jasiyah", info: "37 Ayat" },
                    { no: 46, name: "Al-Ahqaf", info: "35 Ayat" },
                    { no: 47, name: "Muhammad", info: "38 Ayat" },
                    { no: 48, name: "Al-Fath", info: "29 Ayat" },
                    { no: 49, name: "Al-Hujurat", info: "18 Ayat" },
                    { no: 50, name: "Qaf", info: "45 Ayat" },
                    { no: 51, name: "Az-Zariyat", info: "60 Ayat" },
                    { no: 52, name: "At-Tur", info: "49 Ayat" },
                    { no: 53, name: "An-Najm", info: "62 Ayat" },
                    { no: 54, name: "Al-Qamar", info: "55 Ayat" },
                    { no: 55, name: "Ar-Rahman", info: "78 Ayat" },
                    { no: 56, name: "Al-Waqi'ah", info: "96 Ayat" },
                    { no: 57, name: "Al-Hadid", info: "29 Ayat" },
                    { no: 58, name: "Al-Mujadilah", info: "22 Ayat" },
                    { no: 59, name: "Al-Hasyr", info: "24 Ayat" },
                    { no: 60, name: "Al-Mumtahanah", info: "13 Ayat" },
                    { no: 61, name: "As-Saff", info: "14 Ayat" },
                    { no: 62, name: "Al-Jumu'ah", info: "11 Ayat" },
                    { no: 63, name: "Al-Munafiqun", info: "11 Ayat" },
                    { no: 64, name: "At-Taghabun", info: "18 Ayat" },
                    { no: 65, name: "At-Talaq", info: "12 Ayat" },
                    { no: 66, name: "At-Tahrim", info: "12 Ayat" },
                    { no: 67, name: "Al-Mulk", info: "30 Ayat" },
                    { no: 68, name: "Al-Qalam", info: "52 Ayat" },
                    { no: 69, name: "Al-Haqqah", info: "52 Ayat" },
                    { no: 70, name: "Al-Ma'arij", info: "44 Ayat" },
                    { no: 71, name: "Nuh", info: "28 Ayat" },
                    { no: 72, name: "Al-Jinn", info: "28 Ayat" },
                    { no: 73, name: "Al-Muzzammil", info: "20 Ayat" },
                    { no: 74, name: "Al-Muddassir", info: "56 Ayat" },
                    { no: 75, name: "Al-Qiyamah", info: "40 Ayat" },
                    { no: 76, name: "Al-Insan", info: "31 Ayat" },
                    { no: 77, name: "Al-Mursalat", info: "50 Ayat" },
                    { no: 78, name: "An-Naba'", info: "40 Ayat" },
                    { no: 79, name: "An-Nazi'at", info: "46 Ayat" },
                    { no: 80, name: "'Abasa", info: "42 Ayat" },
                    { no: 81, name: "At-Takwir", info: "29 Ayat" },
                    { no: 82, name: "Al-Infitar", info: "19 Ayat" },
                    { no: 83, name: "Al-Mutaffifin", info: "36 Ayat" },
                    { no: 84, name: "Al-Insyiqaq", info: "25 Ayat" },
                    { no: 85, name: "Al-Buruj", info: "22 Ayat" },
                    { no: 86, name: "At-Tariq", info: "17 Ayat" },
                    { no: 87, name: "Al-A'la", info: "19 Ayat" },
                    { no: 88, name: "Al-Ghasyiyah", info: "26 Ayat" },
                    { no: 89, name: "Al-Fajr", info: "30 Ayat" },
                    { no: 90, name: "Al-Balad", info: "20 Ayat" },
                    { no: 91, name: "Asy-Syams", info: "15 Ayat" },
                    { no: 92, name: "Al-Lail", info: "21 Ayat" },
                    { no: 93, name: "Ad-Duha", info: "11 Ayat" },
                    { no: 94, name: "Asy-Syarh", info: "8 Ayat" },
                    { no: 95, name: "At-Tin", info: "8 Ayat" },
                    { no: 96, name: "Al-'Alaq", info: "19 Ayat" },
                    { no: 97, name: "Al-Qadr", info: "5 Ayat" },
                    { no: 98, name: "Al-Bayyinah", info: "8 Ayat" },
                    { no: 99, name: "Az-Zalzalah", info: "8 Ayat" },
                    { no: 100, name: "Al-'Adiyat", info: "11 Ayat" },
                    { no: 101, name: "Al-Qari'ah", info: "11 Ayat" },
                    { no: 102, name: "At-Takasur", info: "8 Ayat" },
                    { no: 103, name: "Al-'Asr", info: "3 Ayat" },
                    { no: 104, name: "Al-Humazah", info: "9 Ayat" },
                    { no: 105, name: "Al-Fil", info: "5 Ayat" },
                    { no: 106, name: "Quraisy", info: "4 Ayat" },
                    { no: 107, name: "Al-Ma'un", info: "7 Ayat" },
                    { no: 108, name: "Al-Kausar", info: "3 Ayat" },
                    { no: 109, name: "Al-Kafirun", info: "6 Ayat" },
                    { no: 110, name: "An-Nasr", info: "3 Ayat" },
                    { no: 111, name: "Al-Lahab", info: "5 Ayat" },
                    { no: 112, name: "Al-Ikhlas", info: "4 Ayat" },
                    { no: 113, name: "Al-Falaq", info: "5 Ayat" },
                    { no: 114, name: "An-Nas", info: "6 Ayat" }
                ];
                window.QURAN_JUZS_DATA = Array.from({ length: 30 }, (_, i) => ({
                    no: i + 1,
                    name: `Juz ${i + 1}`,
                    info: `Juz ${i + 1}`,
                    isJuz: true
                }));
            }

            Alpine.data('surahAutocomplete', (config) => ({
                wireField: config.wireField,
                includeJuz: config.includeJuz || false,
                open: false,
                highlightedIndex: 0,

                get currentVal() {
                    return (this.$wire && this.wireField) ? (this.$wire.get(this.wireField) || '') : '';
                },

                get allItems() {
                    let list = [...window.QURAN_SURAHS_DATA];
                    if (this.includeJuz) {
                        list = [...window.QURAN_JUZS_DATA, ...list];
                    }
                    return list;
                },

                get filteredList() {
                    const val = (this.currentVal || '').trim();
                    if (!val) {
                        if (this.includeJuz) {
                            return [...window.QURAN_JUZS_DATA.slice(27, 30), ...window.QURAN_SURAHS_DATA.slice(77, 85)];
                        }
                        return [window.QURAN_SURAHS_DATA[0], window.QURAN_SURAHS_DATA[1], ...window.QURAN_SURAHS_DATA.slice(77, 85)];
                    }

                    const q = val.toLowerCase().replace(/[^a-z0-9]/g, '');

                    return this.allItems.filter(item => {
                        const normName = item.name.toLowerCase().replace(/[^a-z0-9]/g, '');
                        return normName.includes(q) || item.name.toLowerCase().includes(val.toLowerCase());
                    }).slice(0, 10);
                },

                onInput(e) {
                    this.open = true;
                    this.highlightedIndex = 0;
                },

                onFocus() {
                    this.open = true;
                    this.highlightedIndex = 0;
                },

                toggleDropdown() {
                    this.open = !this.open;
                    this.highlightedIndex = 0;
                },

                navigateNext() {
                    if (!this.open) { this.open = true; return; }
                    if (this.highlightedIndex < this.filteredList.length - 1) {
                        this.highlightedIndex++;
                    } else {
                        this.highlightedIndex = 0;
                    }
                },

                navigatePrev() {
                    if (!this.open) { this.open = true; return; }
                    if (this.highlightedIndex > 0) {
                        this.highlightedIndex--;
                    } else {
                        this.highlightedIndex = this.filteredList.length - 1;
                    }
                },

                selectHighlighted() {
                    if (this.open && this.filteredList.length > 0) {
                        this.chooseItem(this.filteredList[this.highlightedIndex].name);
                    }
                },

                chooseItem(name) {
                    if (this.$wire && this.wireField) {
                        this.$wire.set(this.wireField, name + ' ');
                    }
                    this.open = false;
                    this.$nextTick(() => {
                        if (this.$refs.inputField) {
                            this.$refs.inputField.focus();
                            const len = this.$refs.inputField.value.length;
                            this.$refs.inputField.setSelectionRange(len, len);
                        }
                    });
                }
            }));
        });
    </script>
</div>
