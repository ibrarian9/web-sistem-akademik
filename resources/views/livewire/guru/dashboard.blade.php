<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Panduan Portal Guru & Wali Kelas"
        :steps="[
            ['title' => 'Absensi Mandiri', 'desc' => 'Lakukan Check-In saat tiba di sekolah dan Check-Out sebelum pulang pada menu Absensi Mandiri.'],
            ['title' => 'Input Nilai Siswa', 'desc' => 'Isi nilai mata pelajaran umum/tahfidz siswa serta atur pembobotan komponen pada menu Bobot Nilai.'],
            ['title' => 'Cetak & Terbitkan Rapor', 'desc' => 'Khusus Wali Kelas: verifikasi capaian nilai, absensi, dan sikap sebelum menerbitkan rapor digital.']
        ]"
    />

    <!-- Welcome Header Card (Light Theme Standard) -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-stone-900 tracking-tight">Selamat Datang, {{ auth()->user()->nama }}</h2>
            <p class="text-xs text-stone-600 font-semibold mt-1">Panel pengajar &amp; wali kelas akademis sekolah.</p>
        </div>
        <div class="text-right bg-stone-50 border border-stone-200 p-3 rounded-xl">
            <p class="text-xs text-stone-800 font-bold">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
            <p class="text-[10px] text-emerald-700 font-black uppercase tracking-wider">Tahun Ajaran Aktif</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card title="Total Kelas Diajar" value="{{ $totalKelas }}" icon="layers" trend="" color="indigo" />
        <x-stat-card title="Mata Pelajaran" value="{{ $totalMapel }}" icon="book-open" trend="" color="emerald" />
        <x-stat-card title="Jadwal Hari Ini" value="{{ $jadwalHariIni }}" icon="calendar" trend="" color="amber" />
        
        <!-- Custom Attendance Stat Card -->
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm flex items-center justify-between relative overflow-hidden group">
            <div class="space-y-1">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Absensi Mandiri</span>
                <h3 class="text-2xl font-black text-stone-900 tracking-tight">{{ $statusAbsensi }}</h3>
                @if ($waktuCheckIn)
                    <p class="text-[10px] text-emerald-700 font-bold">Datang pukul {{ $waktuCheckIn }}</p>
                @else
                    <p class="text-[10px] text-rose-600 font-bold">Belum melakukan check-in</p>
                @endif
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 border border-sky-200 flex items-center justify-center text-sky-700 font-bold">
                <x-lucide-clock class="w-6 h-6" />
            </div>
        </div>
    </div>

    <!-- Content Split Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Today's Schedule -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">Jadwal Mengajar Hari Ini</h3>
                <span class="text-[10px] text-emerald-800 bg-emerald-100 border border-emerald-200 px-2.5 py-0.5 rounded font-black uppercase">Mingguan</span>
            </div>

            <div class="space-y-3">
                @forelse ($schedules as $s)
                    <div class="flex items-center justify-between p-4 bg-stone-50 border border-stone-200 rounded-xl hover:border-emerald-300 transition">
                        <div class="space-y-1">
                            <h4 class="text-xs font-extrabold text-stone-900">{{ $s['mapel'] }}</h4>
                            <p class="text-xs text-emerald-700 font-bold">{{ $s['kelas'] }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-100 text-emerald-900 border border-emerald-300 text-xs font-bold">
                                <x-lucide-clock class="w-3.5 h-3.5 text-emerald-700" />
                                {{ $s['jam'] }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-stone-500 font-semibold text-xs italic">
                        Tidak ada jadwal mengajar untuk hari ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions Panel (Standard UI Buttons) -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider border-b border-stone-200 pb-3">Tindakan Cepat Guru</h3>
            
            <div class="grid grid-cols-1 gap-3">
                <a href="{{ route('guru.kurikulum-merdeka') }}" class="flex items-center gap-3 p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 hover:border-emerald-300 rounded-xl text-stone-900 transition duration-150">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-800 shrink-0">
                        <x-lucide-layers class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Setup Kurikulum Merdeka</h4>
                        <p class="text-[10px] text-stone-500 font-medium">Kelola Bab, TP &amp; Auto-Narasi.</p>
                    </div>
                </a>

                <a href="{{ route('guru.input-sumatif') }}" class="flex items-center gap-3 p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 hover:border-emerald-300 rounded-xl text-stone-900 transition duration-150">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-800 shrink-0">
                        <x-lucide-edit-3 class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Input Nilai Sumatif TP &amp; SAS</h4>
                        <p class="text-[10px] text-stone-500 font-medium">Form matriks nilai &amp; auto-narasi.</p>
                    </div>
                </a>

                <a href="{{ route('guru.input-tahfidz') }}" class="flex items-center gap-3 p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 hover:border-amber-300 rounded-xl text-stone-900 transition duration-150">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 border border-amber-300 flex items-center justify-center text-amber-900 shrink-0">
                        <x-lucide-award class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Input Setoran Tahfizh</h4>
                        <p class="text-[10px] text-stone-500 font-medium">Setoran hafalan surah &amp; tajwid.</p>
                    </div>
                </a>

                <a href="{{ route('guru.penilaian-p5') }}" class="flex items-center gap-3 p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 hover:border-cyan-300 rounded-xl text-stone-900 transition duration-150">
                    <div class="w-9 h-9 rounded-lg bg-cyan-100 border border-cyan-300 flex items-center justify-center text-cyan-900 shrink-0">
                        <x-lucide-star class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Penilaian Kokurikuler P5</h4>
                        <p class="text-[10px] text-stone-500 font-medium">Penilaian kualitatif P5 (1-klik).</p>
                    </div>
                </a>
            </div>

            <!-- Piket Duty Status Notice -->
            <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-xl flex items-center justify-between text-xs">
                <div class="space-y-0.5">
                    <span class="font-extrabold text-stone-900 block">Tugas Piket Hari Ini:</span>
                    <span class="text-[11px] {{ $hasPiketHariIni ? 'text-emerald-700 font-bold' : 'text-stone-500 font-semibold' }}">
                        {{ $hasPiketHariIni ? 'Terdapat Jadwal Piket (Masuk: ' . $targetJamMasuk . ' WIB)' : 'Tidak Ada Jadwal Piket (Masuk: ' . $targetJamMasuk . ' WIB)' }}
                    </span>
                </div>
                <div class="p-2 {{ $hasPiketHariIni ? 'bg-emerald-100 border border-emerald-300 text-emerald-800' : 'bg-stone-200 text-stone-600' }} rounded-xl">
                    <x-lucide-shield-check class="w-5 h-5" />
                </div>
            </div>
        </div>
    </div>
</div>
