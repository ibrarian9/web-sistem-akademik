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
            <a href="{{ route('guru.kurikulum-merdeka') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 01-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Setup Bab &amp; TP</span>
            </a>
            <a href="{{ route('guru.input-sumatif') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-sm flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
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
        title="Petunjuk Matriks Penilaian Sumatif TP & SAS"
        :steps="[
            ['title' => 'Pilih Kelas & Mapel', 'desc' => 'Tentukan Kelas, Mata Pelajaran, Bab (opsional), dan Semester aktif yang akan dinilai.'],
            ['title' => 'Input Nilai Cepat (Excel Style)', 'desc' => 'Isikan nilai TP 1, TP 2, ... dan Nilai SAS. Gunakan panah keyboard (↑ / ↓ / Enter) untuk berpindah sel.'],
            ['title' => 'Simpan & Kalkulasi Rapor', 'desc' => 'Klik Simpan & Hitung Rapor untuk menyimpan seluruh matriks nilai secara instan ke dalam database.']
        ]"
        notes="Nilai Rapor dihitung otomatis dari rata-rata nilai TP dan Nilai SAS. Hasil nilai akan langsung terakumulasi pada lembar Rapor Siswa."
    />

    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm space-y-6">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Form Matriks Nilai Sumatif TP &amp; SAS</h1>
                <p class="text-stone-600 text-xs font-semibold mt-1">Gunakan panah keyboard (↑ / ↓ / Enter) untuk berpindah sel nilai secara cepat seperti Excel.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <x-button variant="secondary" size="md" icon="layers" href="{{ route('guru.kurikulum-merdeka') }}">
                    Setup Bab &amp; TP
                </x-button>
                <x-button variant="primary" size="md" icon="check" wire:click="saveAllScores" loadingTarget="saveAllScores">
                    Simpan &amp; Hitung Rapor
                </x-button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Kelas</label>
                <select wire:model.live="kelas_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-xs">
                    @foreach($kelases as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
                <select wire:model.live="mapel_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-xs">
                    @foreach($mapels as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Filter Bab (Lingkup Materi)</label>
                <select wire:model.live="lingkup_materi_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-xs">
                    <option value="">-- Semua Bab --</option>
                    @foreach($lingkupMateris as $lm)
                        <option value="{{ $lm->id }}">{{ $lm->nama_lingkup_materi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Semester</label>
                <select wire:model.live="semester_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-xs">
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
        <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 p-4 rounded-xl text-xs font-bold flex items-center justify-between shadow-xs">
            <span>{{ session('message') }}</span>
            <span class="px-2.5 py-0.5 bg-emerald-200 text-emerald-900 rounded font-black text-[10px]">Tersimpan</span>
        </div>
    @endif

    <!-- Matrix Table -->
    <div x-data="{
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
    }" class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm space-y-4">
        
        <div class="p-4 bg-stone-50 border-b border-stone-200 flex items-center justify-between">
            <span class="text-xs font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                Matriks Penilaian Sumatif TP, SAS, &amp; Hasil Rapor Akhir
            </span>
            <span class="text-[11px] text-stone-500 font-semibold">Total {{ count($siswas) }} Siswa</span>
        </div>

        <x-table loadingTarget="selectedKelasId, selectedMapelId, selectedSemesterId">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th align="center" class="w-12 sticky left-0 bg-emerald-800 z-10">No</x-table.th>
                    <x-table.th class="min-w-[200px] sticky left-12 bg-emerald-800 z-10">Nama Siswa</x-table.th>
                    @foreach($tps as $tpIdx => $tp)
                        <x-table.th align="center" class="min-w-[90px]" title="{{ $tp->deskripsi_tp }}">
                            <span class="block text-emerald-100 font-black">TP {{ $tp->urutan }}</span>
                            <span class="text-[10px] text-emerald-200 font-normal block truncate max-w-[100px]">{{ $tp->deskripsi_tp }}</span>
                        </x-table.th>
                    @endforeach
                    <x-table.th align="center" class="w-24 bg-emerald-900 text-white font-black">Nilai SAS</x-table.th>
                    <x-table.th align="center" class="w-28 bg-emerald-950 text-white font-black">Nilai Rapor</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse($siswas as $index => $s)
                    @php
                        $tpScores = array_filter(array_values($nilaiTpMatrix[$s->id] ?? []), function($v) {
                            return $v !== '' && $v !== null && is_numeric($v);
                        });
                        $avgTp = count($tpScores) > 0 ? array_sum($tpScores) / count($tpScores) : null;
                        
                        $sasVal = $nilaiSasMatrix[$s->id] ?? '';
                        $sasNum = (is_numeric($sasVal) && $sasVal !== '') ? (float)$sasVal : null;
                        
                        $components = [];
                        if ($avgTp !== null) $components[] = $avgTp;
                        if ($sasNum !== null) $components[] = $sasNum;
                        
                        $nilaiRapor = count($components) > 0 ? array_sum($components) / count($components) : null;
                        $nilaiRaporFormatted = $nilaiRapor !== null ? round($nilaiRapor, 2) : null;
                    @endphp
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3 text-center sticky left-0 bg-white font-bold text-stone-500 border-r border-stone-200 text-xs">{{ $index + 1 }}</td>
                        <td class="p-3 sticky left-12 bg-white font-bold text-stone-900 border-r border-stone-200 text-xs">
                            {{ $s->user->nama ?? $s->nama_panggilan }}
                            <span class="block text-[10px] text-stone-500 font-normal">NISN: {{ $s->nisn }}</span>
                        </td>
                        @foreach($tps as $colIdx => $tp)
                            <td class="p-2 text-center border-r border-stone-200">
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    max="100"
                                    data-row="{{ $index }}"
                                    data-col="{{ $colIdx }}"
                                    @keydown.enter.prevent="navigateCell($event, {{ $index }}, {{ $colIdx }})"
                                    @keydown.arrow-down.prevent="navigateCell($event, {{ $index }}, {{ $colIdx }})"
                                    @keydown.arrow-up.prevent="navigateCell($event, {{ $index }}, {{ $colIdx }})"
                                    wire:model.defer="nilaiTpMatrix.{{ $s->id }}.{{ $tp->id }}"
                                    class="w-16 bg-white border border-stone-300 rounded-lg text-center text-stone-900 font-black py-1.5 focus:ring-2 focus:ring-emerald-500 focus:bg-emerald-50 text-xs shadow-xs"
                                    placeholder="0"
                                >
                            </td>
                        @endforeach
                        <!-- Input SAS -->
                        <td class="p-2 text-center bg-cyan-50/50 border-r border-stone-200">
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0" 
                                max="100"
                                data-row="{{ $index }}"
                                data-col="{{ count($allTps) }}"
                                @keydown.enter.prevent="navigateCell($event, {{ $index }}, {{ count($allTps) }})"
                                @keydown.arrow-down.prevent="navigateCell($event, {{ $index }}, {{ count($allTps) }})"
                                @keydown.arrow-up.prevent="navigateCell($event, {{ $index }}, {{ count($allTps) }})"
                                wire:model.defer="nilaiSasMatrix.{{ $s->id }}"
                                class="w-20 bg-white border border-cyan-400 text-cyan-900 font-black rounded-lg text-center py-1.5 focus:ring-2 focus:ring-cyan-500 text-xs shadow-xs"
                                placeholder="0"
                            >
                        </td>

                        <!-- Calculated Column: Nilai Rapor Akhir -->
                        <td class="p-2 text-center bg-emerald-50/60 font-black text-emerald-900 text-sm">
                            {{ $nilaiRaporFormatted !== null ? number_format($nilaiRaporFormatted, 2) : '-' }}
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="count($allTps) + 3" title="Tidak ada siswa" message="Tidak ada siswa terdaftar pada rombel kelas ini." />
                @endforelse
            </tbody>
        </x-table>
    </div>
    </div>
</div>
