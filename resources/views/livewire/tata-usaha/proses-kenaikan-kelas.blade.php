<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Proses Kenaikan Kelas & Kelulusan Siswa"
        :steps="[
            ['title' => 'Pilih Kelas Asal', 'desc' => 'Pilih rombongan belajar (rombel) asal siswa yang akan diproses pada akhir tahun ajaran.'],
            ['title' => 'Pilih Kelas Tujuan / Kelulusan', 'desc' => 'Tentukan kelas tingkat selanjutnya untuk siswa naik kelas, atau pilih Opsi Lulus untuk siswa tingkat akhir.'],
            ['title' => 'Eksekusi Kenaikan', 'desc' => 'Tandai siswa yang berhak naik/lulus lalu klik Eksekusi Kenaikan Kelas. Siswa lulus akan otomatis masuk ke data Alumni.']
        ]"
        notes="Pastikan seluruh nilai rapor semester genap telah selesai dimasukkan sebelum melakukan eksekusi kenaikan kelas."
    />

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Kenaikan Kelas &amp; Kelulusan Massal</h2>
            <p class="text-xs text-slate-400">Proses pemindahan kelas atau kelulusan siswa secara otomatis per rombongan belajar.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif
    @if (session()->has('error'))
        <x-alert-banner type="danger" :message="session('error')" />
    @endif

    <!-- Control Panel / Settings -->
    <div class="bg-slate-900/40 border border-slate-800 rounded-2xl p-6 space-y-4 shadow-xl">
        <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            Pengaturan Rombel & Aksi Tujuan
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- 1. Kelas Asal -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400">1. Pilih Kelas Asal</label>
                <select wire:model.live="kelasAsalId" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Kelas Asal --</option>
                    @foreach ($kelases as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} (Tingkat {{ $k->tingkat }})</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Jenis Aksi -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400">2. Jenis Aksi</label>
                <select wire:model.live="aksiTujuan" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500">
                    <option value="naik_kelas">Kenaikan Kelas (Pindah Rombel)</option>
                    <option value="lulus_alumni">Kelulusan (Pindah ke Data Alumni)</option>
                </select>
            </div>

            <!-- 3. Kelas Tujuan (jika naik_kelas) -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400">3. Target Tujuan</label>
                @if ($aksiTujuan === 'naik_kelas')
                    <select wire:model="kelasTujuanId" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        @foreach ($kelases as $k)
                            @if ($k->id != $kelasAsalId)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }} (Tingkat {{ $k->tingkat }})</option>
                            @endif
                        @endforeach
                    </select>
                @else
                    <div class="px-3 py-2 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-400 text-xs font-bold flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Status Siswa ➔ ALUMNI (Tahun Lulus {{ date('Y') }})
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-between border-t border-slate-800/80 pt-4">
            <div class="text-xs text-slate-400">
                Terpilih: <span class="font-bold text-white">{{ count($selectedSiswa) }}</span> dari {{ count($students) }} siswa
            </div>
            <button wire:click="prosesKenaikan" wire:confirm="Apakah Anda yakin ingin memproses aksi ini untuk siswa terpilih?"
                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-indigo-600/20 inline-flex items-center gap-2"
                @if(count($selectedSiswa) === 0) disabled @endif>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Proses {{ $aksiTujuan === 'naik_kelas' ? 'Kenaikan Kelas' : 'Kelulusan' }}
            </button>
        </div>
    </div>

    <!-- Student Table -->
    <div class="bg-slate-900/40 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4 w-10 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500" />
                        </th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">NIS / NISN</th>
                        <th class="py-3 px-4">Jenis Kelamin</th>
                        <th class="py-3 px-4">Kelas Saat Ini</th>
                        <th class="py-3 px-4 text-center">Status Siswa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($students as $siswa)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 px-4 text-center">
                                <input type="checkbox" wire:model.live="selectedSiswa" value="{{ $siswa->id }}"
                                    class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500" />
                            </td>
                            <td class="py-3 px-4 font-bold text-white">
                                {{ $siswa->user->nama ?? '-' }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-400">
                                {{ $siswa->nis ?? '-' }} / {{ $siswa->nisn ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-slate-300">
                                {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </td>
                            <td class="py-3 px-4 font-medium text-indigo-400">
                                {{ $siswa->kelas->nama_kelas ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase">
                                    {{ $siswa->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500 text-xs">
                                Tidak ada siswa aktif yang ditemukan pada kelas asal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
