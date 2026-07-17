<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="flex flex-col gap-1">
        <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Selamat Datang, {{ auth()->user()->nama }}</h2>
        <p class="text-sm text-stone-500">Berikut ringkasan data akademik dan administrasi hari ini.</p>
    </div>

    <!-- Stat Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-stat-card title="Siswa Aktif" :value="$totalSiswa" icon="users" color="green" />
        <x-stat-card title="Guru Aktif" :value="$totalGuru" icon="user-check" color="blue" />
        <x-stat-card title="Total Kelas" :value="$totalKelas" icon="calendar" color="amber" />
    </div>

    <!-- Quick Access -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Shortcut menu -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider">Akses Cepat Tata Usaha</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('tata-usaha.siswa') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-green-300 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-green-50 text-green-600 border border-green-200 group-hover:scale-105 transition duration-200"><x-lucide-users class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-sm font-semibold text-stone-800">Data Siswa</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Tambah & kelola data siswa aktif.</p>
                    </div>
                </a>
                <a href="{{ route('tata-usaha.guru') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-green-300 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 group-hover:scale-105 transition duration-200"><x-lucide-user-check class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-sm font-semibold text-stone-800">Data Guru</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Kelola profil & penugasan guru.</p>
                    </div>
                </a>
                <a href="{{ route('tata-usaha.kelas') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-green-300 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 group-hover:scale-105 transition duration-200"><x-lucide-layers class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-sm font-semibold text-stone-800">Kelas & Mata Pelajaran</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Atur kelas, wali, dan mapel.</p>
                    </div>
                </a>
                <a href="{{ route('tata-usaha.jadwal') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-green-300 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-stone-100 text-stone-600 border border-stone-200 group-hover:scale-105 transition duration-200"><x-lucide-calendar class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-sm font-semibold text-stone-800">Jadwal Pelajaran</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Kelola jadwal anti bentrok.</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Today Info -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider">Informasi Hari Ini</h3>
            <div class="space-y-3">
                <div class="p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                        <span class="text-sm font-semibold text-stone-700">Hari</span>
                    </div>
                    <span class="text-sm text-stone-600 font-medium">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
                <div class="p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                        <span class="text-sm font-semibold text-stone-700">Waktu</span>
                    </div>
                    <span class="text-sm text-stone-600 font-medium">{{ date('H:i') }} WIB</span>
                </div>
                <div class="p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
                        <span class="text-sm font-semibold text-stone-700">Peran</span>
                    </div>
                    <span class="text-sm text-stone-600 font-medium">Tata Usaha</span>
                </div>
            </div>
        </div>
    </div>
</div>
