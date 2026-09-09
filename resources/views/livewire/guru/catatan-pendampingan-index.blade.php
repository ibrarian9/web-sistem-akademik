<div class="space-y-6">
    <!-- HERO / PAGE HEADER -->
    <x-page-header 
        title="Catatan Pendampingan Siswa Berkebutuhan Khusus" 
        subtitle="Modul terdedikasi Guru Pendamping untuk mendokumentasikan observasi berkala, standarisasi capaian kualitatif (BB, MB, BSH, BSB), rekapitulasi capaian per aspek, serta penerbitan laporan resmi."
        badge="GURU PENDAMPING & ABK"
        icon="clipboard-list"
    >
        <x-slot:actions>
            @if (!auth()->user()->isSuperAdmin2())
                <x-button type="button" variant="primary" size="md" icon="plus" wire:click="openCreateModal">
                    Tambah Catatan Baru
                </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <!-- TAB SELECTOR NAVIGATION -->
    <div class="flex items-center justify-between gap-4 border-b border-stone-200 pb-3 flex-wrap">
        <div class="flex items-center p-1 bg-stone-100 border border-stone-200 rounded-xl overflow-x-auto shadow-2xs">
            <button 
                type="button" 
                wire:click="selectTab('daftar')" 
                class="px-4 py-2 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-2 {{ $tab === 'daftar' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}"
            >
                <x-lucide-clipboard-list class="w-4 h-4 {{ $tab === 'daftar' ? 'text-emerald-700' : 'text-stone-400' }}" />
                <span>Jurnal Observasi Berkala</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $tab === 'daftar' ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-200 text-stone-600' }}">
                    {{ $catatans->total() }}
                </span>
            </button>

            <button 
                type="button" 
                wire:click="selectTab('rekap')" 
                class="px-4 py-2 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-2 {{ $tab === 'rekap' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}"
            >
                <x-lucide-bar-chart-2 class="w-4 h-4 {{ $tab === 'rekap' ? 'text-emerald-700' : 'text-stone-400' }}" />
                <span>Rekapitulasi Perkembangan</span>
            </button>

            <button 
                type="button" 
                wire:click="selectTab('pratinjau')" 
                class="px-4 py-2 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-2 {{ $tab === 'pratinjau' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}"
            >
                <x-lucide-printer class="w-4 h-4 {{ $tab === 'pratinjau' ? 'text-emerald-700' : 'text-stone-400' }}" />
                <span>Pratinjau & Cetak Laporan</span>
            </button>
        </div>

        @if ($tab === 'rekap' || $tab === 'pratinjau')
            <!-- Student & Period Selector for Rekap / Pratinjau -->
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <label class="text-xs font-bold text-stone-500 uppercase tracking-wider">Siswa:</label>
                    <select wire:model.live="selectedSiswaId" class="px-3 py-1.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        @foreach ($allStudents as $s)
                            <option value="{{ $s->id }}">
                                {{ $s->user->nama ?? 'Siswa' }} ({{ $s->kelas->nama_kelas ?? 'Tanpa Kelas' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-1.5">
                    <label class="text-xs font-bold text-stone-500 uppercase tracking-wider">Periode:</label>
                    <select wire:model.live="rekapPeriode" class="px-3 py-1.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="tengah_semester">Tengah Semester</option>
                        <option value="akhir_semester">Akhir Semester</option>
                        <option value="semua">Semua Periode</option>
                    </select>
                </div>
            </div>
        @endif
    </div>

    <!-- FLASH MESSAGES -->
    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif
    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 1: JURNAL OBSERVASI BERKALA                                           -->
    <!-- ========================================================================= -->
    @if ($tab === 'daftar')
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
            <!-- Filter Toolbar -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="w-full lg:max-w-md">
                    <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari siswa, NIS, catatan, atau guru..." />
                </div>

                <div class="flex items-center gap-2.5 flex-wrap">
                    <!-- Filter Kelas -->
                    <select wire:model.live="filterKelasId" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Aspek -->
                    <select wire:model.live="filterAspek" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">Semua Aspek</option>
                        @foreach ($aspekList as $asp)
                            <option value="{{ $asp }}">{{ $asp }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Hasil Kualitatif -->
                    <select wire:model.live="filterHasil" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">Semua Capaian</option>
                        <option value="BB">Belum Berkembang (BB)</option>
                        <option value="MB">Mulai Berkembang (MB)</option>
                        <option value="BSH">Sesuai Harapan (BSH)</option>
                        <option value="BSB">Sangat Baik (BSB)</option>
                    </select>

                    <!-- Filter Periode -->
                    <select wire:model.live="filterPeriode" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="semua">Semua Periode</option>
                        <option value="tengah_semester">Tengah Semester</option>
                        <option value="akhir_semester">Akhir Semester</option>
                    </select>

                    <x-button type="button" variant="secondary" size="sm" icon="rotate-ccw" wire:click="resetFilters" title="Bersihkan Filter">
                        Reset
                    </x-button>
                </div>
            </div>

            <!-- Table of Observation Notes -->
            <x-table loadingTarget="search, filterKelasId, filterAspek, filterHasil, filterPeriode, page">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <x-table.th class="w-28">Tanggal</x-table.th>
                        <x-table.th class="min-w-[180px]">Peserta Didik</x-table.th>
                        <x-table.th class="w-32 text-center">Periode</x-table.th>
                        <x-table.th class="w-40">Aspek Pengamatan</x-table.th>
                        <x-table.th class="w-36 text-center">Capaian</x-table.th>
                        <x-table.th class="min-w-[220px]">Deskripsi Catatan Pengamatan</x-table.th>
                        <x-table.th class="min-w-[180px]">Rekomendasi / Tindak Lanjut</x-table.th>
                        <x-table.th class="w-36">Guru Pendamping</x-table.th>
                        <x-table.th class="w-24 text-center">Aksi</x-table.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($catatans as $item)
                        <tr class="hover:bg-stone-50/80 transition">
                            <td class="p-3.5 border-r border-stone-200">
                                <div class="font-bold text-xs text-stone-900">{{ $item->tanggal->translatedFormat('d M Y') }}</div>
                                <div class="text-[10px] text-stone-400 font-mono">{{ $item->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="p-3.5 border-r border-stone-200">
                                <div class="font-extrabold text-xs text-stone-900">{{ $item->siswa->user->nama ?? 'Siswa' }}</div>
                                <div class="text-[10px] text-stone-500 font-medium">
                                    NIS: {{ $item->siswa->nis ?: '-' }} | Kelas: {{ $item->siswa->kelas->nama_kelas ?? '-' }}
                                </div>
                            </td>
                            <td class="p-3.5 text-center border-r border-stone-200">
                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $item->periode === 'tengah_semester' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                    {{ $item->periode_label }}
                                </span>
                            </td>
                            <td class="p-3.5 font-bold text-stone-800 text-xs border-r border-stone-200">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span>
                                    <span>{{ $item->aspek }}</span>
                                </div>
                            </td>
                            <td class="p-3.5 text-center border-r border-stone-200">
                                @php
                                    $badgeColor = match($item->hasil_perkembangan) {
                                        'BB' => 'bg-rose-100 text-rose-800 border-rose-300',
                                        'MB' => 'bg-amber-100 text-amber-800 border-amber-300',
                                        'BSH' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                        'BSB' => 'bg-blue-100 text-blue-800 border-blue-300',
                                        default => 'bg-stone-100 text-stone-800 border-stone-300',
                                    };
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-black border {{ $badgeColor }}" title="{{ $item->hasil_perkembangan_label }}">
                                    {{ $item->hasil_perkembangan }}
                                </span>
                                <div class="text-[10px] font-semibold text-stone-500 mt-0.5">{{ $item->hasil_perkembangan_label }}</div>
                            </td>
                            <td class="p-3.5 text-xs text-stone-700 leading-relaxed font-medium border-r border-stone-200">
                                {{ $item->catatan }}
                            </td>
                            <td class="p-3.5 text-xs text-stone-600 leading-relaxed border-r border-stone-200 font-medium">
                                {{ $item->rekomendasi ?: '-' }}
                            </td>
                            <td class="p-3.5 text-xs font-bold text-stone-800 border-r border-stone-200">
                                {{ $item->guru->user->nama ?? 'Guru Pendamping' }}
                            </td>
                            <td class="p-3.5 text-center">
                                @if (!auth()->user()->isSuperAdmin2())
                                    <div class="flex items-center justify-center gap-1">
                                        <button type="button" wire:click="openEditModal({{ $item->id }})" class="p-1 text-stone-500 hover:text-emerald-600 rounded-lg hover:bg-emerald-50 transition" title="Edit Catatan">
                                            <x-lucide-pencil class="w-3.5 h-3.5" />
                                        </button>
                                        <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 text-stone-500 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Hapus Catatan">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                @else
                                    <span class="text-[10px] text-stone-400 italic">Lihat Saja</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-table.empty colspan="9" title="Belum ada catatan pendampingan" message="Gunakan tombol Tambah Catatan Baru di atas untuk mencatat observasi perkembangan siswa." />
                    @endforelse
                </tbody>
            </x-table>

            <!-- Pagination -->
            <div class="pt-2">
                {{ $catatans->links() }}
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 2: REKAPITULASI PERKEMBANGAN SISWA                                    -->
    <!-- ========================================================================= -->
    @if ($tab === 'rekap')
        <div class="space-y-6">
            @if ($selectedSiswa)
                <!-- Student Overview Card -->
                <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 border border-emerald-200 flex items-center justify-center text-emerald-800 font-black text-base shadow-2xs">
                            {{ substr($selectedSiswa->user->nama ?? 'S', 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wide">
                                    {{ $selectedSiswa->user->nama ?? 'Nama Siswa' }}
                                </h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    Anak Berkebutuhan Khusus (ABK)
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-stone-500 font-medium mt-1 flex-wrap">
                                <span>NIS: <b class="text-stone-800">{{ $selectedSiswa->nis ?: '-' }}</b></span>
                                <span>Kelas: <b class="text-stone-800">{{ $selectedSiswa->kelas->nama_kelas ?? '-' }}</b></span>
                                <span>Halaqah: <b class="text-stone-800">{{ $selectedSiswa->kelasTahfidz->nama_kelas ?? '-' }}</b></span>
                                <span>Guru Pendamping: <b class="text-emerald-700">{{ $selectedSiswa->shadowTeacher->user->nama ?? 'Belum Diplot' }}</b></span>
                            </div>
                        </div>
                    </div>

                    <!-- Periode Indicator & Switcher -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-stone-600">Filter Periode:</span>
                        <div class="flex items-center p-1 bg-stone-100 border border-stone-200 rounded-xl text-xs font-bold">
                            <button type="button" wire:click="$set('rekapPeriode', 'tengah_semester')" class="px-3 py-1 rounded-lg transition {{ $rekapPeriode === 'tengah_semester' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                                Tengah Semester
                            </button>
                            <button type="button" wire:click="$set('rekapPeriode', 'akhir_semester')" class="px-3 py-1 rounded-lg transition {{ $rekapPeriode === 'akhir_semester' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                                Akhir Semester
                            </button>
                            <button type="button" wire:click="$set('rekapPeriode', 'semua')" class="px-3 py-1 rounded-lg transition {{ $rekapPeriode === 'semua' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                                Semua
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Capaian Distribution Summary Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-2xs space-y-1">
                        <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Total Observasi</span>
                        <div class="text-2xl font-black text-stone-900">{{ $rekapData['total_observasi'] ?? 0 }}</div>
                        <span class="text-[10px] text-stone-400 font-medium">Sesi pengamatan</span>
                    </div>

                    <div class="bg-rose-50/60 border border-rose-200 rounded-2xl p-4 shadow-2xs space-y-1">
                        <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider">BB (Belum Berkembang)</span>
                        <div class="text-2xl font-black text-rose-800">{{ $rekapData['total_bb'] ?? 0 }}</div>
                        <span class="text-[10px] text-rose-600 font-semibold">Perlu bimbingan penuh</span>
                    </div>

                    <div class="bg-amber-50/60 border border-amber-200 rounded-2xl p-4 shadow-2xs space-y-1">
                        <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">MB (Mulai Berkembang)</span>
                        <div class="text-2xl font-black text-amber-800">{{ $rekapData['total_mb'] ?? 0 }}</div>
                        <span class="text-[10px] text-amber-600 font-semibold">Mulai tampak kemandirian</span>
                    </div>

                    <div class="bg-emerald-50/60 border border-emerald-200 rounded-2xl p-4 shadow-2xs space-y-1">
                        <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">BSH (Sesuai Harapan)</span>
                        <div class="text-2xl font-black text-emerald-800">{{ $rekapData['total_bsh'] ?? 0 }}</div>
                        <span class="text-[10px] text-emerald-600 font-semibold">Konsisten & stabil</span>
                    </div>

                    <div class="bg-blue-50/60 border border-blue-200 rounded-2xl p-4 shadow-2xs space-y-1">
                        <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">BSB (Sangat Baik)</span>
                        <div class="text-2xl font-black text-blue-800">{{ $rekapData['total_bsb'] ?? 0 }}</div>
                        <span class="text-[10px] text-blue-600 font-semibold">Mandiri & inisiatif</span>
                    </div>
                </div>

                <!-- 7 Aspects Qualitative Progress Matrix -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-extrabold text-stone-800 uppercase tracking-wider flex items-center gap-2">
                            <x-lucide-award class="w-4 h-4 text-emerald-600" />
                            <span>Rekapitulasi Capaian Kualitatif 7 Aspek Pengamatan:</span>
                        </h4>
                        <span class="text-[11px] font-bold text-stone-400">Periode: {{ ucfirst(str_replace('_', ' ', $rekapPeriode)) }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($rekapData['aspek_breakdown'] as $aspekNama => $aData)
                            <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs flex flex-col justify-between space-y-4">
                                <div>
                                    <div class="flex items-start justify-between gap-2 border-b border-stone-100 pb-3">
                                        <div>
                                            <h5 class="text-xs font-black text-stone-900 uppercase tracking-wide">{{ $aspekNama }}</h5>
                                            <span class="text-[10px] text-stone-400 font-medium">{{ $aData['total_observasi'] }} kali diobservasi</span>
                                        </div>
                                        @if ($aData['capaian_terakhir'])
                                            @php
                                                $bColor = match($aData['capaian_terakhir']) {
                                                    'BB' => 'bg-rose-100 text-rose-800 border-rose-300',
                                                    'MB' => 'bg-amber-100 text-amber-800 border-amber-300',
                                                    'BSH' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                                    'BSB' => 'bg-blue-100 text-blue-800 border-blue-300',
                                                    default => 'bg-stone-100 text-stone-800 border-stone-300',
                                                };
                                            @endphp
                                            <div class="text-right">
                                                <span class="inline-block px-2.5 py-1 rounded-xl text-xs font-black border {{ $bColor }}">
                                                    {{ $aData['capaian_terakhir'] }}
                                                </span>
                                                <span class="text-[9px] font-bold text-stone-500 block mt-0.5">{{ $aData['capaian_label'] }}</span>
                                            </div>
                                        @else
                                            <span class="text-[11px] text-stone-400 italic font-medium">Belum diobservasi</span>
                                        @endif
                                    </div>

                                    <!-- Distribution pills -->
                                    <div class="grid grid-cols-4 gap-1.5 pt-3 text-center">
                                        <div class="bg-rose-50/80 rounded-lg p-1 text-[10px] font-bold text-rose-700">
                                            BB: <b class="font-black">{{ $aData['distribusi']['BB'] }}</b>
                                        </div>
                                        <div class="bg-amber-50/80 rounded-lg p-1 text-[10px] font-bold text-amber-700">
                                            MB: <b class="font-black">{{ $aData['distribusi']['MB'] }}</b>
                                        </div>
                                        <div class="bg-emerald-50/80 rounded-lg p-1 text-[10px] font-bold text-emerald-700">
                                            BSH: <b class="font-black">{{ $aData['distribusi']['BSH'] }}</b>
                                        </div>
                                        <div class="bg-blue-50/80 rounded-lg p-1 text-[10px] font-bold text-blue-700">
                                            BSB: <b class="font-black">{{ $aData['distribusi']['BSB'] }}</b>
                                        </div>
                                    </div>

                                    <!-- Latest notes preview -->
                                    <div class="pt-3">
                                        <span class="text-[10px] font-extrabold text-stone-500 uppercase tracking-wider block mb-1">Catatan Terakhir:</span>
                                        <p class="text-xs text-stone-700 leading-relaxed font-medium bg-stone-50 p-2.5 rounded-xl border border-stone-200">
                                            {{ $aData['catatan_terakhir'] ?: 'Belum ada catatan pada periode ini.' }}
                                        </p>
                                    </div>
                                </div>

                                @if ($aData['rekomendasi_terakhir'])
                                    <div class="pt-2 border-t border-stone-100">
                                        <span class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider block mb-0.5">Tindak Lanjut:</span>
                                        <p class="text-[11px] text-stone-600 italic">"{{ $aData['rekomendasi_terakhir'] }}"</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Consolidated Recommendations Box -->
                <div class="bg-emerald-50/50 border border-emerald-200 rounded-2xl p-6 shadow-xs space-y-3">
                    <h4 class="text-xs font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                        <x-lucide-lightbulb class="w-4 h-4 text-emerald-700" />
                        <span>Rangkuman Rekomendasi & Rencana Tindak Lanjut Guru Pendamping:</span>
                    </h4>
                    @if (!empty($rekapData['rekomendasi_list']))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach ($rekapData['rekomendasi_list'] as $rec)
                                <div class="bg-white p-3 rounded-xl border border-emerald-200 text-xs font-medium text-stone-800 shadow-2xs flex items-start gap-2">
                                    <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                                    <span>{{ $rec }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-stone-500 italic">Belum ada rekomendasi yang dicatat pada periode ini.</p>
                    @endif
                </div>
            @else
                <x-table.empty title="Pilih Siswa Terlebih Dahulu" message="Silakan pilih siswa pada dropdown di atas untuk menampilkan rekapitulasi perkembangan." />
            @endif
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 3: PRATINJAU & CETAK LAPORAN                                          -->
    <!-- ========================================================================= -->
    @if ($tab === 'pratinjau')
        <div class="space-y-6">
            @if ($selectedSiswa)
                <!-- Action Bar -->
                <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2 text-xs font-bold text-stone-700">
                        <x-lucide-printer class="w-4 h-4 text-emerald-600" />
                        <span>Pratinjau Lembar Laporan Pendampingan: <b class="text-stone-900">{{ $selectedSiswa->user->nama ?? 'Siswa' }}</b> ({{ ucfirst(str_replace('_', ' ', $rekapPeriode)) }})</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <a 
                            href="{{ route('pendampingan.cetak', ['siswaId' => $selectedSiswa->id, 'periode' => $rekapPeriode]) }}" 
                            target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm"
                        >
                            <x-lucide-download class="w-3.5 h-3.5" />
                            <span>Cetak / Unduh PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Live Report Document Frame -->
                <div class="max-w-4xl mx-auto bg-white border border-stone-300 rounded-3xl p-8 sm:p-12 shadow-md space-y-6 text-stone-900">
                    <!-- Kop Laporan -->
                    <div class="text-center border-b-2 border-emerald-800 pb-4 space-y-1">
                        <h2 class="text-base sm:text-lg font-black tracking-wide text-emerald-950 uppercase">
                            PONDOK PESANTREN & SEKOLAH ISLAM TERPADU
                        </h2>
                        <h3 class="text-xs sm:text-sm font-extrabold text-stone-800 uppercase tracking-wider">
                            LAPORAN CAPAIAN PERKEMBANGAN PENDAMPINGAN KHUSUS (INKLUSI)
                        </h3>
                        <p class="text-[11px] text-stone-500 font-medium">
                            Periode: {{ $rekapPeriode === 'tengah_semester' ? 'Tengah Semester (PTS)' : ($rekapPeriode === 'akhir_semester' ? 'Akhir Semester (PAS/PAT)' : 'Seluruh Periode') }}
                        </p>
                    </div>

                    <!-- Meta Data Siswa & Pendamping -->
                    <div class="grid grid-cols-2 gap-y-2 gap-x-8 text-xs bg-stone-50/60 p-4 rounded-xl border border-stone-200">
                        <div>
                            <span class="text-stone-500 font-bold block">Nama Peserta Didik:</span>
                            <span class="font-black text-stone-900 text-sm">{{ strtoupper($selectedSiswa->user->nama ?? '-') }}</span>
                        </div>
                        <div>
                            <span class="text-stone-500 font-bold block">Kelas / Rombel:</span>
                            <span class="font-extrabold text-stone-900">{{ $selectedSiswa->kelas->nama_kelas ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-stone-500 font-bold block">NIS / NISN:</span>
                            <span class="font-bold text-stone-800">{{ $selectedSiswa->nis ?: '-' }} / {{ $selectedSiswa->nisn ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-stone-500 font-bold block">Guru Pendamping Khusus:</span>
                            <span class="font-black text-emerald-800">{{ $selectedSiswa->shadowTeacher->user->nama ?? 'Guru Pendamping' }}</span>
                        </div>
                    </div>

                    <!-- Kriteria Capaian Legend -->
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-[11px] text-emerald-950 font-medium">
                        <b class="font-extrabold uppercase block mb-1">Skala Capaian Kualitatif:</b>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <span><b>BB</b> : Belum Berkembang</span>
                            <span><b>MB</b> : Mulai Berkembang</span>
                            <span><b>BSH</b> : Sesuai Harapan</span>
                            <span><b>BSB</b> : Sangat Baik</span>
                        </div>
                    </div>

                    <!-- Rekapitulasi 7 Aspek Tabel -->
                    <div class="border border-stone-300 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-stone-100 text-stone-900 font-extrabold uppercase border-b border-stone-300">
                                <tr>
                                    <th class="p-3 w-12 text-center border-r border-stone-300">No</th>
                                    <th class="p-3 w-48 border-r border-stone-300">Aspek Pengamatan</th>
                                    <th class="p-3 w-32 text-center border-r border-stone-300">Capaian</th>
                                    <th class="p-3">Deskripsi & Catatan Pengamatan Guru</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-300">
                                @php $n = 1; @endphp
                                @foreach ($rekapData['aspek_breakdown'] as $aName => $aRow)
                                    <tr>
                                        <td class="p-3 text-center font-bold border-r border-stone-300">{{ $n++ }}</td>
                                        <td class="p-3 font-extrabold text-stone-900 border-r border-stone-300">{{ $aName }}</td>
                                        <td class="p-3 text-center border-r border-stone-300">
                                            @if ($aRow['capaian_terakhir'])
                                                @php
                                                    $pill = match($aRow['capaian_terakhir']) {
                                                        'BB' => 'bg-rose-100 text-rose-800 border-rose-300',
                                                        'MB' => 'bg-amber-100 text-amber-800 border-amber-300',
                                                        'BSH' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                                        'BSB' => 'bg-blue-100 text-blue-800 border-blue-300',
                                                        default => 'bg-stone-100 text-stone-800',
                                                    };
                                                @endphp
                                                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-black border {{ $pill }}">
                                                    {{ $aRow['capaian_terakhir'] }}
                                                </span>
                                                <span class="text-[10px] text-stone-500 block mt-0.5">{{ $aRow['capaian_label'] }}</span>
                                            @else
                                                <span class="text-stone-400 italic">-</span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-stone-700 leading-relaxed">
                                            {{ $aRow['catatan_terakhir'] ?: 'Belum ada catatan pada periode ini.' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Rekomendasi Box -->
                    <div class="p-4 bg-stone-50 border border-stone-300 rounded-xl space-y-2 text-xs">
                        <b class="font-extrabold uppercase text-stone-900 block">Rekomendasi & Rencana Tindak Lanjut:</b>
                        @if (!empty($rekapData['rekomendasi_list']))
                            <ul class="list-disc pl-5 space-y-1 text-stone-700 font-medium">
                                @foreach ($rekapData['rekomendasi_list'] as $rec)
                                    <li>{{ $rec }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-stone-500 italic">Pertahankan pendampingan intensif bersama guru dan orang tua.</p>
                        @endif
                    </div>

                    <!-- Tanda Tangan Footer -->
                    <div class="grid grid-cols-3 gap-4 text-center text-xs pt-8 border-t border-stone-200">
                        <div>
                            <span>Orang Tua / Wali Siswa,</span>
                            <div class="h-16"></div>
                            <b class="border-t border-stone-400 pt-1 block">( ........................................ )</b>
                        </div>
                        <div>
                            <span>Guru Pendamping Khusus,</span>
                            <div class="h-16"></div>
                            <b class="border-t border-stone-400 pt-1 block text-emerald-900">{{ $selectedSiswa->shadowTeacher->user->nama ?? 'Guru Pendamping' }}</b>
                        </div>
                        <div>
                            <span>Kepala Sekolah,</span>
                            <div class="h-16"></div>
                            <b class="border-t border-stone-400 pt-1 block">Ustadz Pembina, M.Pd</b>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 1: FORM INPUT / EDIT CATATAN OBSERVASI PENDAMPINGAN                -->
    <!-- ========================================================================= -->
    @if ($showFormModal)
        <x-floating-card 
            title="{{ $editingId ? 'Sunting Catatan Pendampingan' : 'Tambah Catatan Pendampingan Baru' }}" 
            badge="GURU PENDAMPING"
            showClose="true"
            onClose="closeFormModal"
        >
            <form wire:submit.prevent="saveRecord" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tanggal Observasi -->
                    <x-input 
                        type="date" 
                        label="Tanggal Observasi" 
                        name="form_tanggal" 
                        wire:model="form_tanggal" 
                        required 
                    />

                    <!-- Periode Evaluasi -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Periode Evaluasi <span class="text-rose-600">*</span></label>
                        <select wire:model="form_periode" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                            <option value="tengah_semester">Tengah Semester (PTS)</option>
                            <option value="akhir_semester">Akhir Semester (PAS / PAT)</option>
                        </select>
                        @error('form_periode') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Pilih Siswa Berkebutuhan Khusus -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                        Peserta Didik Berkebutuhan Khusus <span class="text-rose-600">*</span>
                    </label>
                    <select wire:model="form_siswa_id" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">-- Pilih Siswa Dampingan --</option>
                        @foreach ($allStudents as $st)
                            <option value="{{ $st->id }}">
                                {{ $st->user->nama ?? 'Siswa' }} | Kelas {{ $st->kelas->nama_kelas ?? '-' }} (NIS: {{ $st->nis ?: '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('form_siswa_id') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Aspek Pengamatan (7 Pilihan Wajib) -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                        Aspek Pengamatan Perkembangan <span class="text-rose-600">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach ($aspekList as $aspItem)
                            <label class="cursor-pointer flex items-center gap-2 p-2.5 rounded-xl border text-xs font-bold transition {{ $form_aspek === $aspItem ? 'bg-emerald-50 border-emerald-500 text-emerald-900 ring-2 ring-emerald-500/20' : 'bg-stone-50 border-stone-200 text-stone-700 hover:bg-stone-100' }}">
                                <input type="radio" wire:model.live="form_aspek" value="{{ $aspItem }}" class="sr-only" />
                                <span class="w-2 h-2 rounded-full {{ $form_aspek === $aspItem ? 'bg-emerald-600' : 'bg-stone-300' }}"></span>
                                <span class="truncate">{{ $aspItem }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('form_aspek') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Standarisasi Penilaian Kualitatif (BB, MB, BSH, BSB) - ZERO NUMBER INPUT -->
                <div class="space-y-2 p-4 bg-stone-50 border border-stone-200 rounded-2xl">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-extrabold text-stone-800 uppercase tracking-wider">
                            Hasil Capaian Perkembangan Kualitatif <span class="text-rose-600">*</span>
                        </label>
                        <span class="text-[10px] font-bold text-emerald-800 uppercase bg-emerald-100 px-2 py-0.5 rounded-md">Standarisasi Bebas Angka</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <!-- BB -->
                        <label class="cursor-pointer p-3 rounded-xl border transition flex items-start gap-3 {{ $form_hasil_perkembangan === 'BB' ? 'bg-rose-50 border-rose-500 ring-2 ring-rose-500/20 text-rose-950' : 'bg-white border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                            <input type="radio" wire:model.live="form_hasil_perkembangan" value="BB" class="sr-only" />
                            <span class="px-2 py-0.5 rounded-md font-black text-xs {{ $form_hasil_perkembangan === 'BB' ? 'bg-rose-600 text-white' : 'bg-rose-100 text-rose-800' }}">BB</span>
                            <div>
                                <span class="text-xs font-bold block">Belum Berkembang</span>
                                <span class="text-[10px] text-stone-500 leading-tight block">Memerlukan bantuan dan bimbingan penuh dari pendamping.</span>
                            </div>
                        </label>

                        <!-- MB -->
                        <label class="cursor-pointer p-3 rounded-xl border transition flex items-start gap-3 {{ $form_hasil_perkembangan === 'MB' ? 'bg-amber-50 border-amber-500 ring-2 ring-amber-500/20 text-amber-950' : 'bg-white border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                            <input type="radio" wire:model.live="form_hasil_perkembangan" value="MB" class="sr-only" />
                            <span class="px-2 py-0.5 rounded-md font-black text-xs {{ $form_hasil_perkembangan === 'MB' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-800' }}">MB</span>
                            <div>
                                <span class="text-xs font-bold block">Mulai Berkembang</span>
                                <span class="text-[10px] text-stone-500 leading-tight block">Mulai tampak inisiatif namun masih perlu diingatkan/diarahkan.</span>
                            </div>
                        </label>

                        <!-- BSH -->
                        <label class="cursor-pointer p-3 rounded-xl border transition flex items-start gap-3 {{ $form_hasil_perkembangan === 'BSH' ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20 text-emerald-950' : 'bg-white border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                            <input type="radio" wire:model.live="form_hasil_perkembangan" value="BSH" class="sr-only" />
                            <span class="px-2 py-0.5 rounded-md font-black text-xs {{ $form_hasil_perkembangan === 'BSH' ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-800' }}">BSH</span>
                            <div>
                                <span class="text-xs font-bold block">Berkembang Sesuai Harapan</span>
                                <span class="text-[10px] text-stone-500 leading-tight block">Menunjukkan kemampuan secara konsisten sesuai target.</span>
                            </div>
                        </label>

                        <!-- BSB -->
                        <label class="cursor-pointer p-3 rounded-xl border transition flex items-start gap-3 {{ $form_hasil_perkembangan === 'BSB' ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-500/20 text-blue-950' : 'bg-white border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                            <input type="radio" wire:model.live="form_hasil_perkembangan" value="BSB" class="sr-only" />
                            <span class="px-2 py-0.5 rounded-md font-black text-xs {{ $form_hasil_perkembangan === 'BSB' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800' }}">BSB</span>
                            <div>
                                <span class="text-xs font-bold block">Berkembang Sangat Baik</span>
                                <span class="text-[10px] text-stone-500 leading-tight block">Menguasai secara mandiri dan dapat memandu teman sebaya.</span>
                            </div>
                        </label>
                    </div>
                    @error('form_hasil_perkembangan') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Deskripsi / Catatan Pengamatan -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                        Deskripsi / Catatan Pengamatan Guru <span class="text-rose-600">*</span>
                    </label>
                    <textarea wire:model="form_catatan" rows="3" placeholder="Tuliskan deskripsi objektif perilaku, respon belajar, atau kemajuan ananda pada aspek ini..." class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs"></textarea>
                    @error('form_catatan') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Rekomendasi / Tindak Lanjut -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                        Rekomendasi / Rencana Tindak Lanjut (Opsional)
                    </label>
                    <textarea wire:model="form_rekomendasi" rows="2" placeholder="Contoh: Stimulasi motorik halus melalui latihan menggunting kertas, atau pendampingan interaksi saat jam istirahat..." class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs"></textarea>
                    @error('form_rekomendasi') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                    <x-button type="button" variant="secondary" size="sm" wire:click="closeFormModal">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm" icon="check" loadingTarget="saveRecord">
                        Simpan Catatan
                    </x-button>
                </div>
            </form>
        </x-floating-card>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL KONFIRMASI HAPUS                                                    -->
    <!-- ========================================================================= -->
    @if ($deletingId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-xs">
            <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-2xl max-w-sm w-full space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <x-lucide-alert-triangle class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-stone-900">Hapus Catatan Pengamatan?</h3>
                        <p class="text-xs text-stone-500 font-medium">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-100">
                    <x-button type="button" variant="secondary" size="sm" wire:click="cancelDelete">
                        Batal
                    </x-button>
                    <x-button type="button" variant="danger-solid" size="sm" icon="trash-2" wire:click="deleteRecord({{ $deletingId }})">
                        Hapus Catatan
                    </x-button>
                </div>
            </div>
        </div>
    @endif
</div>
