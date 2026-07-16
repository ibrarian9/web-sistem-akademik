<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Dashboard Murid</h2>
            <p class="text-xs text-slate-500">Selamat datang kembali! Berikut adalah ringkasan akademik dan keuangan Anda.</p>
        </div>
        <div class="px-4 py-2 bg-slate-900 border border-slate-800 rounded-2xl">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Kelas Anda</span>
            <span class="text-xs font-bold text-white">Kelas {{ auth()->user()->siswa->kelas->nama_kelas ?? '-' }}</span>
        </div>
    </div>

    <!-- Outstanding Invoices Warning Alert -->
    @if ($hasOutstanding)
        <div class="bg-amber-500/10 border border-amber-500/20 rounded-3xl p-5 flex items-start gap-4">
            <div class="p-2.5 bg-amber-500/10 text-amber-400 rounded-xl">
                <x-lucide-alert-triangle class="w-5 h-5" />
            </div>
            <div class="space-y-1">
                <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider">Tunggakan Administrasi Terdeteksi</h4>
                <p class="text-xs text-slate-350 leading-relaxed">
                    Anda memiliki {{ $pendingInvoicesCount }} tagihan keuangan aktif yang belum dilunasi. Akses rapor nilai semester Anda sementara waktu dikunci demi tertib administrasi yayasan. Silakan lakukan pembayaran pada menu Keuangan.
                </p>
            </div>
        </div>
    @endif

    <!-- Statistic Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Rata-rata Nilai -->
        <div class="bg-slate-900 border border-slate-850 rounded-3xl p-5 flex items-center justify-between shadow-xl">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Rata-rata Nilai</span>
                <span class="text-2xl font-black text-white tracking-tight">{{ $avgGrade ?: '0.00' }}</span>
            </div>
            <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-2xl">
                <x-lucide-award class="w-6 h-6" />
            </div>
        </div>

        <!-- Kehadiran Rate -->
        <div class="bg-slate-900 border border-slate-850 rounded-3xl p-5 flex items-center justify-between shadow-xl">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Kehadiran</span>
                <span class="text-2xl font-black text-white tracking-tight">{{ $attendanceRate }}%</span>
            </div>
            <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-2xl">
                <x-lucide-check-circle class="w-6 h-6" />
            </div>
        </div>

        <!-- Tagihan Aktif -->
        <div class="bg-slate-900 border border-slate-850 rounded-3xl p-5 flex items-center justify-between shadow-xl">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Tagihan SPP</span>
                <span class="text-2xl font-black text-white tracking-tight">{{ $pendingInvoicesCount }} Tagihan</span>
            </div>
            <div class="p-3 bg-rose-500/10 text-rose-400 rounded-2xl">
                <x-lucide-credit-card class="w-6 h-6" />
            </div>
        </div>

        <!-- Kelas Belajar Hari Ini -->
        <div class="bg-slate-900 border border-slate-850 rounded-3xl p-5 flex items-center justify-between shadow-xl">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Jadwal Hari Ini</span>
                <span class="text-2xl font-black text-white tracking-tight">{{ count($todaySchedule) }} Sesi</span>
            </div>
            <div class="p-3 bg-sky-500/10 text-sky-400 rounded-2xl">
                <x-lucide-calendar class="w-6 h-6" />
            </div>
        </div>
    </div>

    <!-- Main Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Today Schedule -->
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white tracking-wide uppercase">Jadwal Pelajaran Hari Ini</h3>
            
            <div class="space-y-3">
                @forelse ($todaySchedule as $sched)
                    <div class="p-4 bg-slate-950/40 border border-slate-850 rounded-2xl flex items-center justify-between">
                        <div class="space-y-1">
                            <h4 class="text-xs font-bold text-white">{{ $sched['mapel'] }}</h4>
                            <span class="text-[10px] text-slate-400 block">Pengajar: {{ $sched['guru'] }}</span>
                        </div>
                        <span class="px-3 py-1 bg-indigo-600/10 border border-indigo-500/20 text-indigo-400 rounded-xl text-[10px] font-bold tracking-wide">
                            {{ $sched['jam'] }}
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-600 text-xs font-medium">
                        <x-lucide-coffee class="w-8 h-8 mx-auto mb-2 text-slate-700" />
                        <span>Tidak ada jadwal pelajaran hari ini.</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Activity Log Timeline -->
        <div class="lg:col-span-1 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white tracking-wide uppercase">Log Aktivitas Terbaru</h3>
            
            <div class="flow-root">
                <ul class="-mb-8">
                    @forelse ($activityLogs as $log)
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-800" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-slate-950 border border-slate-800 flex items-center justify-center text-indigo-400">
                                            <x-lucide-activity class="w-4 h-4" />
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-[11px] text-slate-300">{{ $log['description'] }}</p>
                                        </div>
                                        <div class="text-right text-[10px] whitespace-nowrap text-slate-500">
                                            <time>{{ $log['time'] }}</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <div class="py-8 text-center text-slate-600 text-xs font-medium">
                            <span>Belum ada log aktivitas.</span>
                        </div>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
