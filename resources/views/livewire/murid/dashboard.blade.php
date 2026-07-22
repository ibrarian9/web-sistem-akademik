<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold text-stone-900 tracking-tight">Dashboard Murid</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                    Akun Siswa Aktif
                </span>
            </div>
            <p class="text-xs text-stone-500 mt-1">Selamat datang kembali, <strong>{{ auth()->user()->nama }}</strong>! Berikut adalah ringkasan perkembangan Anda.</p>
        </div>
        <div class="flex items-center gap-3 px-4 py-2.5 bg-white border border-stone-200 rounded-2xl shadow-sm">
            <div class="w-8 h-8 rounded-xl bg-green-50 border border-green-200 flex items-center justify-center text-green-700 font-bold text-xs">
                <x-lucide-graduation-cap class="w-4.5 h-4.5" />
            </div>
            <div>
                <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Kelas / NISN</span>
                <span class="text-xs font-bold text-stone-800">
                    Kelas {{ auth()->user()->siswa->kelas->nama_kelas ?? '-' }} (NISN: {{ auth()->user()->siswa->nisn ?? '-' }})
                </span>
            </div>
        </div>
    </div>

    <!-- Outstanding Invoices Warning Alert -->
    @if ($hasOutstanding)
        <div class="bg-amber-50 border-2 border-amber-300 rounded-2xl p-5 flex items-start gap-4 shadow-sm">
            <div class="p-2.5 bg-amber-600 text-white rounded-xl shrink-0 shadow-sm">
                <x-lucide-alert-triangle class="w-6 h-6" />
            </div>
            <div class="space-y-1">
                <h4 class="text-xs font-bold text-amber-900 uppercase tracking-wider">Tunggakan Administrasi Terdeteksi</h4>
                <p class="text-xs text-amber-800 font-medium leading-relaxed">
                    Anda memiliki <strong>{{ $pendingInvoicesCount }} tagihan SPP/keuangan aktif</strong> yang belum dilunasi. 
                    Akses penerbitan rapor nilai semester sementara dikunci demi tertib administrasi yayasan. Silakan lakukan pembayaran pada menu Keuangan.
                </p>
            </div>
        </div>
    @endif

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Panduan Portal Belajar Murid & Orang Tua"
        :steps="[
            ['title' => 'Pantau Presensi & Jadwal', 'desc' => 'Periksa persentase kehadiran harian dan jadwal pelajaran aktif semester berjalan.'],
            ['title' => 'Cek Nilai & Rapor Digital', 'desc' => 'Lihat nilai per komponen mata pelajaran serta unduh Rapor Akademik / Tahfizh.'],
            ['title' => 'Pembayaran SPP', 'desc' => 'Pantau sisa tagihan SPP dan riwayat kuitansi pembayaran sekolah.']
        ]"
        notes="Akses penerbitan rapor otomatis sementara dikunci jika terdapat tunggakan administrasi keuangan."
    />

    <!-- Statistic Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Rata-rata Nilai -->
        <a href="{{ route('murid.rapor') }}" class="bg-white border border-stone-200 hover:border-indigo-400 transition rounded-2xl p-5 flex items-center justify-between shadow-sm group">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Rata-rata Nilai</span>
                <span class="text-2xl font-black text-stone-900 tracking-tight group-hover:text-indigo-600 transition">{{ $avgGrade ?: '0.00' }}</span>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl group-hover:scale-110 transition duration-300">
                <x-lucide-award class="w-6 h-6" />
            </div>
        </a>

        <!-- Kehadiran Rate -->
        <a href="{{ route('murid.kehadiran') }}" class="bg-white border border-stone-200 hover:border-green-400 transition rounded-2xl p-5 flex items-center justify-between shadow-sm group">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Kehadiran Saya</span>
                <span class="text-2xl font-black text-stone-900 tracking-tight group-hover:text-green-600 transition">{{ $attendanceRate }}%</span>
            </div>
            <div class="p-3 bg-green-50 text-green-600 rounded-xl group-hover:scale-110 transition duration-300">
                <x-lucide-check-circle class="w-6 h-6" />
            </div>
        </a>

        <!-- Tagihan Aktif -->
        <a href="{{ route('murid.tagihan') }}" class="bg-white border border-stone-200 hover:border-red-400 transition rounded-2xl p-5 flex items-center justify-between shadow-sm group">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Tagihan SPP</span>
                <span class="text-2xl font-black text-stone-900 tracking-tight group-hover:text-red-600 transition">{{ $pendingInvoicesCount }} Tagihan</span>
            </div>
            <div class="p-3 bg-red-50 text-red-600 rounded-xl group-hover:scale-110 transition duration-300">
                <x-lucide-credit-card class="w-6 h-6" />
            </div>
        </a>

        <!-- Kelas Belajar Hari Ini -->
        <a href="{{ route('murid.jadwal') }}" class="bg-white border border-stone-200 hover:border-sky-400 transition rounded-2xl p-5 flex items-center justify-between shadow-sm group">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Jadwal Pelajaran Hari Ini</span>
                <span class="text-2xl font-black text-stone-900 tracking-tight group-hover:text-sky-600 transition">{{ count($todaySchedule) }} Sesi</span>
            </div>
            <div class="p-3 bg-sky-50 text-sky-600 rounded-xl group-hover:scale-110 transition duration-300">
                <x-lucide-calendar class="w-6 h-6" />
            </div>
        </a>
    </div>

    <!-- Main Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Today Schedule -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-stone-900 tracking-wide uppercase flex items-center gap-2">
                    <x-lucide-clock class="w-4 h-4 text-sky-600" />
                    Jadwal Pelajaran Hari Ini
                </h3>
                <a href="{{ route('murid.jadwal') }}" class="text-xs font-bold text-sky-700 hover:underline">Lihat Semua Jadwal &rarr;</a>
            </div>
            
            <div class="space-y-3">
                @forelse ($todaySchedule as $sched)
                    <div class="p-4 bg-stone-50 border border-stone-200 rounded-xl flex items-center justify-between hover:bg-stone-100/80 transition">
                        <div class="space-y-1">
                            <h4 class="text-xs font-bold text-stone-900">{{ $sched['mapel'] }}</h4>
                            <span class="text-xs text-stone-600 block font-medium">Pengajar: {{ $sched['guru'] }}</span>
                        </div>
                        <span class="px-3 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-xl text-xs font-bold tracking-wide">
                            {{ $sched['jam'] }}
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-stone-500 text-xs font-medium space-y-2">
                        <x-lucide-coffee class="w-8 h-8 mx-auto text-stone-400" />
                        <span>Tidak ada sesi mata pelajaran hari ini. Selamat beristirahat!</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Activity Log Timeline -->
        <div class="lg:col-span-1 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-stone-900 tracking-wide uppercase flex items-center gap-2">
                    <x-lucide-activity class="w-4 h-4 text-indigo-600" />
                    Aktivitas Terbaru
                </h3>
                <a href="{{ route('murid.riwayat-aktivitas') }}" class="text-xs font-bold text-indigo-700 hover:underline">Detail &rarr;</a>
            </div>
            
            <div class="flow-root">
                <ul class="-mb-8">
                    @forelse ($activityLogs as $log)
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-stone-200" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-stone-100 border border-stone-300 flex items-center justify-center text-indigo-600">
                                            <x-lucide-activity class="w-4 h-4" />
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-xs font-semibold text-stone-800">{{ $log['description'] }}</p>
                                        </div>
                                        <div class="text-right text-[10px] whitespace-nowrap text-stone-500 font-medium">
                                            <time>{{ $log['time'] }}</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <div class="py-8 text-center text-stone-500 text-xs font-medium">
                            <span>Belum ada log aktivitas pencatatan.</span>
                        </div>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
