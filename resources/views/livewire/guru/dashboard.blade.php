<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Panduan Portal Guru & Wali Kelas"
        :steps="[
            ['title' => 'Absensi Mandiri', 'desc' => 'Lakukan Check-In saat tiba di sekolah dan Check-Out sebelum pulang pada menu Absensi Mandiri.'],
            ['title' => 'Input Nilai Siswa', 'desc' => 'Isi nilai mata pelajaran umum/tahfidz siswa serta atur pembobotan komponen pada menu Bobot Nilai.'],
            ['title' => 'Cetak & Terbitkan Rapor', 'desc' => 'Khusus Wali Kelas: verifikasi capaian nilai, absensi, dan sikap sebelum menerbitkan rapor digital.']
        ]"
    />

    <!-- Welcome Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Selamat Datang, {{ auth()->user()->nama }}</h2>
            <p class="text-xs text-slate-500">Panel pengajar & wali kelas akademis.</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-slate-400 font-medium">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Tahun Ajaran Aktif</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card title="Total Kelas Diajar" value="{{ $totalKelas }}" icon="layers" trend="" color="indigo" />
        <x-stat-card title="Mata Pelajaran" value="{{ $totalMapel }}" icon="book-open" trend="" color="emerald" />
        <x-stat-card title="Jadwal Hari Ini" value="{{ $jadwalHariIni }}" icon="calendar" trend="" color="amber" />
        
        <!-- Custom Attendance Stat Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl flex items-center justify-between relative overflow-hidden group">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Absensi Mandiri</span>
                <h3 class="text-2xl font-black text-white tracking-tight">{{ $statusAbsensi }}</h3>
                @if ($waktuCheckIn)
                    <p class="text-[10px] text-emerald-400 font-semibold">Datang pukul {{ $waktuCheckIn }}</p>
                @else
                    <p class="text-[10px] text-rose-400 font-semibold">Belum melakukan check-in</p>
                @endif
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 transition-colors duration-200">
                <x-lucide-clock class="w-6 h-6" />
            </div>
        </div>
    </div>

    <!-- Content Split Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Today's Schedule -->
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white tracking-wide">Jadwal Mengajar Hari Ini</h3>
                <span class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider">Mingguan</span>
            </div>

            <div class="space-y-3">
                @forelse ($schedules as $s)
                    <div class="flex items-center justify-between p-4 bg-slate-950/40 border border-slate-850 rounded-2xl">
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-white">{{ $s['mapel'] }}</h4>
                            <p class="text-xs text-indigo-400 font-semibold">{{ $s['kelas'] }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-xs font-bold">
                                <x-lucide-clock class="w-3.5 h-3.5" />
                                {{ $s['jam'] }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-500 font-medium">
                        Tidak ada jadwal mengajar untuk hari ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions Panel -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <h3 class="text-base font-bold text-white tracking-wide">Tindakan Cepat</h3>
            
            <div class="grid grid-cols-1 gap-3">
                <a href="{{ route('guru.absensi-diri') }}" class="flex items-center gap-3 p-4 bg-slate-950/40 hover:bg-slate-905 border border-slate-850 hover:border-slate-800 rounded-2xl text-white transition-all duration-200 group">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                        <x-lucide-clock class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold">Absensi Mandiri</h4>
                        <p class="text-[10px] text-slate-500">Check-in / check-out kehadiran guru.</p>
                    </div>
                </a>

                <a href="{{ route('guru.input-nilai') }}" class="flex items-center gap-3 p-4 bg-slate-950/40 hover:bg-slate-905 border border-slate-850 hover:border-slate-800 rounded-2xl text-white transition-all duration-200 group">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <x-lucide-edit-3 class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold">Input Nilai Siswa</h4>
                        <p class="text-[10px] text-slate-500">Input nilai UH, PTS, PAS, Hafalan, Sikap.</p>
                    </div>
                </a>

                <a href="{{ route('guru.absensi-siswa') }}" class="flex items-center gap-3 p-4 bg-slate-950/40 hover:bg-slate-905 border border-slate-850 hover:border-slate-800 rounded-2xl text-white transition-all duration-200 group">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                        <x-lucide-clipboard class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold">Absensi Siswa</h4>
                        <p class="text-[10px] text-slate-500">Input kehadiran harian siswa.</p>
                    </div>
                <a href="{{ route('guru.kelola-rapor') }}" class="flex items-center gap-3 p-4 bg-slate-950/40 hover:bg-slate-905 border border-slate-850 hover:border-slate-800 rounded-2xl text-white transition-all duration-200 group">
                    <div class="w-9 h-9 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                        <x-lucide-book-open class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold">Terbitkan &amp; Cetak Rapor</h4>
                        <p class="text-[10px] text-slate-500">Wali kelas: terbitkan rapor siswa.</p>
                    </div>
                </a>
            </div>

            <!-- Piket Duty Status Notice -->
            <div class="p-4 bg-slate-950/60 border border-slate-800 rounded-2xl flex items-center justify-between text-xs">
                <div class="space-y-0.5">
                    <span class="font-bold text-white block">Tugas Piket Hari Ini:</span>
                    <span class="text-[11px] {{ $hasPiketHariIni ? 'text-emerald-400 font-semibold' : 'text-slate-400' }}">
                        {{ $hasPiketHariIni ? 'Terdapat Jadwal Piket (Masuk: ' . $targetJamMasuk . ' WIB)' : 'Tidak Ada Jadwal Piket (Masuk: ' . $targetJamMasuk . ' WIB)' }}
                    </span>
                </div>
                <div class="p-2 {{ $hasPiketHariIni ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-800 text-slate-400' }} rounded-xl">
                    <x-lucide-shield-check class="w-5 h-5" />
                </div>
            </div>
        </div>
    </div>
</div>
