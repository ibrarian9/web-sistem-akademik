<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Laporan Rekap Nilai Akademik Siswa"
        :steps="[
            ['title' => 'Pilih Kelas, Mapel & Semester', 'desc' => 'Gunakan filter di atas untuk menentukan rombel kelas, mata pelajaran, serta semester berjalan.'],
            ['title' => 'Perhitungan Otomatis', 'desc' => 'Nilai akhir dihitung secara otomatis dari pembobotan persentase tiap komponen (UH, UTS, UAS, Tahfizh).'],
            ['title' => 'Predikat Rapor', 'desc' => 'Tabel langsung mengonversi nilai akhir menjadi predikat mutu A, B, C, D, atau E.']
        ]"
    />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Rekap Nilai Akademik</h2>
            <p class="text-sm text-stone-500 font-medium">Laporan rekapitulasi nilai siswa per kelas per mata pelajaran per semester.</p>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Kelas Selection -->
            <div>
                <label for="kelasId" class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Kelas</label>
                <select id="kelasId" wire:model.live="kelasId" 
                        class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-700 shadow-sm focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Mata Pelajaran Selection -->
            <div>
                <label for="mapelId" class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Mata Pelajaran</label>
                <select id="mapelId" wire:model.live="mapelId" 
                        class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-700 shadow-sm focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach ($mapels as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Semester Selection -->
            <div>
                <label for="semesterId" class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Semester</label>
                <select id="semesterId" wire:model.live="semesterId" 
                        class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-700 shadow-sm focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                    @foreach ($semesters as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_semester }} ({{ $s->tahunAjaran->nama_tahun ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Report Table / Matrix Card -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        @if ($kelas && $mapel && $semester)
            <!-- Table Action Panel -->
            <div class="px-6 py-4 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-stone-50/50">
                <div class="text-sm font-bold text-stone-700">
                    Kelas: <span class="text-green-700">{{ $kelas->nama_kelas }}</span> 
                    | Mapel: <span class="text-stone-600 font-semibold">{{ $mapel->nama_mapel }}</span>
                    | Semester: <span class="text-stone-600 font-semibold">{{ $semester->nama_semester }}</span>
                </div>
                <button wire:click="downloadPdf" 
                        class="inline-flex items-center justify-center gap-2 py-2 px-4 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-sm font-semibold text-stone-700 shadow-sm transition duration-150 shrink-0">
                    <x-lucide-file-text class="w-4 h-4 text-red-600" />
                    <span>Ekspor PDF</span>
                </button>
            </div>

            <!-- Scrollable Matrix Table -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-xs text-stone-600 min-w-[700px]">
                    <thead>
                        <tr class="bg-stone-50 border-b border-stone-200 font-bold text-stone-700">
                            <th class="py-3 px-4 w-12 text-center border-r border-stone-200">No</th>
                            <th class="py-3 px-4 w-56 border-r border-stone-200">Nama Siswa</th>
                            @foreach ($components as $comp)
                                <th class="py-3 px-3 text-center border-r border-stone-200 bg-stone-50">
                                    {{ $comp->nama }}
                                    <div class="text-[9px] text-stone-400 font-semibold mt-0.5">Bobot: {{ intval($comp->bobot) }}%</div>
                                </th>
                            @endforeach
                            <th class="py-3 px-4 w-24 text-center border-r border-stone-200 bg-green-50 text-green-800 font-extrabold text-sm">Nilai Akhir</th>
                            <th class="py-3 px-3 w-16 text-center bg-stone-100 text-stone-800 font-extrabold text-sm">Predikat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @forelse ($matrix as $index => $row)
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-3 px-4 text-center border-r border-stone-100 font-bold text-stone-400">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 border-r border-stone-100 font-bold text-stone-800">
                                    {{ $row['siswa']->user->nama }}
                                    <div class="text-[10px] text-stone-400 font-semibold mt-0.5">NIS: {{ $row['siswa']->nis }}</div>
                                </td>
                                @foreach ($components as $comp)
                                    @php
                                        $val = $row['compGrades'][$comp->id];
                                        $cellClass = is_null($val) ? 'text-stone-300' : 'text-stone-700 font-semibold';
                                        $cellText = is_null($val) ? '•' : $val;
                                    @endphp
                                    <td class="py-3 px-3 text-center border-r border-stone-100 {{ $cellClass }}">{{ $cellText }}</td>
                                @endforeach
                                <td class="py-3 px-4 text-center border-r border-stone-100 bg-green-50/30 text-green-700 font-extrabold text-sm">{{ $row['finalGrade'] }}</td>
                                <td class="py-3 px-3 text-center bg-stone-50 font-extrabold text-stone-800 text-sm">
                                    @php
                                        $predClass = '';
                                        if ($row['predikat'] === 'A') $predClass = 'text-green-600';
                                        elseif ($row['predikat'] === 'B') $predClass = 'text-blue-600';
                                        elseif ($row['predikat'] === 'C') $predClass = 'text-orange-600';
                                        elseif ($row['predikat'] === 'D') $predClass = 'text-yellow-600';
                                        else $predClass = 'text-red-600';
                                    @endphp
                                    <span class="{{ $predClass }}">{{ $row['predikat'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $components->count() + 4 }}" class="py-12 text-center text-stone-400 font-medium">
                                    Tidak ada data siswa aktif di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Calculation Note Panel -->
            <div class="p-6 border-t border-stone-200 bg-stone-50/30 text-xs text-stone-500 space-y-1">
                <p class="font-bold text-stone-700">Keterangan Rumus Nilai Akhir:</p>
                <p>• Nilai Akhir dihitung berdasarkan penjumlahan dari: <span class="font-semibold text-stone-700">Rata-rata Nilai per Komponen x (Bobot Komponen / 100)</span>.</p>
                <p>• Klasifikasi Predikat: <span class="font-semibold text-green-700">A (>= 90)</span>, <span class="font-semibold text-blue-700">B (80-89)</span>, <span class="font-semibold text-orange-700">C (70-79)</span>, <span class="font-semibold text-yellow-700">D (60-69)</span>, <span class="font-semibold text-red-700">E (< 60)</span>.</p>
            </div>
        @else
            <!-- Empty State -->
            <div class="py-16 text-center">
                <div class="w-12 h-12 rounded-full bg-stone-100 flex items-center justify-center mx-auto text-stone-400 mb-3">
                    <x-lucide-award class="w-6 h-6" />
                </div>
                <h3 class="text-sm font-bold text-stone-700">Silakan lengkapi filter terlebih dahulu</h3>
                <p class="text-xs text-stone-400 mt-1">Pilih Kelas, Mata Pelajaran, dan Semester untuk menampilkan rekapitulasi nilai.</p>
            </div>
        @endif
    </div>
</div>
