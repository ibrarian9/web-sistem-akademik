<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Input Nilai Mata Pelajaran Siswa"
        :steps="[
            ['title' => 'Pilih Kelas, Mapel & Komponen', 'desc' => 'Tentukan kelas, mata pelajaran, serta komponen penilaian (UH, UTS, UAS, Tahfizh).'],
            ['title' => 'Input Skala 0-100', 'desc' => 'Masukkan skor nilai angka siswa (0.0 - 100.0) dan amati acuan standar KKM mata pelajaran.'],
            ['title' => 'Simpan Penilaian', 'desc' => 'Klik Simpan Seluruh Nilai untuk menyimpan nilai akhir ke basis data rapor.']
        ]"
    />

    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Input Nilai Siswa</h2>
        <p class="text-xs text-slate-500">Pilih kelas, mata pelajaran, dan komponen penilaian untuk menginput nilai siswa secara kolektif.</p>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Selection Bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <!-- Kelas -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas</label>
                <select wire:model.live="kelas_id" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                    <option value="">Pilih Kelas</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Mata Pelajaran -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mata Pelajaran</label>
                <select wire:model.live="mapel_id" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                    <option value="">Pilih Mapel</option>
                    @foreach ($subjects as $s)
                        <option value="{{ $s['id'] }}">{{ $s['nama_mapel'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Komponen Nilai -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Komponen Nilai</label>
                <select wire:model.live="komponen_nilai_id" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                    <option value="">Pilih Komponen</option>
                    @foreach ($components as $comp)
                        <option value="{{ $comp['id'] }}">{{ $comp['nama'] }} ({{ floatval($comp['bobot']) }}%)</option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal Penilaian -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Penilaian</label>
                <input wire:model.live="tanggal" type="date" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
            </div>
        </div>
    </div>

    <!-- Student Grades Input Table -->
    @if ($kelas_id && $mapel_id && $komponen_nilai_id)
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Daftar Nilai Siswa</h3>
                <div class="flex items-center gap-2 px-3 py-1 bg-indigo-950/60 border border-indigo-800/60 rounded-xl text-xs font-bold text-indigo-300">
                    <x-lucide-target class="w-4 h-4 text-indigo-400" />
                    <span>KKM Mata Pelajaran: {{ number_format($selectedMapelKkm, 2) }}</span>
                </div>
            </div>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800">
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">NIS</th>
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Siswa</th>
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-32">Nilai (0 - 100)</th>
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Catatan Pengajar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850">
                            @forelse ($grades as $index => $grade)
                                <tr class="hover:bg-slate-950/20">
                                    <td class="py-3.5 text-xs font-semibold text-slate-400">{{ $grade['nis'] }}</td>
                                    <td class="py-3.5 text-xs font-bold text-white">{{ $grade['nama'] }}</td>
                                    <td class="py-3.5">
                                        <input wire:model="grades.{{ $index }}.nilai" type="number" step="0.01" min="0" max="100"
                                            class="w-full px-2.5 py-1.5 bg-slate-950/60 border border-slate-800 rounded-lg text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 text-right font-bold" placeholder="0.0" />
                                        @error("grades.{$index}.nilai") 
                                            <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> 
                                        @enderror
                                    </td>
                                    <td class="py-3.5">
                                        <input wire:model="grades.{{ $index }}.catatan" type="text"
                                            class="w-full px-2.5 py-1.5 bg-slate-950/60 border border-slate-800 rounded-lg text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Keterangan pencapaian siswa..." />
                                        @error("grades.{$index}.catatan") 
                                            <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> 
                                        @enderror
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-500 font-semibold">
                                        Tidak ada siswa aktif terdaftar di kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (count($grades) > 0)
                    <div class="flex justify-end border-t border-slate-800 pt-4">
                        <button type="submit" class="py-2.5 px-6 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition duration-200 shadow-lg shadow-indigo-600/10">
                            Simpan Seluruh Nilai
                        </button>
                    </div>
                @endif
            </form>
        </div>
    @else
        <div class="bg-slate-900/40 border border-slate-800 rounded-3xl p-12 text-center text-slate-500 font-medium">
            <x-lucide-edit-3 class="w-8 h-8 mx-auto mb-3 text-slate-600" />
            <span>Pilih seluruh filter di atas untuk memulai pengisian nilai siswa.</span>
        </div>
    @endif
</div>
