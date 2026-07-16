<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Kehadiran Saya</h2>
        <p class="text-xs text-slate-500">Pantau rangkuman kehadiran dan absensi harian kelas Anda.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Kehadiran Rate -->
        <div class="bg-slate-900 border border-slate-850 rounded-3xl p-5 flex items-center justify-between shadow-xl">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Persentase Hadir</span>
                <span class="text-2xl font-black text-white tracking-tight">{{ $rate }}%</span>
            </div>
            <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-2xl">
                <x-lucide-activity class="w-6 h-6" />
            </div>
        </div>

        <!-- Total Hadir -->
        <div class="bg-slate-900 border border-slate-850 rounded-3xl p-5 flex items-center justify-between shadow-xl">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Hadir</span>
                <span class="text-2xl font-black text-white tracking-tight">{{ $totalHadir }} Hari</span>
            </div>
            <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-2xl">
                <x-lucide-check-circle class="w-6 h-6" />
            </div>
        </div>

        <!-- Total Izin -->
        <div class="bg-slate-900 border border-slate-850 rounded-3xl p-5 flex items-center justify-between shadow-xl">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Izin / Sakit</span>
                <span class="text-2xl font-black text-white tracking-tight">{{ $totalIzin }} Hari</span>
            </div>
            <div class="p-3 bg-amber-500/10 text-amber-400 rounded-2xl">
                <x-lucide-info class="w-6 h-6" />
            </div>
        </div>

        <!-- Total Tidak Hadir -->
        <div class="bg-slate-900 border border-slate-850 rounded-3xl p-5 flex items-center justify-between shadow-xl">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Tidak Hadir (Alpa)</span>
                <span class="text-2xl font-black text-white tracking-tight">{{ $totalTidakHadir }} Hari</span>
            </div>
            <div class="p-3 bg-rose-500/10 text-rose-400 rounded-2xl">
                <x-lucide-alert-circle class="w-6 h-6" />
            </div>
        </div>
    </div>

    <!-- Attendance Logs History Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Riwayat Absensi Harian</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800">
                        <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse ($history as $h)
                        <tr>
                            <td class="py-3.5 text-xs text-white font-semibold">{{ $h['tanggal'] }}</td>
                            <td class="py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                    {{ $h['status'] === 'hadir' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                    {{ $h['status'] === 'izin' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                    {{ $h['status'] === 'tidak_hadir' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' }}
                                ">
                                    {{ $h['status'] === 'tidak_hadir' ? 'Alpa / Tidak Hadir' : $h['status'] }}
                                </span>
                            </td>
                            <td class="py-3.5 text-xs text-slate-400">{{ $h['catatan'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-500 font-semibold">
                                Belum ada riwayat kehadiran tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
