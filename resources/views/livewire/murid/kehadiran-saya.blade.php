<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold text-stone-900 tracking-tight">Kehadiran Saya</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                    Presensi Harian
                </span>
            </div>
            <p class="text-xs text-stone-500 mt-1">Pantau rekam jejak kedisiplinan dan absensi harian Anda di sekolah.</p>
        </div>
        <div class="flex items-center gap-3 px-4 py-2.5 bg-white border border-stone-200 rounded-2xl shadow-sm">
            <div class="w-8 h-8 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-xs">
                <x-lucide-user-check class="w-4 h-4" />
            </div>
            <div>
                <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Identitas Siswa</span>
                <span class="text-xs font-bold text-stone-800">
                    {{ auth()->user()->nama }} (Kelas {{ auth()->user()->siswa->kelas->nama_kelas ?? '-' }})
                </span>
            </div>
        </div>
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Ketentuan Kehadiran Presensi Siswa"
        :steps="[
            ['title' => 'Target Minimal 75%', 'desc' => 'Batas minimal persentase kehadiran untuk mengikuti ujian semester adalah 75%.'],
            ['title' => 'Surat Keterangan', 'desc' => 'Apabila berhalangan hadir dikarenakan sakit/izin, kumpulkan surat dokter atau wali murid ke Guru Piket.'],
            ['title' => 'Filter Bulan', 'desc' => 'Gunakan pemilih bulan untuk memfilter riwayat presensi harian siswa per periode.']
        ]"
    />

    <!-- Attendance Performance Gauge Banner -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-5 w-full md:w-auto">
            <div class="relative flex items-center justify-center w-24 h-24 shrink-0">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                    <path class="text-stone-200" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    <path class="{{ $rate >= 75 ? 'text-emerald-600' : 'text-rose-600' }}" stroke-dasharray="{{ $rate }}, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                </svg>
                <div class="absolute flex flex-col items-center justify-center text-center">
                    <span class="text-lg font-black text-stone-900 tracking-tight">{{ $rate }}%</span>
                    <span class="text-[9px] font-bold text-stone-400 uppercase">Rate</span>
                </div>
            </div>

            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-stone-900">Tingkat Kehadiran</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                        {{ $performanceCategory === 'Sangat Baik' || $performanceCategory === 'Baik' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                        {{ $performanceCategory }}
                    </span>
                </div>
                <p class="text-xs text-stone-600 font-medium">
                    Total tercatat {{ $totalPertemuan }} hari pertemuan sekolah pada periode akademik ini.
                </p>
                <div class="w-full bg-stone-100 h-2 rounded-full overflow-hidden mt-3 max-w-xs border border-stone-200">
                    <div class="bg-emerald-600 h-full rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $rate)) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Metric Cards Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 w-full md:w-auto">
            <!-- Total Hadir -->
            <div class="bg-stone-50 border border-stone-200 rounded-xl p-3.5 text-center space-y-1">
                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Hadir</span>
                <span class="text-xl font-black text-emerald-700">{{ $totalHadir }}</span>
                <span class="text-[10px] font-semibold text-stone-500 block">Hari</span>
            </div>

            <!-- Total Izin -->
            <div class="bg-stone-50 border border-stone-200 rounded-xl p-3.5 text-center space-y-1">
                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Izin</span>
                <span class="text-xl font-black text-amber-700">{{ $totalIzin }}</span>
                <span class="text-[10px] font-semibold text-stone-500 block">Hari</span>
            </div>

            <!-- Total Sakit -->
            <div class="bg-stone-50 border border-stone-200 rounded-xl p-3.5 text-center space-y-1">
                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Sakit</span>
                <span class="text-xl font-black text-sky-700">{{ $totalSakit }}</span>
                <span class="text-[10px] font-semibold text-stone-500 block">Hari</span>
            </div>

            <!-- Total Alpa -->
            <div class="bg-stone-50 border border-stone-200 rounded-xl p-3.5 text-center space-y-1">
                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Alpa</span>
                <span class="text-xl font-black text-rose-700">{{ $totalTidakHadir }}</span>
                <span class="text-[10px] font-semibold text-stone-500 block">Hari</span>
            </div>
        </div>
    </div>

    <!-- Log Kehadiran Section -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-stone-200 pb-4">
            <div>
                <h3 class="text-sm font-bold text-stone-900 uppercase tracking-wider">Riwayat Absensi Harian</h3>
                <p class="text-xs text-stone-500">Daftar kehadiran tercatat berdasarkan pengisian presensi kelas.</p>
            </div>

            <!-- Filter Controls -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Month Filter -->
                <div class="relative">
                    <input type="month" wire:model.live="selectedMonth" 
                           class="bg-stone-50 border border-stone-300 rounded-xl px-3 py-1.5 text-xs text-stone-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-semibold">
                </div>

                <!-- Status Filter -->
                <div>
                    <select wire:model.live="selectedStatus" 
                            class="bg-stone-50 border border-stone-300 rounded-xl px-3 py-1.5 text-xs text-stone-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-semibold">
                        <option value="">Semua Status</option>
                        <option value="hadir">Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="tidak_hadir">Alpa / Tidak Hadir</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Log Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-stone-200 bg-stone-50">
                        <th class="py-3 px-4 text-xs font-bold text-stone-600 uppercase tracking-wider">Hari & Tanggal</th>
                        <th class="py-3 px-4 text-xs font-bold text-stone-600 uppercase tracking-wider">Status Absensi</th>
                        <th class="py-3 px-4 text-xs font-bold text-stone-600 uppercase tracking-wider">Pengisi Absensi</th>
                        <th class="py-3 px-4 text-xs font-bold text-stone-600 uppercase tracking-wider">Catatan / Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
                    @forelse ($history as $h)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="py-3.5 px-4 text-xs font-semibold">
                                <div class="flex flex-col">
                                    <span class="text-stone-900 font-bold">{{ $h['tanggal'] }}</span>
                                    <span class="text-[10px] text-stone-500">{{ $h['hari'] }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if ($h['status'] === 'hadir')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <x-lucide-check-circle class="w-3.5 h-3.5" />
                                        Hadir
                                    </span>
                                @elseif ($h['status'] === 'izin')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                        <x-lucide-file-text class="w-3.5 h-3.5" />
                                        Izin
                                    </span>
                                @elseif ($h['status'] === 'sakit')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-sky-100 text-sky-800 border border-sky-200">
                                        <x-lucide-activity class="w-3.5 h-3.5" />
                                        Sakit
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200">
                                        <x-lucide-alert-circle class="w-3.5 h-3.5" />
                                        Alpa / Tidak Hadir
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs text-stone-700 font-medium">
                                {{ $h['guru'] }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-stone-600 font-medium">
                                {{ $h['catatan'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-stone-500 space-y-2">
                                <x-lucide-calendar class="w-10 h-10 mx-auto text-stone-400" />
                                <p class="text-xs font-semibold">Belum ada riwayat kehadiran tercatat pada filter ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
