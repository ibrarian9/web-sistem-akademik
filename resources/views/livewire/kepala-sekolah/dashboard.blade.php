@php
    $userRole = auth()->user()->role->nama ?? '';
    $isPengawas = in_array($userRole, ['pengawas', 'koordinator']);
    $prefixRoute = $isPengawas ? 'pengawas' : 'kepala-sekolah';
    $roleTitle = $isPengawas ? 'Pengawas Sekolah' : 'Kepala Sekolah';
@endphp

<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Executive Monitoring {{ $roleTitle }}"
        :steps="[
            ['title' => 'Monitoring Akademik', 'desc' => 'Ringkasan rasio santri, tenaga pendidik, rombel kelas, serta tingkat kehadiran siswa.'],
            ['title' => 'Supervisi & Penilaian Guru', 'desc' => 'Tinjau capaian portofolio guru, berikan penilaian skor, predikat, dan validasi koreksi nilai rapor.'],
            ['title' => 'Rekap Tunggakan', 'desc' => 'Pantau jumlah santri yang masih memiliki kewajiban administrasi tagihan/SPP tertunggak.']
        ]"
    />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Dashboard Pemantauan {{ $roleTitle }}</h2>
            <p class="text-xs text-stone-500">Ringkasan eksekutif capaian akademis, supervisi guru, presensi, dan tunggakan administrasi yayasan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route($prefixRoute . '.capaian-guru') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                <x-lucide-award class="w-4 h-4" />
                <span>Evaluasi / Nilai Guru</span>
            </a>
            @if ($isPengawas)
                <a href="{{ route('pengawas.koreksi-nilai') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                    <x-lucide-shield-check class="w-4 h-4" />
                    <span>Persetujuan Nilai</span>
                </a>
            @endif
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex justify-between items-center text-stone-500 text-xs">
                <span class="font-semibold">Total Siswa Aktif</span>
                <div class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                    <x-lucide-users class="w-5 h-5" />
                </div>
            </div>
            <p class="text-2xl font-black text-stone-800">{{ $totalSiswa }} <span class="text-xs font-normal text-stone-400">Santri</span></p>
            <p class="text-[11px] text-stone-500">Kehadiran: <span class="font-bold text-emerald-600">{{ $persentaseKehadiran }}%</span></p>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex justify-between items-center text-stone-500 text-xs">
                <span class="font-semibold">Total Guru & Asatidz</span>
                <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <x-lucide-user-check class="w-5 h-5" />
                </div>
            </div>
            <p class="text-2xl font-black text-stone-800">{{ $totalGuru }} <span class="text-xs font-normal text-stone-400">Pengajar</span></p>
            <p class="text-[11px] text-stone-500">Status: <span class="font-bold text-emerald-600">Aktif Mengajar</span></p>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex justify-between items-center text-stone-500 text-xs">
                <span class="font-semibold">Total Rombel / Kelas</span>
                <div class="p-2 rounded-xl bg-sky-50 text-sky-600">
                    <x-lucide-school class="w-5 h-5" />
                </div>
            </div>
            <p class="text-2xl font-black text-stone-800">{{ $totalKelas }} <span class="text-xs font-normal text-stone-400">Kelas</span></p>
            <p class="text-[11px] text-stone-500">Tingkat: <span class="font-semibold text-stone-700">Tingkat 1 - 6</span></p>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex justify-between items-center text-stone-500 text-xs">
                <span class="font-semibold">Santri Menunggak SPP</span>
                <div class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <x-lucide-alert-circle class="w-5 h-5" />
                </div>
            </div>
            <p class="text-2xl font-black text-amber-700">{{ $totalSiswaMenunggak }} <span class="text-xs font-normal text-stone-400">Santri</span></p>
            <p class="text-[11px] text-stone-500">Total: <span class="font-semibold text-rose-600">Rp {{ number_format($totalNominalTunggakan, 0, ',', '.') }}</span></p>
        </div>
    </div>

    <!-- QUICK ACCESS MENU CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route($prefixRoute . '.laporan.absensi-siswa') }}" wire:navigate class="p-4 bg-white border border-stone-200 rounded-2xl shadow-sm hover:border-indigo-400 hover:shadow-md transition group flex items-center gap-3.5">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition">
                <x-lucide-file-text class="w-5 h-5" />
            </div>
            <div>
                <h4 class="text-xs font-bold text-stone-800 group-hover:text-indigo-600 transition">Laporan Absensi Siswa</h4>
                <p class="text-[10px] text-stone-500">Rekap persentase kehadiran harian</p>
            </div>
        </a>

        <a href="{{ route($prefixRoute . '.laporan.absensi-guru') }}" wire:navigate class="p-4 bg-white border border-stone-200 rounded-2xl shadow-sm hover:border-emerald-400 hover:shadow-md transition group flex items-center gap-3.5">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition">
                <x-lucide-clipboard class="w-5 h-5" />
            </div>
            <div>
                <h4 class="text-xs font-bold text-stone-800 group-hover:text-emerald-600 transition">Rekap Absensi Guru</h4>
                <p class="text-[10px] text-stone-500">Presensi & kehadiran asatidz</p>
            </div>
        </a>

        <a href="{{ route($prefixRoute . '.laporan.rekap-nilai') }}" wire:navigate class="p-4 bg-white border border-stone-200 rounded-2xl shadow-sm hover:border-amber-400 hover:shadow-md transition group flex items-center gap-3.5">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl group-hover:bg-amber-600 group-hover:text-white transition">
                <x-lucide-award class="w-5 h-5" />
            </div>
            <div>
                <h4 class="text-xs font-bold text-stone-800 group-hover:text-amber-600 transition">Laporan Rekap Nilai</h4>
                <p class="text-[10px] text-stone-500">Distribusi nilai sumatif & TP</p>
            </div>
        </a>

        <a href="{{ route($prefixRoute . '.laporan.tunggakan') }}" wire:navigate class="p-4 bg-white border border-stone-200 rounded-2xl shadow-sm hover:border-rose-400 hover:shadow-md transition group flex items-center gap-3.5">
            <div class="p-3 bg-rose-50 text-rose-600 rounded-xl group-hover:bg-rose-600 group-hover:text-white transition">
                <x-lucide-alert-circle class="w-5 h-5" />
            </div>
            <div>
                <h4 class="text-xs font-bold text-stone-800 group-hover:text-rose-600 transition">Laporan Tunggakan</h4>
                <p class="text-[10px] text-stone-500">Daftar tagihan belum lunas</p>
            </div>
        </a>
    </div>

    <!-- RERATA NILAI KELAS -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Performa Rata-Rata Nilai Per Kelas</h3>
            <span class="text-[11px] text-stone-400 font-medium">Semester Aktif</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($kelasAverages as $ka)
                <div class="p-4 bg-stone-50 border border-stone-200 rounded-xl flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-stone-800">Kelas {{ $ka['nama_kelas'] }}</h4>
                        <p class="text-[10px] text-stone-500">Wali: {{ $ka['wali_kelas'] }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-stone-500 font-semibold block">Rata-rata</span>
                        <span class="text-sm font-black text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">{{ $ka['avg'] }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-xs text-stone-400">
                    Belum ada data nilai pada semester aktif.
                </div>
            @endforelse
        </div>
    </div>
</div>
