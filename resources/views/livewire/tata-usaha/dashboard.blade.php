<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Pusat Kendali Tata Usaha" 
        subtitle="Selamat datang, {{ auth()->user()->nama }}. Berikut ringkasan data akademik dan administrasi sekolah hari ini."
        badge="DASHBOARD ADMINISTRASI &amp; TU"
        badgeVariant="emerald"
        icon="layout-dashboard"
    />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Panduan Pusat Kendali Tata Usaha (TU)"
        :steps="[
            ['title' => 'Kelola Data Master', 'desc' => 'Gunakan menu Siswa, Guru, & Karyawan untuk menginput serta memverifikasi biodata kepegawaian & siswa.'],
            ['title' => 'Penjadwalan & Komponen', 'desc' => 'Atur Jadwal Pelajaran, Kelas, serta Komponen Nilai Mapel Umum dan Tahfidz pada menu Tata Kelola.'],
            ['title' => 'Kenaikan & Kalender', 'desc' => 'Jalankan Proses Kenaikan Kelas di akhir semester dan atur agenda akademik di Kalender Akademik.']
        ]"
        notes="Selalu pastikan Tahun Ajaran & Semester Aktif telah sesuai pada pengaturan sistem."
    />

    <!-- Stat Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-stat-card title="Siswa Aktif" :value="$totalSiswa" icon="users" color="green" />
        <x-stat-card title="Guru Aktif" :value="$totalGuru" icon="user-check" color="blue" />
        <x-stat-card title="Total Kelas" :value="$totalKelas" icon="calendar" color="amber" />
    </div>

    <!-- Quick Access -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Shortcut menu -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Akses Cepat Tata Usaha</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('tata-usaha.siswa') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-emerald-300 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 group-hover:scale-105 transition duration-200"><x-lucide-users class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Data Siswa</h4>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">Tambah &amp; kelola data siswa aktif.</p>
                    </div>
                </a>
                <a href="{{ route('tata-usaha.guru') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-emerald-300 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 group-hover:scale-105 transition duration-200"><x-lucide-user-check class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Data Guru</h4>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">Kelola profil &amp; penugasan guru.</p>
                    </div>
                </a>
                <a href="{{ route('tata-usaha.kelas') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-emerald-300 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 group-hover:scale-105 transition duration-200"><x-lucide-layers class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Kelas &amp; Mata Pelajaran</h4>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">Atur kelas, wali, dan mapel.</p>
                    </div>
                </a>
                <a href="{{ route('tata-usaha.jadwal') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-emerald-300 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 group-hover:scale-105 transition duration-200"><x-lucide-calendar class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Jadwal Pelajaran</h4>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">Kelola jadwal anti bentrok.</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Today Info -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Informasi Hari Ini</h3>
            <div class="space-y-3">
                <div class="p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                        <span class="text-xs font-bold text-stone-700">Hari</span>
                    </div>
                    <span class="text-xs text-stone-600 font-semibold">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
                <div class="p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                        <span class="text-xs font-bold text-stone-700">Waktu</span>
                    </div>
                    <span class="text-xs text-stone-600 font-semibold">{{ date('H:i') }} WIB</span>
                </div>
                <div class="p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
                        <span class="text-xs font-bold text-stone-700">Peran</span>
                    </div>
                    <span class="text-xs text-stone-600 font-semibold">Tata Usaha</span>
                </div>
            </div>
        </div>
    </div>
</div>
