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
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Setup Bab &amp; TP</span>
            </a>
            <a href="{{ route('guru.input-sumatif') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-sm flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Nilai Sumatif</span>
            </a>
        @endif

        @if($isTahfizh || $isKeduanya)
            <a href="{{ route('guru.input-tahfidz') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
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
                <a href="{{ route('guru.kurikulum-merdeka') }}" class="bg-stone-100 hover:bg-stone-200 text-stone-800 border border-stone-300 font-bold px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 transition">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Setup Bab &amp; TP
                </a>
                <button wire:click="saveAllScores" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl text-xs flex items-center gap-2 transition shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan &amp; Hitung Rapor
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Kelas</label>
                <select wire:model.live="kelas_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500">
                    @foreach($kelases as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
                <select wire:model.live="mapel_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500">
                    @foreach($mapels as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Filter Bab (Lingkup Materi)</label>
                <select wire:model.live="lingkup_materi_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Bab --</option>
                    @foreach($lingkupMateris as $lm)
                        <option value="{{ $lm->id }}">{{ $lm->nama_lingkup_materi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Semester</label>
                <select wire:model.live="semester_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->tahunAjaran->nama ?? '' }} - {{ ucfirst($sem->semester) }}</option>
                    @endforeach
                </select>
            </div>
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

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs text-stone-800">
                <thead class="bg-stone-100 text-stone-700 font-extrabold uppercase tracking-wider border-b border-stone-200">
                    <tr>
                        <th class="p-3.5 sticky left-0 bg-stone-100 z-10 w-12 text-center border-r border-stone-200">No</th>
                        <th class="p-3.5 sticky left-12 bg-stone-100 z-10 min-w-[200px] border-r border-stone-200">Nama Siswa</th>
                        @foreach($tps as $tpIdx => $tp)

                            <th class="p-3 text-center min-w-[90px] border-r border-stone-200" title="{{ $tp->deskripsi_tp }}">
                                <span class="block text-emerald-700 font-black">TP {{ $tp->urutan }}</span>
                                <span class="text-[10px] text-stone-500 font-normal block truncate max-w-[100px]">{{ $tp->deskripsi_tp }}</span>
                            </th>
                        @endforeach
                        <th class="p-3.5 text-center bg-cyan-50 text-cyan-900 min-w-[90px] font-black border-r border-stone-200">Nilai SAS</th>
                        
                        <!-- Calculated Final Rapor Column -->
                        <th class="p-3.5 text-center bg-emerald-50 text-emerald-900 min-w-[100px] font-black">Nilai Rapor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
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
                            <td class="p-3 text-center sticky left-0 bg-white font-bold text-stone-500 border-r border-stone-200">{{ $index + 1 }}</td>
                            <td class="p-3 sticky left-12 bg-white font-bold text-stone-900 border-r border-stone-200">
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
                        <tr>
                            <td colspan="{{ count($allTps) + 3 }}" class="p-8 text-center text-stone-500 font-medium">
                                Tidak ada siswa terdaftar pada kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
