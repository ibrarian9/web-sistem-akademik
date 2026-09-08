<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Monitoring Akademik & Pembelajaran" 
        subtitle="Pusat supervisi terpadu rekapitulasi nilai siswa, pengawasan kurikulum Bab & TP, serta pemantauan presensi harian."
        badge="SUPERVISI AKADEMIK"
        badgeVariant="emerald"
        icon="activity"
    />

    <!-- Quick KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card 
            title="Rata-rata Nilai Rapor" 
            :value="$avgNilaiSekolah > 0 ? number_format($avgNilaiSekolah, 1) : '-'"
            subtitle="Semester Aktif Berjalan"
            icon="award"
            variant="emerald"
        />
        <x-stat-card 
            title="Kurikulum Bab & TP" 
            :value="$totalBab . ' Bab'"
            :subtitle="$totalTp . ' Total TP Disusun Guru'"
            icon="layers"
            variant="sky"
        />
        <x-stat-card 
            title="Kehadiran Santri Hari Ini" 
            :value="$persenHadirSiswaToday > 0 ? $persenHadirSiswaToday . '%' : '-'"
            :subtitle="'Tanggal: ' . \Carbon\Carbon::parse($selectedTanggal)->isoFormat('D MMM Y')"
            icon="users"
            variant="teal"
        />
        <x-stat-card 
            title="Kehadiran Guru Hari Ini" 
            :value="$guruHadirToday . ' / ' . $totalGuruAktif"
            subtitle="Guru Hadir di Sekolah"
            icon="user-check"
            variant="amber"
        />
    </div>

    <!-- Main Navigation Tabs -->
    <div class="bg-white border border-stone-200 p-2 rounded-2xl shadow-xs flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2 overflow-x-auto">
            <button wire:click="setTab('nilai')" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'nilai' ? 'bg-emerald-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
                <x-lucide-award class="w-4 h-4" />
                <span>Monitoring Nilai Siswa</span>
            </button>
            <button wire:click="setTab('kurikulum')" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'kurikulum' ? 'bg-emerald-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
                <x-lucide-layers class="w-4 h-4" />
                <span>Monitoring Bab & TP</span>
            </button>
            <button wire:click="setTab('absen')" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'absen' ? 'bg-emerald-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
                <x-lucide-clipboard-check class="w-4 h-4" />
                <span>Monitoring Absensi</span>
            </button>
        </div>

        <div class="text-[11px] font-semibold text-stone-400 px-3 hidden md:block">
            Mode Supervisi Eksekutif &bull; Antarmuka Ramah Pengguna
        </div>
    </div>

    <!-- ==================== TAB 1: MONITORING NILAI ==================== -->
    @if ($activeTab === 'nilai')
        <div class="space-y-6">
            <!-- Filter Selector Bar -->
            <div class="bg-white border border-stone-200 rounded-2xl shadow-xs p-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Rombel Kelas</label>
                        <select wire:model.live="selectedKelasId" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->nama_kelas }} (Tingkat {{ $c->tingkat }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Mata Pelajaran</label>
                        <select wire:model.live="selectedMapelId" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                            <option value="">-- Pilih Mapel --</option>
                            @foreach ($mapels as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Semester</label>
                        <select wire:model.live="selectedSemesterId" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                            @foreach ($semesters as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_semester }} ({{ $s->tahunAjaran->nama_tahun ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Meta & Summary Info Strip -->
            <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold border border-emerald-200 shrink-0">
                        <x-lucide-user-check class="w-6 h-6" />
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-stone-400 block">Guru Pengampu Kelas:</span>
                        <h4 class="text-sm font-extrabold text-stone-900">{{ $matrixNilai['guruPengampu'] }}</h4>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-wider text-stone-400 block">Rata-rata Nilai:</span>
                        <span class="text-base font-black text-emerald-800">{{ $matrixNilai['avgKelas'] > 0 ? $matrixNilai['avgKelas'] : '-' }}</span>
                    </div>
                    <div class="h-8 w-px bg-stone-200 hidden sm:block"></div>
                    <div class="flex items-center gap-1.5">
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-md text-xs font-black" title="Predikat A (>=90)">A: {{ $matrixNilai['distribusi']['A'] }}</span>
                        <span class="px-2 py-0.5 bg-sky-100 text-sky-800 rounded-md text-xs font-black" title="Predikat B (80-89)">B: {{ $matrixNilai['distribusi']['B'] }}</span>
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-md text-xs font-black" title="Predikat C (70-79)">C: {{ $matrixNilai['distribusi']['C'] }}</span>
                        <span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded-md text-xs font-black" title="Predikat D (<70)">D: {{ $matrixNilai['distribusi']['D'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Matrix Nilai Table -->
            <div class="bg-white border border-stone-200 rounded-2xl shadow-xs overflow-hidden">
                <x-table loadingTarget="selectedKelasId, selectedMapelId, selectedSemesterId">
                    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                        <tr>
                            <x-table.th align="center" class="w-12">No</x-table.th>
                            <x-table.th class="w-56">Nama Santri</x-table.th>
                            @foreach ($matrixNilai['babs'] as $index => $bab)
                                <x-table.th align="center" class="w-28">
                                    Bab {{ $bab->urutan ?? ($index + 1) }}
                                    <div class="text-[9px] text-emerald-200 font-medium mt-0.5 truncate max-w-[100px]" title="{{ $bab->nama_lingkup_materi }}">
                                        {{ \Illuminate\Support\Str::limit($bab->nama_lingkup_materi, 14) }}
                                    </div>
                                </x-table.th>
                            @endforeach
                            <x-table.th align="center" class="w-24 bg-emerald-850 text-white font-bold">SAS</x-table.th>
                            <x-table.th align="center" class="w-28 bg-emerald-900 text-white font-black">Nilai Akhir</x-table.th>
                            <x-table.th align="center" class="w-20 bg-emerald-950 text-white font-black">Predikat</x-table.th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($matrixNilai['students'] as $index => $row)
                            <tr class="hover:bg-stone-50 transition">
                                <td class="p-3 text-center border-r border-stone-200 font-bold text-stone-400 text-xs">{{ $index + 1 }}</td>
                                <td class="p-3 border-r border-stone-200 font-bold text-stone-900 text-xs">
                                    {{ $row['siswa']->user->nama }}
                                    <div class="text-[10px] text-stone-400 font-semibold mt-0.5">NIS: {{ $row['siswa']->nis }}</div>
                                </td>
                                @foreach ($matrixNilai['babs'] as $bab)
                                    @php
                                        $val = $row['babGrades'][$bab->id] ?? null;
                                    @endphp
                                    <td class="p-3 text-center border-r border-stone-200 text-xs {{ is_null($val) ? 'text-stone-300' : 'text-stone-800 font-bold' }}">
                                        {{ is_null($val) ? '•' : $val }}
                                    </td>
                                @endforeach
                                <td class="p-3 text-center border-r border-stone-200 text-xs {{ is_null($row['nilaiSas']) ? 'text-stone-300' : 'text-stone-800 font-bold' }}">
                                    {{ is_null($row['nilaiSas']) ? '•' : $row['nilaiSas'] }}
                                </td>
                                <td class="p-3 text-center border-r border-stone-200 bg-emerald-50/50 text-emerald-800 font-black text-sm">
                                    {{ $row['finalGrade'] !== null ? $row['finalGrade'] : '-' }}
                                </td>
                                <td class="p-3 text-center bg-stone-50 font-black text-xs">
                                    @php
                                        $badgeVariant = match($row['predikat']) {
                                            'A' => 'emerald',
                                            'B' => 'sky',
                                            'C' => 'amber',
                                            'D' => 'rose',
                                            default => 'stone',
                                        };
                                    @endphp
                                    <x-badge :variant="$badgeVariant" size="xs">{{ $row['predikat'] }}</x-badge>
                                </td>
                            </tr>
                        @empty
                            <x-table.empty :colspan="$matrixNilai['babs']->count() + 5" title="Belum ada data nilai" message="Tidak ada data siswa aktif atau penilaian pada rombel kelas ini." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>
        </div>

    <!-- ==================== TAB 2: MONITORING BAB & TP ==================== -->
    @elseif ($activeTab === 'kurikulum')
        <div class="space-y-6">
            <!-- Filter & Search Bar -->
            <div class="bg-white border border-stone-200 rounded-2xl shadow-xs p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="w-full md:w-1/3">
                    <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Pilih Mata Pelajaran</label>
                    <select wire:model.live="kurikulumMapelId" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                        @foreach ($mapels as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-1/2">
                    <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Cari Bab atau Tujuan Pembelajaran (TP)</label>
                    <div class="relative">
                        <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3.5 top-3" />
                        <input type="text" wire:model.live.debounce.300ms="searchBabTp" placeholder="Ketik kata kunci nama materi atau tujuan pembelajaran..." class="w-full rounded-xl border border-stone-200 bg-stone-50 pl-10 pr-4 py-2.5 text-xs font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Guru Pengampu & Summary Header -->
            @if ($kurikulumData['mapel'])
                <div class="bg-emerald-50/70 border border-emerald-200 rounded-2xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-600 text-white">
                                KURIKULUM MERDEKA
                            </span>
                            <h3 class="text-base font-extrabold text-stone-900">{{ $kurikulumData['mapel']->nama_mapel }}</h3>
                        </div>
                        <p class="text-xs text-stone-600">
                            Total: <strong class="text-emerald-800">{{ $kurikulumData['babs']->count() }} Bab (Lingkup Materi)</strong> 
                            &bull; <strong class="text-emerald-800">{{ $kurikulumData['babs']->sum(fn($b) => $b->tujuanPembelajaran->count()) }} Total TP</strong>
                        </p>
                    </div>

                    <!-- Assigned Teachers Pill -->
                    <div class="text-xs text-stone-600 flex flex-wrap items-center gap-2">
                        <span class="font-bold text-stone-500">Guru Pengampu:</span>
                        @forelse ($kurikulumData['guruList']->pluck('guru.user.nama')->unique() as $guruName)
                            <span class="px-3 py-1 bg-white border border-stone-200 rounded-xl font-bold text-stone-800 shadow-2xs">
                                {{ $guruName }}
                            </span>
                        @empty
                            <span class="italic text-stone-400">Belum diplot ke kelas</span>
                        @endforelse
                    </div>
                </div>

                <!-- Template Narasi Info -->
                @if ($kurikulumData['template'])
                    <div class="bg-stone-50 border border-stone-200 rounded-2xl p-4 text-xs text-stone-600 space-y-1">
                        <div class="font-bold text-stone-700 flex items-center gap-1.5">
                            <x-lucide-info class="w-4 h-4 text-emerald-600" />
                            <span>Format Template Narasi Rapor Otomatis:</span>
                        </div>
                        <p>&bull; Capaian Tertinggi: <span class="font-semibold text-emerald-800">"Ananda {{ $kurikulumData['template']->frasa_tertinggi }} [Deskripsi TP]"</span></p>
                        <p>&bull; Capaian Terendah: <span class="font-semibold text-amber-800">"namun {{ $kurikulumData['template']->frasa_terendah }} [Deskripsi TP]"</span></p>
                    </div>
                @endif

                <!-- List of Babs and TPs -->
                <div class="space-y-4">
                    @forelse ($kurikulumData['babs'] as $bab)
                        <div class="bg-white border border-stone-200 rounded-2xl shadow-xs overflow-hidden">
                            <div class="p-4 bg-stone-50/80 border-b border-stone-200 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                        {{ $bab->urutan }}
                                    </span>
                                    <div>
                                        <h4 class="text-sm font-extrabold text-stone-900">{{ $bab->nama_lingkup_materi }}</h4>
                                        <span class="text-[10px] text-stone-400 font-semibold uppercase">Bab / Lingkup Materi {{ $bab->urutan }}</span>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-extrabold">
                                    {{ $bab->tujuanPembelajaran->count() }} TP Disusun
                                </span>
                            </div>

                            <div class="p-4 divide-y divide-stone-100">
                                @forelse ($bab->tujuanPembelajaran as $idx => $tp)
                                    <div class="py-3 first:pt-0 last:pb-0 flex items-start gap-3">
                                        <span class="px-2 py-0.5 bg-stone-100 border border-stone-200 rounded-md text-[10px] font-black text-stone-600 shrink-0 mt-0.5">
                                            TP {{ $tp->urutan ?? ($idx + 1) }}
                                        </span>
                                        <p class="text-xs font-semibold text-stone-800 leading-relaxed">
                                            {{ $tp->deskripsi_tp }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="py-4 text-center text-xs text-stone-400 italic">
                                        Belum ada rincian Tujuan Pembelajaran (TP) yang dimasukkan untuk Bab ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="bg-white border border-stone-200 rounded-3xl p-12 text-center space-y-2">
                            <x-lucide-layers class="w-10 h-10 text-stone-300 mx-auto" />
                            <h4 class="text-sm font-bold text-stone-700">Belum Ada Bab & TP yang Disusun</h4>
                            <p class="text-xs text-stone-400">Guru pengampu mata pelajaran ini belum membuat Lingkup Materi pada portal Guru.</p>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>

    <!-- ==================== TAB 3: MONITORING ABSENSI ==================== -->
    @elseif ($activeTab === 'absen')
        <div class="space-y-6">
            <!-- Sub-tab & Date Filter Bar -->
            <div class="bg-white border border-stone-200 rounded-2xl shadow-xs p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <button wire:click="setAbsenSubTab('siswa')" 
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $absenSubTab === 'siswa' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                        <x-lucide-users class="w-4 h-4" />
                        <span>Presensi Santri</span>
                    </button>
                    <button wire:click="setAbsenSubTab('guru')" 
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $absenSubTab === 'guru' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                        <x-lucide-user-check class="w-4 h-4" />
                        <span>Presensi Guru</span>
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <label class="text-xs font-bold text-stone-500 uppercase tracking-wider">Tanggal:</label>
                    <input type="date" wire:model.live="selectedTanggal" class="rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2 text-xs font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Sub-tab 1: Absensi Siswa -->
            @if ($absenSubTab === 'siswa')
                <!-- Status Metrics Banner -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    <div class="bg-emerald-50 border border-emerald-200 p-3.5 rounded-xl text-center">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 block">Hadir</span>
                        <span class="text-xl font-black text-emerald-900">{{ $absenSiswaData['counts']['hadir'] }}</span>
                    </div>
                    <div class="bg-sky-50 border border-sky-200 p-3.5 rounded-xl text-center">
                        <span class="text-[10px] font-black uppercase tracking-wider text-sky-700 block">Sakit</span>
                        <span class="text-xl font-black text-sky-900">{{ $absenSiswaData['counts']['sakit'] }}</span>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 p-3.5 rounded-xl text-center">
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 block">Izin</span>
                        <span class="text-xl font-black text-amber-900">{{ $absenSiswaData['counts']['izin'] }}</span>
                    </div>
                    <div class="bg-rose-50 border border-rose-200 p-3.5 rounded-xl text-center">
                        <span class="text-[10px] font-black uppercase tracking-wider text-rose-700 block">Alpa</span>
                        <span class="text-xl font-black text-rose-900">{{ $absenSiswaData['counts']['alpa'] }}</span>
                    </div>
                    <div class="bg-stone-100 border border-stone-200 p-3.5 rounded-xl text-center col-span-2 sm:col-span-1">
                        <span class="text-[10px] font-black uppercase tracking-wider text-stone-500 block">Belum Diabsen</span>
                        <span class="text-xl font-black text-stone-700">{{ $absenSiswaData['counts']['belum'] }}</span>
                    </div>
                </div>

                <!-- Secondary Filter: Rombel Kelas & Search Siswa -->
                <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row items-center gap-3">
                    <div class="w-full sm:w-48">
                        <select wire:model.live="absenKelasId" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                            <option value="">Semua Rombel</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full sm:flex-1 relative">
                        <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3.5 top-2.5" />
                        <input type="text" wire:model.live.debounce.300ms="searchSiswaAbsen" placeholder="Cari santri berdasarkan nama atau NIS..." class="w-full rounded-xl border border-stone-200 bg-stone-50 pl-10 pr-3 py-2 text-xs font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Absensi Siswa Table -->
                <div class="bg-white border border-stone-200 rounded-2xl shadow-xs overflow-hidden">
                    <x-table loadingTarget="selectedTanggal, absenKelasId, searchSiswaAbsen">
                        <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                            <tr>
                                <x-table.th align="center" class="w-12">No</x-table.th>
                                <x-table.th class="w-64">Nama Santri</x-table.th>
                                <x-table.th class="w-28">Kelas</x-table.th>
                                <x-table.th align="center" class="w-32">Status Presensi</x-table.th>
                                <x-table.th>Catatan / Keterangan</x-table.th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @forelse ($absenSiswaData['list'] as $idx => $item)
                                <tr class="hover:bg-stone-50 transition">
                                    <td class="p-3 text-center text-xs font-bold text-stone-400 border-r border-stone-200">{{ $idx + 1 }}</td>
                                    <td class="p-3 border-r border-stone-200 text-xs font-bold text-stone-900">
                                        {{ $item['siswa']->user->nama }}
                                        <div class="text-[10px] text-stone-400 font-semibold mt-0.5">NIS: {{ $item['siswa']->nis }}</div>
                                    </td>
                                    <td class="p-3 border-r border-stone-200 text-xs font-semibold text-stone-700">
                                        {{ $item['siswa']->kelas->nama_kelas ?? '-' }}
                                    </td>
                                    <td class="p-3 text-center border-r border-stone-200 text-xs">
                                        @php
                                            $badgeVariant = match($item['status']) {
                                                'hadir' => 'emerald',
                                                'sakit' => 'sky',
                                                'izin' => 'amber',
                                                'alpa' => 'rose',
                                                default => 'stone',
                                            };
                                            $label = match($item['status']) {
                                                'hadir' => 'HADIR',
                                                'sakit' => 'SAKIT',
                                                'izin' => 'IZIN',
                                                'alpa' => 'ALPA',
                                                default => 'BELUM DIABSEN',
                                            };
                                        @endphp
                                        <x-badge :variant="$badgeVariant" size="xs">{{ $label }}</x-badge>
                                    </td>
                                    <td class="p-3 text-xs text-stone-600 font-medium italic">
                                        {{ $item['catatan'] }}
                                    </td>
                                </tr>
                            @empty
                                <x-table.empty :colspan="5" title="Tidak ada data presensi santri" message="Tidak ada santri yang cocok dengan filter pencarian ini." />
                            @endforelse
                        </tbody>
                    </x-table>
                </div>

            <!-- Sub-tab 2: Absensi Guru -->
            @else
                <!-- Status Metrics Banner Guru -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-emerald-50 border border-emerald-200 p-3.5 rounded-xl text-center">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 block">Hadir Tepat Waktu</span>
                        <span class="text-xl font-black text-emerald-900">{{ $absenGuruData['counts']['hadir'] }}</span>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 p-3.5 rounded-xl text-center">
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 block">Terlambat</span>
                        <span class="text-xl font-black text-amber-900">{{ $absenGuruData['counts']['terlambat'] }}</span>
                    </div>
                    <div class="bg-sky-50 border border-sky-200 p-3.5 rounded-xl text-center">
                        <span class="text-[10px] font-black uppercase tracking-wider text-sky-700 block">Izin / Sakit</span>
                        <span class="text-xl font-black text-sky-900">{{ $absenGuruData['counts']['izin'] + $absenGuruData['counts']['sakit'] }}</span>
                    </div>
                    <div class="bg-rose-50 border border-rose-200 p-3.5 rounded-xl text-center">
                        <span class="text-[10px] font-black uppercase tracking-wider text-rose-700 block">Alpa / Belum Absen</span>
                        <span class="text-xl font-black text-rose-900">{{ $absenGuruData['counts']['alpa'] + $absenGuruData['counts']['belum'] }}</span>
                    </div>
                </div>

                <!-- Search Guru -->
                <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs">
                    <div class="relative">
                        <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3.5 top-2.5" />
                        <input type="text" wire:model.live.debounce.300ms="searchGuruAbsen" placeholder="Cari ustadz / guru berdasarkan nama atau NIP..." class="w-full rounded-xl border border-stone-200 bg-stone-50 pl-10 pr-3 py-2 text-xs font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Absensi Guru Table -->
                <div class="bg-white border border-stone-200 rounded-2xl shadow-xs overflow-hidden">
                    <x-table loadingTarget="selectedTanggal, searchGuruAbsen">
                        <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                            <tr>
                                <x-table.th align="center" class="w-12">No</x-table.th>
                                <x-table.th class="w-64">Nama Guru / Ustadz</x-table.th>
                                <x-table.th align="center" class="w-28">Waktu Masuk</x-table.th>
                                <x-table.th align="center" class="w-28">Waktu Pulang</x-table.th>
                                <x-table.th align="center" class="w-32">Status</x-table.th>
                                <x-table.th>Catatan / Keterangan</x-table.th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @forelse ($absenGuruData['list'] as $idx => $item)
                                <tr class="hover:bg-stone-50 transition">
                                    <td class="p-3 text-center text-xs font-bold text-stone-400 border-r border-stone-200">{{ $idx + 1 }}</td>
                                    <td class="p-3 border-r border-stone-200 text-xs font-bold text-stone-900">
                                        {{ $item['guru']->user->nama }}
                                        <div class="text-[10px] text-stone-400 font-semibold mt-0.5">NIP: {{ $item['guru']->nip ?? '-' }}</div>
                                    </td>
                                    <td class="p-3 text-center border-r border-stone-200 text-xs font-bold text-stone-700">
                                        {{ $item['waktu_datang'] }}
                                    </td>
                                    <td class="p-3 text-center border-r border-stone-200 text-xs font-bold text-stone-700">
                                        {{ $item['waktu_pulang'] }}
                                    </td>
                                    <td class="p-3 text-center border-r border-stone-200 text-xs">
                                        @php
                                            $badgeVariant = match($item['status']) {
                                                'hadir' => 'emerald',
                                                'terlambat' => 'amber',
                                                'sakit', 'izin' => 'sky',
                                                'alpa' => 'rose',
                                                default => 'stone',
                                            };
                                            $label = match($item['status']) {
                                                'hadir' => 'HADIR',
                                                'terlambat' => 'TERLAMBAT',
                                                'sakit' => 'SAKIT',
                                                'izin' => 'IZIN',
                                                'alpa' => 'ALPA',
                                                default => 'BELUM HADIR',
                                            };
                                        @endphp
                                        <x-badge :variant="$badgeVariant" size="xs">{{ $label }}</x-badge>
                                    </td>
                                    <td class="p-3 text-xs text-stone-600 font-medium italic">
                                        {{ $item['catatan'] }}
                                    </td>
                                </tr>
                            @empty
                                <x-table.empty :colspan="6" title="Tidak ada data presensi guru" message="Tidak ada guru yang cocok dengan filter pencarian ini." />
                            @endforelse
                        </tbody>
                    </x-table>
                </div>
            @endif
        </div>
    @endif
</div>
