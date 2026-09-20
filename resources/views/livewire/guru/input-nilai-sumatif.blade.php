<div 
    x-data="{
        isDirty: false,
        init() {
            window.addEventListener('beforeunload', (e) => {
                if (this.isDirty) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
            Livewire.on('scores-saved', () => {
                this.isDirty = false;
            });
        },
        markDirty(el) {
            this.isDirty = true;
            if (el && Number(el.value) > 100) {
                el.classList.add('ring-2', 'ring-rose-500', 'border-rose-500', 'bg-rose-50');
            } else if (el) {
                el.classList.remove('ring-2', 'ring-rose-500', 'border-rose-500', 'bg-rose-50');
            }
        },
        navigateCell(event, rowIdx, colIdx) {
            let nextRow = rowIdx;
            if (event.key === 'Enter' || event.key === 'ArrowDown') {
                nextRow = rowIdx + 1;
            } else if (event.key === 'ArrowUp') {
                nextRow = Math.max(0, rowIdx - 1);
            }
            let targetInput = document.querySelector(`input[data-row='${nextRow}'][data-col='${colIdx}']`);
            if (targetInput) {
                targetInput.focus();
                targetInput.select();
            }
        }
    }" 
    class="space-y-6 font-sans pb-24 md:pb-16"
>
    <!-- Quick Module Switcher Navigation -->
    <x-guru-module-switcher active="sumatif" />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Matriks Penilaian Sumatif TP & SAS"
        :steps="[
            ['title' => 'Pilih Kelas & Mapel', 'desc' => 'Tentukan Kelas, Mata Pelajaran, Bab (opsional), dan Semester aktif yang akan dinilai.'],
            ['title' => 'Input Nilai Cepat (Excel Style)', 'desc' => 'Isikan nilai TP 1, TP 2, ... dan Nilai SAS. Gunakan panah keyboard (↑ / ↓ / Enter) untuk berpindah sel.'],
            ['title' => 'Simpan & Kalkulasi Rapor', 'desc' => 'Klik Simpan & Hitung Rapor untuk menyimpan seluruh matriks nilai secara instan ke dalam database.']
        ]"
        notes="Nilai Rapor dihitung otomatis dari rata-rata nilai TP dan Nilai SAS. Hasil nilai akan langsung terakumulasi pada lembar Rapor Siswa."
    />

    <!-- Header Section -->
    <x-page-header 
        title="Form Matriks Nilai Sumatif TP & SAS" 
        subtitle="Input nilai capaian Tujuan Pembelajaran dan Sumatif Akhir Semester secara cepat seperti spreadsheet Excel."
        badge="NILAI SUMATIF"
        badgeVariant="emerald"
        icon="table"
    >
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <span x-show="isDirty" x-cloak class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 border border-amber-300 text-amber-900 rounded-xl text-xs font-bold animate-pulse shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Nilai belum disimpan</span>
                </span>
                <button 
                    type="button" 
                    id="tour-sumatif-help-btn"
                    onclick="runSumatifTour()"
                    class="px-3.5 py-2.5 bg-emerald-50 hover:bg-emerald-100 active:bg-emerald-200 text-emerald-800 border border-emerald-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer flex-1 sm:flex-none"
                    title="Buka panduan langkah demi langkah cara mengisi nilai sumatif"
                >
                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Panduan Interaktif</span>
                </button>
                <x-button variant="secondary" size="md" icon="layers" href="{{ route('guru.kurikulum-merdeka') }}" class="flex-1 sm:flex-none">
                    Setup Bab & TP
                </x-button>
                <div id="tour-sumatif-simpan" class="flex-1 sm:flex-none">
                    <x-button variant="primary" size="md" icon="check" wire:click="saveAllScores" loadingTarget="saveAllScores" class="w-full">
                        Simpan & Hitung Rapor
                    </x-button>
                </div>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Filter Bar Card -->
    <div id="tour-sumatif-filter" class="bg-white border border-stone-200 p-4 sm:p-6 rounded-2xl shadow-xs space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Kelas</label>
                <select wire:model.live="kelas_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl text-stone-900 px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:bg-white shadow-2xs">
                    @foreach($kelases as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Mata Pelajaran</label>
                <select wire:model.live="mapel_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl text-stone-900 px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:bg-white shadow-2xs">
                    @foreach($mapels as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Filter Bab (Lingkup Materi)</label>
                <select wire:model.live="lingkup_materi_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl text-stone-900 px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:bg-white shadow-2xs">
                    <option value="">-- Semua Bab (Tampilkan Seluruh TP) --</option>
                    @foreach($lingkupMateris as $lm)
                        <option value="{{ $lm->id }}">Bab {{ $lm->urutan }}: {{ $lm->nama_lingkup_materi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Semester</label>
                <select wire:model.live="semester_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl text-stone-900 px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:bg-white shadow-2xs">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->tahunAjaran->nama ?? '' }} - {{ ucfirst($sem->semester) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Filter Livewire Loading Bar Indicator -->
        <div wire:loading.delay wire:target="kelas_id, mapel_id, lingkup_materi_id, semester_id" class="w-full">
            <x-loading-state type="bar" target="kelas_id, mapel_id, lingkup_materi_id, semester_id" />
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 p-4 rounded-2xl text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <span class="px-2.5 py-0.5 bg-emerald-200 text-emerald-900 rounded-lg font-black text-[10px]">Tersimpan</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-50 border border-rose-300 text-rose-800 p-4 rounded-2xl text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <span class="px-2.5 py-0.5 bg-rose-200 text-rose-900 rounded-lg font-black text-[10px]">Perhatian</span>
        </div>
    @endif

    <!-- Matrix Table Container -->
    <div id="tour-sumatif-tabel" class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-xs">
        
        <!-- Table Header Bar -->
        <div class="p-3.5 sm:p-4 bg-stone-50 border-b border-stone-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <span class="text-xs font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                Matriks Penilaian Sumatif TP, SAS, & Hasil Rapor Akhir
            </span>
            <span class="text-[11px] text-stone-500 font-semibold">Total {{ count($siswas) }} Siswa Terdaftar</span>
        </div>

        <!-- Mobile Horizontal Scroll Hint -->
        <div class="flex items-center gap-1.5 px-4 py-2 bg-emerald-50/70 border-b border-emerald-100 text-[11px] font-bold text-emerald-800 md:hidden">
            <svg class="w-3.5 h-3.5 text-emerald-600 animate-pulse shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            <span>Geser tabel ke samping untuk mengisi nilai TP & SAS</span>
        </div>

        <!-- Horizontal Scroll Table Wrapper -->
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-separate border-spacing-0 text-xs text-stone-800">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider select-none">
                    <tr>
                        <!-- No Column: Sticky on desktop, normal hidden on small mobile to maximize space -->
                        <th class="hidden md:table-cell w-12 min-w-[48px] p-3 text-center sticky left-0 bg-emerald-800 text-white font-extrabold text-xs z-20 border-b border-r border-emerald-700">
                            No
                        </th>
                        
                        <!-- Nama Siswa: Sticky on left for both mobile & desktop -->
                        <th class="sticky left-0 md:left-12 z-20 min-w-[125px] max-w-[140px] md:min-w-[200px] md:max-w-none p-2.5 md:p-3 bg-emerald-800 text-white font-extrabold text-xs border-b border-r border-emerald-700 shadow-[3px_0_5px_-2px_rgba(0,0,0,0.25)] md:shadow-none">
                            Nama Siswa
                        </th>
                        
                        <!-- Dynamic TP Columns with Bab.TP Prefix -->
                        @foreach($tps as $tpIdx => $tp)
                            <th class="min-w-[80px] sm:min-w-[95px] p-2 text-center bg-emerald-800 text-white font-extrabold border-b border-r border-emerald-700" title="{{ $tp->deskripsi_tp }}">
                                <span class="block text-white font-black text-xs">B{{ $tp->lingkupMateri->urutan ?? '1' }}.TP{{ $tp->urutan }}</span>
                                <span class="text-[10px] text-emerald-200 font-normal block truncate max-w-[75px] sm:max-w-[100px] mx-auto">{{ $tp->deskripsi_tp }}</span>
                            </th>
                        @endforeach
                        
                        <!-- Rata-rata TP Column -->
                        <th class="w-20 sm:w-24 min-w-[80px] p-2 text-center bg-emerald-900/90 text-white font-black text-xs uppercase border-b border-r border-emerald-950" title="Rata-rata Nilai TP">
                            Rata² TP
                        </th>

                        <!-- Nilai SAS Column -->
                        <th class="w-20 sm:w-24 min-w-[80px] p-2 text-center bg-emerald-900 text-white font-black text-xs uppercase border-b border-r border-emerald-950">
                            Nilai SAS
                        </th>
                        
                        <!-- Nilai Rapor Column -->
                        <th class="w-24 sm:w-28 min-w-[85px] p-2 text-center bg-emerald-950 text-white font-black text-xs uppercase border-b border-emerald-950">
                            Nilai Rapor
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($siswas as $index => $s)
                        @php
                            $tpScores = array_filter(array_values($nilaiTpMatrix[$s->id] ?? []), function($v) {
                                return $v !== '' && $v !== null && is_numeric($v);
                            });
                            $avgTp = count($tpScores) > 0 ? array_sum($tpScores) / count($tpScores) : null;

                            $sasVal = $nilaiSasMatrix[$s->id] ?? '';
                            $sasNum = (is_numeric($sasVal) && $sasVal !== '') ? (float)$sasVal : null;
                            
                            if ($avgTp !== null && $sasNum !== null) {
                                $nilaiRapor = ($avgTp + $sasNum) / 2;
                            } elseif ($avgTp !== null) {
                                $nilaiRapor = $avgTp;
                            } elseif ($sasNum !== null) {
                                $nilaiRapor = $sasNum;
                            } else {
                                $nilaiRapor = null;
                            }
                            $nilaiRaporFormatted = $nilaiRapor !== null ? round($nilaiRapor, 2) : null;
                        @endphp
                        <tr class="hover:bg-stone-50 transition group">
                            <!-- No (Desktop Only) -->
                            <td class="hidden md:table-cell w-12 min-w-[48px] p-3 text-center sticky left-0 bg-white group-hover:bg-stone-50 font-bold text-stone-500 border-b border-r border-stone-200 text-xs z-10 transition">
                                {{ $index + 1 }}
                            </td>

                            <!-- Nama Siswa (Sticky Left with Shadow on Mobile) -->
                            <td class="p-2.5 md:p-3 sticky left-0 md:left-12 bg-white group-hover:bg-stone-50 font-bold text-stone-900 border-b border-r-2 md:border-r border-stone-200 text-xs z-10 shadow-[3px_0_6px_-2px_rgba(0,0,0,0.12)] md:shadow-none min-w-[125px] max-w-[140px] md:min-w-[200px] md:max-w-none transition">
                                <div class="line-clamp-2 text-xs font-extrabold text-stone-900 leading-tight">
                                    {{ $s->user->nama ?? $s->nama_panggilan }}
                                </div>
                                <span class="block text-[10px] text-stone-500 font-mono font-normal mt-0.5 truncate">NISN: {{ $s->nisn }}</span>
                            </td>

                            <!-- Dynamic TP Inputs -->
                            @foreach($tps as $colIdx => $tp)
                                <td class="p-1.5 sm:p-2 text-center border-b border-r border-stone-200">
                                    <input 
                                        type="number" 
                                        inputmode="decimal"
                                        step="0.01" 
                                        min="0" 
                                        max="100" 
                                        data-row="{{ $index }}"
                                        data-col="{{ $colIdx }}"
                                        @input="markDirty($el)"
                                        @keydown.enter.prevent="navigateCell($event, {{ $index }}, {{ $colIdx }})"
                                        @keydown.arrow-down.prevent="navigateCell($event, {{ $index }}, {{ $colIdx }})"
                                        @keydown.arrow-up.prevent="navigateCell($event, {{ $index }}, {{ $colIdx }})"
                                        wire:model.defer="nilaiTpMatrix.{{ $s->id }}.{{ $tp->id }}"
                                        class="w-14 sm:w-16 h-8 sm:h-9 bg-white border border-stone-300 rounded-lg text-center text-stone-900 font-black text-xs py-1 focus:ring-2 focus:ring-emerald-500 focus:bg-emerald-50 focus:border-emerald-500 shadow-2xs transition"
                                        placeholder="0"
                                    >
                                </td>
                            @endforeach

                            <!-- Rata-rata TP -->
                            <td class="p-2 text-center bg-emerald-50/40 font-bold text-stone-700 text-xs border-b border-r border-stone-200">
                                {{ $avgTp !== null ? number_format($avgTp, 1) : '-' }}
                            </td>

                            <!-- Input SAS -->
                            <td class="p-1.5 sm:p-2 text-center bg-cyan-50/30 group-hover:bg-cyan-50/60 border-b border-r border-stone-200 transition">
                                <input 
                                    type="number" 
                                    inputmode="decimal"
                                    step="0.01" 
                                    min="0" 
                                    max="100" 
                                    data-row="{{ $index }}"
                                    data-col="{{ count($allTps) }}"
                                    @input="markDirty($el)"
                                    @keydown.enter.prevent="navigateCell($event, {{ $index }}, {{ count($allTps) }})"
                                    @keydown.arrow-down.prevent="navigateCell($event, {{ $index }}, {{ count($allTps) }})"
                                    @keydown.arrow-up.prevent="navigateCell($event, {{ $index }}, {{ count($allTps) }})"
                                    wire:model.defer="nilaiSasMatrix.{{ $s->id }}"
                                    class="w-16 sm:w-20 h-8 sm:h-9 bg-white border border-cyan-400 text-cyan-900 font-black rounded-lg text-center text-xs py-1 focus:ring-2 focus:ring-cyan-500 shadow-2xs transition"
                                    placeholder="0"
                                >
                            </td>

                            <!-- Calculated Column: Nilai Rapor Akhir -->
                            <td class="p-2 text-center bg-emerald-50/50 group-hover:bg-emerald-100/60 font-black text-emerald-900 text-xs sm:text-sm border-b border-stone-200 transition">
                                {{ $nilaiRaporFormatted !== null ? number_format($nilaiRaporFormatted, 2) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <x-table.empty :colspan="count($allTps) + 5" title="Tidak ada siswa" message="Tidak ada siswa terdaftar pada rombel kelas ini." />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Floating Mobile Bottom Action Bar -->
    @if(count($siswas) > 0)
        <div class="fixed bottom-4 left-4 right-4 z-30 md:hidden flex flex-col gap-1.5">
            <span x-show="isDirty" x-cloak class="inline-flex items-center justify-center gap-1.5 py-1.5 px-3 bg-amber-100 border border-amber-300 text-amber-900 rounded-xl text-xs font-bold text-center shadow-md animate-pulse">
                <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Nilai belum disimpan! Tekan simpan di bawah.</span>
            </span>
            <button 
                type="button" 
                wire:click="saveAllScores" 
                class="w-full py-3 px-4 bg-emerald-700 active:bg-emerald-800 text-white rounded-2xl font-bold text-xs shadow-xl flex items-center justify-center gap-2 border border-emerald-600/80 cursor-pointer"
            >
                <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Simpan & Hitung Rapor ({{ count($siswas) }} Siswa)</span>
            </button>
        </div>
    @endif

    <script>
        function getSumatifTourSteps() {
            return [
                {
                    element: '#tour-sumatif-filter',
                    popover: {
                        title: '1. Filter Kelas, Mapel, & Bab',
                        description: 'Tentukan Rombel Kelas dan Mata Pelajaran yang akan dinilai. Sistem otomatis memfilter ke Bab 1 agar tampilan tabel rapi dan tidak terlalu lebar.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-sumatif-tabel',
                    popover: {
                        title: '2. Input Nilai Cepat Format Excel',
                        description: 'Ketik nilai capaian TP dan Sumatif Akhir Semester (SAS). Anda dapat menekan tombol panah Atas, Bawah, atau Enter pada keyboard untuk berpindah siswa dengan sangat cepat.',
                        side: 'top',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-sumatif-simpan',
                    popover: {
                        title: '3. Simpan & Kalkulasi Rapor',
                        description: 'Jika muncul lencana kuning "Nilai belum disimpan", pastikan menekan tombol Simpan & Hitung Rapor ini untuk memperbarui nilai rapor akhir siswa ke database.',
                        side: 'bottom',
                        align: 'end'
                    }
                }
            ];
        }

        function runSumatifTour() {
            if (typeof window.startSiakadTour === 'function') {
                window.startSiakadTour('siakad_tour_sumatif_v1', getSumatifTourSteps());
            }
        }

        document.addEventListener('livewire:navigated', () => {
            if (typeof window.checkAutoTour === 'function') {
                window.checkAutoTour('siakad_tour_sumatif_v1', getSumatifTourSteps(), 800);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            if (typeof window.checkAutoTour === 'function') {
                window.checkAutoTour('siakad_tour_sumatif_v1', getSumatifTourSteps(), 800);
            }
        });
    </script>
</div>
