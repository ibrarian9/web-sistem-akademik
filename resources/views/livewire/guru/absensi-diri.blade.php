<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Absensi Mandiri Guru"
        :steps="[
            ['title' => 'Check-In Datang', 'desc' => 'Klik tombol Check-In Datang saat baru tiba di sekolah sebelum batas jam toleransi.'],
            ['title' => 'Check-Out Pulang', 'desc' => 'Klik tombol Check-Out Pulang setelah selesai seluruh tugas mengajar/piket hari berjalan.'],
            ['title' => 'Jadwal Piket', 'desc' => 'Bagi guru bertugas piket, jam toleransi masuk akan menyesuaikan jadwal piket sekolah.']
        ]"
    />

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Absensi Mandiri Guru</h2>
            <p class="text-xs text-slate-500">Lakukan pencatatan waktu kehadiran masuk dan pulang mengajar setiap hari kerja.</p>
        </div>
        <div class="px-4 py-2 bg-slate-900 border border-slate-800 rounded-2xl text-right space-y-0.5">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Jam Masuk Toleransi</span>
            <span class="text-xs font-bold text-white block">{{ $targetJamMasuk }} WIB (Toleransi {{ $toleransiMenit }}m)</span>
            <span class="text-[10px] {{ $hasPiketToday ? 'text-emerald-400 font-semibold' : 'text-slate-400' }} block">
                {{ $hasPiketToday ? 'Tugas Piket Hari Ini' : 'Normal (Tanpa Piket)' }}
            </span>
        </div>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Interactive Attendance Buttons -->
        <div class="lg:col-span-1 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6 flex flex-col justify-between">
            <div class="space-y-4">
                <h3 class="text-base font-bold text-white tracking-wide border-b border-slate-850 pb-2">Status Hari Ini</h3>
                
                <div class="p-4 bg-slate-950/60 border border-slate-850 rounded-2xl space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400">Status Kehadiran</span>
                        <span class="font-bold uppercase tracking-wider
                            {{ $statusToday === 'hadir' ? 'text-emerald-400' : '' }}
                            {{ $statusToday === 'telat' ? 'text-amber-400' : '' }}
                            {{ $statusToday === 'Belum Hadir' ? 'text-slate-500' : '' }}
                        ">
                            {{ $statusToday }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400">Jam Datang</span>
                        <span class="text-white font-semibold">{{ $waktu_datang ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400">Jam Pulang</span>
                        <span class="text-white font-semibold">{{ $waktu_pulang ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="space-y-3 pt-6">
                @if (!$waktu_datang)
                    <button wire:click="checkIn" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold tracking-wide transition duration-200 shadow-lg shadow-indigo-600/10">
                        Check-In Datang
                    </button>
                @elseif (!$waktu_pulang)
                    <button wire:click="checkOut" class="w-full py-3 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold tracking-wide transition duration-200 shadow-lg shadow-sky-600/10">
                        Check-Out Pulang
                    </button>
                @else
                    <div class="w-full py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-center rounded-xl text-xs font-bold uppercase tracking-wide">
                        Selesai Absensi Hari Ini
                    </div>
                @endif
            </div>
        </div>

        <!-- Attendance History -->
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <h3 class="text-base font-bold text-white tracking-wide">Riwayat Kehadiran (15 Hari Terakhir)</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Datang</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Pulang</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @forelse ($history as $hist)
                            <tr>
                                <td class="py-3 text-xs text-white font-semibold">
                                    {{ date('d-m-Y', strtotime($hist['tanggal'])) }}
                                </td>
                                <td class="py-3 text-xs text-slate-350">
                                    {{ $hist['waktu_datang'] ? date('H:i', strtotime($hist['waktu_datang'])) : '-' }}
                                </td>
                                <td class="py-3 text-xs text-slate-350">
                                    {{ $hist['waktu_pulang'] ? date('H:i', strtotime($hist['waktu_pulang'])) : '-' }}
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        {{ $hist['status'] === 'hadir' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                        {{ $hist['status'] === 'telat' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                        {{ !in_array($hist['status'], ['hadir', 'telat']) ? 'bg-slate-500/10 text-slate-400 border border-slate-500/20' : '' }}
                                    ">
                                        {{ $hist['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-500 font-semibold">
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
