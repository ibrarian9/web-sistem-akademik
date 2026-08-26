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
    <x-page-header 
        title="Rekap Nilai Akademik Siswa" 
        subtitle="Laporan rekapitulasi nilai siswa per kelas per mata pelajaran per semester."
        badge="LAPORAN NILAI"
        badgeVariant="emerald"
        icon="book-open"
    >
        <x-slot:actions>
            <x-button type="button" variant="outline" size="sm" icon="file-text" wire:click="downloadPdf">
                Ekspor PDF
            </x-button>
        </x-slot:actions>
    </x-page-header>

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
                <x-button type="button" variant="outline" size="sm" icon="file-text" wire:click="downloadPdf" :disabled="empty($matrix)" title="{{ empty($matrix) ? 'Tidak ada data nilai untuk diekspor' : 'Ekspor Dokumen PDF' }}">
                    Ekspor PDF
                </x-button>
            </div>

            <!-- Scrollable Matrix Table -->
            <x-table loadingTarget="kelasId, mapelId, semesterId">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <x-table.th align="center" class="w-12">No</x-table.th>
                        <x-table.th class="w-56">Nama Siswa</x-table.th>
                        @foreach ($components as $comp)
                            <x-table.th align="center" class="w-28">
                                {{ $comp->nama }}
                                <div class="text-[9px] text-emerald-200 font-semibold mt-0.5">Bobot: {{ intval($comp->bobot) }}%</div>
                            </x-table.th>
                        @endforeach
                        <x-table.th align="center" class="w-28 bg-emerald-900 text-white font-black">Nilai Akhir</x-table.th>
                        <x-table.th align="center" class="w-20 bg-emerald-950 text-white font-black">Predikat</x-table.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($matrix as $index => $row)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-3.5 text-center border-r border-stone-200 font-bold text-stone-400 text-xs">{{ $index + 1 }}</td>
                            <td class="p-3.5 border-r border-stone-200 font-bold text-stone-900 text-xs">
                                {{ $row['siswa']->user->nama }}
                                <div class="text-[10px] text-stone-400 font-semibold mt-0.5">NIS: {{ $row['siswa']->nis }}</div>
                            </td>
                            @foreach ($components as $comp)
                                @php
                                    $val = $row['compGrades'][$comp->id];
                                    $cellClass = is_null($val) ? 'text-stone-300' : 'text-stone-700 font-bold';
                                    $cellText = is_null($val) ? '•' : $val;
                                @endphp
                                <td class="p-3.5 text-center border-r border-stone-200 text-xs {{ $cellClass }}">{{ $cellText }}</td>
                            @endforeach
                            <td class="p-3.5 text-center border-r border-stone-200 bg-emerald-50/50 text-emerald-800 font-black text-sm">{{ $row['finalGrade'] }}</td>
                            <td class="p-3.5 text-center bg-stone-50 font-black text-sm">
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
                        <x-table.empty :colspan="$components->count() + 4" title="Belum ada data nilai" message="Tidak ada data siswa aktif atau penilaian pada rombel kelas ini." />
                    @endforelse
                </tbody>
            </x-table>

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
