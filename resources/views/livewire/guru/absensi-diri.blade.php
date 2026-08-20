<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Kehadiran & Presensi Guru"
        :steps="[
            ['title' => 'Pencatatan Presensi', 'desc' => 'Presensi kehadiran harian dicatat dan dikelola secara terpusat oleh Bagian Tata Usaha.'],
            ['title' => 'Jam Masuk & Toleransi', 'desc' => 'Jam batas masuk kehadiran adalah pukul ' . $targetJamMasuk . ' WIB dengan toleransi ' . $toleransiMenit . ' menit.'],
            ['title' => 'Tugas Piket', 'desc' => 'Guru yang bertugas piket hari berjalan akan mendapatkan penyesuaian jadwal pada sistem.']
        ]"
    />

    <!-- Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                KEPEGAWAIAN GURU
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Presensi &amp; Kehadiran Guru</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Pantau status kehadiran harian Anda yang tercatat secara resmi oleh Tata Usaha.</p>
        </div>
        <div class="text-right bg-stone-50 border border-stone-200 px-4 py-3 rounded-xl space-y-0.5">
            <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Jam Masuk Toleransi</span>
            <span class="text-xs font-black text-stone-900 block">{{ $targetJamMasuk }} WIB (Toleransi {{ $toleransiMenit }}m)</span>
            <span class="text-[10px] font-bold {{ $hasPiketToday ? 'text-emerald-700' : 'text-stone-500' }} block">
                {{ $hasPiketToday ? '● Tugas Piket Hari Ini' : 'Normal (Tanpa Piket)' }}
            </span>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Status Today Card -->
        <div class="lg:col-span-1 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">Status Kehadiran Hari Ini</h3>
                    <span class="text-[10px] font-bold text-stone-500">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</span>
                </div>
                
                <div class="p-4 bg-stone-50 border border-stone-200 rounded-xl space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-stone-600 font-semibold">Status Presensi</span>
                        <span class="font-extrabold uppercase px-2.5 py-0.5 rounded-full text-[10px]
                            {{ $statusToday === 'hadir' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : '' }}
                            {{ $statusToday === 'telat' ? 'bg-amber-100 text-amber-800 border border-amber-300' : '' }}
                            {{ !in_array($statusToday, ['hadir', 'telat']) ? 'bg-stone-200 text-stone-700' : '' }}
                        ">
                            {{ $statusToday }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-stone-600 font-semibold">Jam Datang</span>
                        <span class="text-stone-900 font-bold">{{ $waktu_datang ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-stone-600 font-semibold">Jam Pulang</span>
                        <span class="text-stone-900 font-bold">{{ $waktu_pulang ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Action info box -->
            <div class="space-y-3 pt-2">
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-950 rounded-xl text-xs space-y-1.5 text-center shadow-xs">
                    <span class="font-extrabold block uppercase tracking-wide text-emerald-900 flex items-center justify-center gap-1.5">
                        <x-lucide-shield-check class="w-4 h-4 text-emerald-700" />
                        <span>Pencatatan Terpusat oleh TU</span>
                    </span>
                    <p class="text-[11px] text-emerald-800 font-medium leading-relaxed">Presensi kehadiran dikelola &amp; diinput oleh Tata Usaha. Data kehadiran tersinkronisasi otomatis dengan sistem penggajian.</p>
                </div>
            </div>
        </div>

        <!-- Attendance History -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">Riwayat Kehadiran (15 Hari Terakhir)</h3>
                <span class="text-[10px] bg-stone-100 text-stone-700 font-bold px-2.5 py-0.5 rounded-full border border-stone-200">Terbaru</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-stone-50 border-b border-stone-200 text-stone-600 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="p-3 border-r border-stone-200">Tanggal</th>
                            <th class="p-3 border-r border-stone-200">Datang</th>
                            <th class="p-3 border-r border-stone-200">Pulang</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($history as $hist)
                            <tr class="hover:bg-stone-50 transition">
                                <td class="p-3 border-r border-stone-200 text-stone-900 font-bold">
                                    {{ date('d-m-Y', strtotime($hist['tanggal'])) }}
                                </td>
                                <td class="p-3 border-r border-stone-200 text-stone-700 font-semibold">
                                    {{ $hist['waktu_datang'] ? date('H:i', strtotime($hist['waktu_datang'])) : '-' }}
                                </td>
                                <td class="p-3 border-r border-stone-200 text-stone-700 font-semibold">
                                    {{ $hist['waktu_pulang'] ? date('H:i', strtotime($hist['waktu_pulang'])) : '-' }}
                                </td>
                                <td class="p-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                        {{ $hist['status'] === 'hadir' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : '' }}
                                        {{ $hist['status'] === 'telat' ? 'bg-amber-100 text-amber-800 border border-amber-300' : '' }}
                                        {{ !in_array($hist['status'], ['hadir', 'telat']) ? 'bg-stone-100 text-stone-700 border border-stone-300' : '' }}
                                    ">
                                        {{ $hist['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-stone-500 font-semibold">
                                    Belum ada riwayat kehadiran tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
