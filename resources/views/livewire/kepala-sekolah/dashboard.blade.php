<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Executive Monitoring Kepala Sekolah"
        :steps="[
            ['title' => 'Monitoring Eksekutif', 'desc' => 'Ringkasan performa akademik siswa, rasio pengajar, serta mutasi finansial kas sekolah.'],
            ['title' => 'Capaian Per Kelas', 'desc' => 'Amati statistik rata-rata capaian nilai per kelas perwalian untuk mengevaluasi mutu pembelajaran.'],
            ['title' => 'Akses Read-Only', 'desc' => 'Sebagai Pimpinan, akun ini memiliki akses peninjauan tanpa mengubah langsung entri operasional.']
        ]"
    />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Dashboard Pemantauan Kepala Sekolah</h2>
            <p class="text-xs text-stone-500">Ringkasan eksekutif capaian akademis dan arus kas keuangan yayasan (Akses Read-Only).</p>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex justify-between items-center text-stone-500 text-xs">
                <span class="font-semibold">Total Siswa Aktif</span>
                <x-lucide-users class="w-4 h-4 text-indigo-600" />
            </div>
            <p class="text-2xl font-black text-stone-800">{{ $totalSiswa }}</p>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex justify-between items-center text-stone-500 text-xs">
                <span class="font-semibold">Total Guru Aktif</span>
                <x-lucide-user-check class="w-4 h-4 text-emerald-600" />
            </div>
            <p class="text-2xl font-black text-stone-800">{{ $totalGuru }}</p>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex justify-between items-center text-stone-500 text-xs">
                <span class="font-semibold">Total Pemasukan SPP</span>
                <x-lucide-trending-up class="w-4 h-4 text-emerald-600" />
            </div>
            <p class="text-xl font-black text-emerald-700">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex justify-between items-center text-stone-500 text-xs">
                <span class="font-semibold">Total Pengeluaran</span>
                <x-lucide-trending-down class="w-4 h-4 text-rose-600" />
            </div>
            <p class="text-xl font-black text-rose-700">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- RERATA NILAI KELAS -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Performa Rata-Rata Nilai Per Kelas</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($kelasAverages as $ka)
                <div class="p-4 bg-stone-50 border border-stone-200 rounded-xl flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-stone-800">{{ $ka['nama_kelas'] }}</h4>
                        <p class="text-[10px] text-stone-500">Wali: {{ $ka['wali_kelas'] }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-stone-500 font-semibold block">Rata-rata</span>
                        <span class="text-sm font-black text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">{{ $ka['avg'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
