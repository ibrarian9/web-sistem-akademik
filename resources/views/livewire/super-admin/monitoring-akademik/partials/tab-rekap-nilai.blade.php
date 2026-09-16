<!-- ==================== TAB 1: MONITORING REKAP NILAI SISWA ==================== -->
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
@endif
