<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Input Nilai Mata Pelajaran Siswa"
        :steps="[
            ['title' => 'Pilih Kelas, Mapel & Komponen', 'desc' => 'Tentukan kelas, mata pelajaran, serta komponen penilaian (UH, UTS, UAS, Tahfizh).'],
            ['title' => 'Input Skala 0-100', 'desc' => 'Masukkan skor nilai angka siswa (0.0 - 100.0) dan amati acuan standar KKM mata pelajaran.'],
            ['title' => 'Simpan Penilaian', 'desc' => 'Klik Simpan Seluruh Nilai untuk menyimpan nilai akhir ke basis data rapor.']
        ]"
    />

    <!-- Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                AKADEMIK &amp; PENILAIAN
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Input Nilai Siswa</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Pilih kelas, mata pelajaran, dan komponen penilaian untuk menginput nilai siswa secara kolektif.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
            <x-lucide-check-circle class="w-4 h-4 text-emerald-600 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-300 text-rose-900 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
            <x-lucide-alert-circle class="w-4 h-4 text-rose-600 shrink-0" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Selection Bar -->
    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Kelas -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Kelas / Rombel</label>
                <select wire:model.live="kelas_id" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-xs">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Mata Pelajaran -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Mata Pelajaran</label>
                <select wire:model.live="mapel_id" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-xs">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach ($subjects as $s)
                        <option value="{{ $s['id'] }}">{{ $s['nama_mapel'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Komponen Nilai -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Komponen Nilai</label>
                <select wire:model.live="komponen_nilai_id" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-xs">
                    <option value="">-- Pilih Komponen --</option>
                    @foreach ($components as $comp)
                        <option value="{{ $comp['id'] }}">{{ $comp['nama'] }} ({{ floatval($comp['bobot']) }}%)</option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal Penilaian -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Tanggal Penilaian</label>
                <input wire:model.live="tanggal" type="date" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-xs" />
            </div>
        </div>
    </div>

    <!-- Student Grades Input Table -->
    @if ($kelas_id && $mapel_id && $komponen_nilai_id)
        <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden space-y-4">
            <div class="p-4 bg-stone-50 border-b border-stone-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">Daftar Nilai Siswa</h3>
                <div class="flex items-center gap-2 px-3 py-1 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-extrabold text-emerald-800">
                    <x-lucide-target class="w-4 h-4 text-emerald-600" />
                    <span>KKM Mata Pelajaran: {{ number_format($selectedMapelKkm, 2) }}</span>
                </div>
            </div>

            <form wire:submit.prevent="save" class="space-y-6">
                <x-table loadingTarget="selectedKelasId, selectedMapelId, selectedKomponenId">
                    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                        <tr>
                            <x-table.th class="w-32">NIS</x-table.th>
                            <x-table.th class="w-64">Nama Siswa</x-table.th>
                            <x-table.th class="w-40">Nilai (0 - 100)</x-table.th>
                            <x-table.th>Catatan Pengajar</x-table.th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($grades as $index => $grade)
                            <tr class="hover:bg-stone-50 transition">
                                <td class="p-3.5 border-r border-stone-200 text-stone-600 font-mono font-bold text-xs">{{ $grade['nis'] }}</td>
                                <td class="p-3.5 border-r border-stone-200 font-extrabold text-stone-900 text-xs">{{ $grade['nama'] }}</td>
                                <td class="p-3.5 border-r border-stone-200">
                                    <input wire:model="grades.{{ $index }}.nilai" type="number" step="0.01" min="0" max="100"
                                        class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs focus:ring-2 focus:ring-emerald-600 text-right font-black shadow-xs" placeholder="0.0" />
                                    @error("grades.{$index}.nilai") 
                                        <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> 
                                    @enderror
                                </td>
                                <td class="p-3.5">
                                    <input wire:model="grades.{{ $index }}.catatan" type="text"
                                        class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-xs" placeholder="Keterangan pencapaian siswa..." />
                                    @error("grades.{$index}.catatan") 
                                        <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> 
                                    @enderror
                                </td>
                            </tr>
                        @empty
                            <x-table.empty :colspan="4" title="Tidak ada data siswa" message="Tidak ada data siswa aktif terdaftar di rombel kelas ini." />
                        @endforelse
                    </tbody>
                </x-table>

                @if (count($grades) > 0)
                    <div class="flex justify-end border-t border-stone-200 p-4 bg-stone-50">
                        <x-button type="submit" variant="primary" size="md" icon="check-circle">
                            Simpan Seluruh Nilai
                        </x-button>
                    </div>
                @endif
            </form>
        </div>
    @else
        <div class="bg-white border border-stone-200 rounded-2xl p-12 text-center text-stone-500 font-medium shadow-sm">
            <x-lucide-edit-3 class="w-10 h-10 mx-auto mb-3 text-stone-400" />
            <span class="text-xs font-bold text-stone-600">Pilih kelas, mata pelajaran, dan komponen penilaian di atas untuk memulai pengisian nilai.</span>
        </div>
    @endif
</div>
