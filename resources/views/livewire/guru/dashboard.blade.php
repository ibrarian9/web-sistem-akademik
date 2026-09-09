<div class="space-y-6 font-sans">
    @if ($isGuruPendamping)
        <!-- Info & Tutorial Box for Guru Pendamping -->
        <x-info-tutorial-box 
            title="Panduan Guru Pendamping Khusus (Shadow Teacher)"
            :steps="[
                ['title' => 'Catatan Berkala', 'desc' => 'Input pengamatan harian/mingguan perkembangan murid berkebutuhan khusus (ABK) yang didampingi.'],
                ['title' => '7 Aspek Kualitatif', 'desc' => 'Terapkan skala kualitatif (BB, MB, BSH, BSB) tanpa angka numerik untuk mengukur kemandirian & interaksi.'],
                ['title' => 'Rekap & Cetak Laporan', 'desc' => 'Pantau rekap capaian tengah/akhir semester dan cetak lembar evaluasi resmi beserta tindak lanjut.']
            ]"
        />

        <!-- Welcome Header Card -->
        <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-extrabold text-stone-900 tracking-tight">Selamat Datang, {{ auth()->user()->nama }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">Guru Pendamping</span>
                </div>
                <p class="text-xs text-stone-600 font-semibold mt-1">Panel pendampingan khusus anak berkebutuhan khusus (ABK) di kelas umum & tahfizh.</p>
            </div>
            <div class="text-right bg-stone-50 border border-stone-200 p-3 rounded-xl">
                <p class="text-xs text-stone-800 font-bold">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                <p class="text-[10px] text-emerald-700 font-black uppercase tracking-wider">Tahun Ajaran Aktif</p>
            </div>
        </div>

        <!-- Stats Grid for Pendamping -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card title="Siswa Didampingi" value="{{ $totalSiswaDidampingi }}" icon="users" trend="" color="indigo" />
            <x-stat-card title="Catatan Bulan Ini" value="{{ $totalCatatanBulanIni }}" icon="file-text" trend="" color="emerald" />
            <x-stat-card title="Jam Masuk Target" value="{{ $targetJamMasuk }} WIB" icon="calendar" trend="" color="amber" />
            
            <!-- Attendance Stat Card -->
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

        <!-- Split Layout for Pendamping -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Siswa Didampingi & Catatan Terbaru -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Siswa Didampingi Card -->
                <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                        <div class="flex items-center gap-2">
                            <x-lucide-users class="w-4 h-4 text-emerald-600" />
                            <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">Murid yang Didampingi</h3>
                        </div>
                        <a href="{{ route('guru.pendampingan') }}" wire:navigate class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline">
                            Buka Catatan Pendampingan &rarr;
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($siswaDidampingiList as $sd)
                            <div class="flex items-center justify-between p-4 bg-stone-50 border border-stone-200 rounded-xl hover:border-emerald-300 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-800 font-bold text-sm">
                                        {{ strtoupper(substr($sd['nama'], 0, 2)) }}
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-extrabold text-stone-900">{{ $sd['nama'] }}</h4>
                                        <p class="text-[11px] text-stone-500">NISN: {{ $sd['nisn'] ?? '-' }} &bull; <span class="text-emerald-700 font-semibold">{{ $sd['kelas'] }}</span></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
                                        {{ $sd['catatan_count'] }} Catatan
                                    </span>
                                    <a href="{{ route('guru.pendampingan') }}" wire:navigate class="px-3 py-1 bg-white hover:bg-stone-100 border border-stone-300 text-stone-700 font-bold text-xs rounded-lg transition">
                                        Input Catatan
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="py-10 text-center text-stone-500 font-semibold text-xs italic">
                                Belum ada siswa yang diplot untuk didampingi oleh akun Anda. Hubungi Tata Usaha / Super Admin untuk penugasan shadow teacher.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Catatan Pengamatan Terbaru -->
                <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                        <div class="flex items-center gap-2">
                            <x-lucide-file-text class="w-4 h-4 text-purple-600" />
                            <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">Catatan Pendampingan Terbaru</h3>
                        </div>
                        <span class="text-[10px] text-purple-800 bg-purple-100 border border-purple-200 px-2.5 py-0.5 rounded font-black uppercase">5 Terkini</span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($catatanTerbaru as $ct)
                            <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-xl hover:border-stone-300 transition space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-extrabold text-stone-900">{{ $ct['siswa']['nama_lengkap'] ?? '-' }}</span>
                                        <span class="text-[10px] text-stone-400">&bull;</span>
                                        <span class="text-[10px] text-stone-500 font-semibold">{{ \Carbon\Carbon::parse($ct['tanggal'])->isoFormat('D MMM Y') }}</span>
                                    </div>
                                    <div>
                                        @php
                                            $skalaColor = match($ct['skala']) {
                                                'BSB' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                                'BSH' => 'bg-blue-100 text-blue-800 border-blue-300',
                                                'MB' => 'bg-amber-100 text-amber-800 border-amber-300',
                                                'BB' => 'bg-rose-100 text-rose-800 border-rose-300',
                                                default => 'bg-stone-100 text-stone-800 border-stone-300',
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black border {{ $skalaColor }}">
                                            {{ $ct['skala'] }} - {{ $ct['hasil_perkembangan'] }}
                                        </span>
                                    </div>
                                </div>
                                <p class="text-xs text-stone-600 line-clamp-2">{{ $ct['catatan'] }}</p>
                                <div class="flex items-center gap-2 pt-1 border-t border-stone-200 text-[10px] text-stone-500">
                                    <span class="font-bold text-purple-700">Aspek: {{ $ct['aspek_pengamatan'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-stone-500 font-semibold text-xs italic">
                                Belum ada catatan perkembangan yang dimasukkan.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column: Quick Actions -->
            <div class="space-y-4">
                <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider border-b border-stone-200 pb-3">Tindakan Guru Pendamping</h3>
                    
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('guru.pendampingan') }}" wire:navigate class="flex items-center gap-3 p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 hover:border-emerald-300 rounded-xl text-stone-900 transition duration-150">
                            <div class="w-9 h-9 rounded-lg bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-800 shrink-0">
                                <x-lucide-edit-3 class="w-4 h-4" />
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-stone-900">Catatan Pendampingan</h4>
                                <p class="text-[10px] text-stone-500 font-medium">Input observasi 7 aspek perkembangan kualitatif.</p>
                            </div>
                        </a>

                        <a href="{{ route('guru.absensi-siswa') }}" wire:navigate class="flex items-center gap-3 p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 hover:border-blue-300 rounded-xl text-stone-900 transition duration-150">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 border border-blue-300 flex items-center justify-center text-blue-800 shrink-0">
                                <x-lucide-check-square class="w-4 h-4" />
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <h4 class="text-xs font-bold text-stone-900">Lihat Absensi Siswa</h4>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-stone-200 text-stone-700">Hanya Lihat</span>
                                </div>
                                <p class="text-[10px] text-stone-500 font-medium">Pantau kehadiran harian siswa dampingan.</p>
                            </div>
                        </a>

                        <a href="{{ route('guru.piket') }}" wire:navigate class="flex items-center gap-3 p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 hover:border-emerald-300 rounded-xl text-stone-900 transition duration-150">
                            <div class="w-9 h-9 rounded-lg bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-800 shrink-0">
                                <x-lucide-shield-check class="w-4 h-4" />
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-stone-900">Jadwal Piket</h4>
                                <p class="text-[10px] text-stone-500 font-medium">Lihat jadwal penugasan piket guru harian.</p>
                            </div>
                        </a>

                        <a href="{{ route('guru.absensi-diri') }}" wire:navigate class="flex items-center gap-3 p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 hover:border-amber-300 rounded-xl text-stone-900 transition duration-150">
                            <div class="w-9 h-9 rounded-lg bg-amber-100 border border-amber-300 flex items-center justify-center text-amber-900 shrink-0">
                                <x-lucide-clock class="w-4 h-4" />
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-stone-900">Presensi Guru Mandiri</h4>
                                <p class="text-[10px] text-stone-500 font-medium">Check-in dan check-out kehadiran pendamping.</p>
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

    @else
        <!-- Info & Tutorial Box for Regular Teachers -->
        <x-info-tutorial-box 
            title="Panduan Portal Guru & Wali Kelas"
            :steps="[
                ['title' => 'Absensi Mandiri', 'desc' => 'Lakukan Check-In saat tiba di sekolah dan Check-Out sebelum pulang pada menu Absensi Mandiri.'],
                ['title' => 'Input Nilai Sumatif', 'desc' => 'Isi nilai sumatif Lingkup Materi dan Sumatif Akhir Semester (SAS) untuk mata pelajaran yang diampu.'],
                ['title' => 'Cetak & Terbitkan Rapor', 'desc' => 'Khusus Wali Kelas: verifikasi capaian nilai, absensi, dan sikap sebelum menerbitkan rapor digital.']
            ]"
        />

        <!-- Welcome Header Card (Light Theme Standard) -->
        <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-stone-900 tracking-tight">Selamat Datang, {{ auth()->user()->nama }}</h2>
                <p class="text-xs text-stone-600 font-semibold mt-1">Panel pengajar & wali kelas akademis sekolah.</p>
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
                            <p class="text-[10px] text-stone-500 font-medium">Kelola Bab, TP & Auto-Narasi.</p>
                        </div>
                    </a>

                    <a href="{{ route('guru.input-sumatif') }}" class="flex items-center gap-3 p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 hover:border-emerald-300 rounded-xl text-stone-900 transition duration-150">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-800 shrink-0">
                            <x-lucide-edit-3 class="w-4 h-4" />
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-stone-900">Input Nilai Sumatif TP & SAS</h4>
                            <p class="text-[10px] text-stone-500 font-medium">Form matriks nilai & auto-narasi.</p>
                        </div>
                    </a>

                    <a href="{{ route('guru.input-tahfidz') }}" class="flex items-center gap-3 p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 hover:border-amber-300 rounded-xl text-stone-900 transition duration-150">
                        <div class="w-9 h-9 rounded-lg bg-amber-100 border border-amber-300 flex items-center justify-center text-amber-900 shrink-0">
                            <x-lucide-award class="w-4 h-4" />
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-stone-900">Input Setoran Tahfizh</h4>
                            <p class="text-[10px] text-stone-500 font-medium">Setoran hafalan surah & tajwid.</p>
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

                    <a href="{{ route('guru.ekskul') }}" class="flex items-center gap-3 p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 hover:border-amber-300 rounded-xl text-stone-900 transition duration-150">
                        <div class="w-9 h-9 rounded-lg bg-amber-100 border border-amber-300 flex items-center justify-center text-amber-900 shrink-0">
                            <x-lucide-star class="w-4 h-4" />
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-stone-900">Ekstrakurikuler Siswa</h4>
                            <p class="text-[10px] text-stone-500 font-medium">Katalog & penilaian ekskul santri.</p>
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
    @endif
</div>
