<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="flex flex-col gap-1">
        <h2 class="text-xl font-bold text-white tracking-tight">Selamat Datang, {{ auth()->user()->nama }}</h2>
        <p class="text-sm text-slate-500">Berikut adalah ringkasan status operasional yayasan hari ini.</p>
    </div>

    <!-- Stat Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stat-card title="Total Siswa Aktif" :value="$totalSiswa" icon="users" color="indigo" />
        <x-stat-card title="Total Guru & Staf" :value="$totalGuru" icon="user-check" color="emerald" />
        <x-stat-card title="Total Kelas" :value="$totalKelas" icon="calendar" color="amber" />
        <x-stat-card title="Tunggakan SPP" :value="$totalTunggakan" icon="wallet" color="rose" />
    </div>

    <!-- Action Items / Quick Links -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Shortcut menu card -->
        <div class="lg:col-span-2 bg-slate-900/40 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Akses Cepat Pengelolaan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="#" class="p-4 bg-slate-900 border border-slate-850 hover:border-indigo-500/50 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-400 group-hover:scale-105 transition duration-200"><x-lucide-users class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-sm font-semibold text-white">Manajemen Siswa</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Tambah & Kelola data siswa aktif.</p>
                    </div>
                </a>
                <a href="#" class="p-4 bg-slate-900 border border-slate-850 hover:border-indigo-500/50 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 group-hover:scale-105 transition duration-200"><x-lucide-user-check class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-sm font-semibold text-white">Manajemen Guru</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Atur penugasan & data guru.</p>
                    </div>
                </a>
                <a href="#" class="p-4 bg-slate-900 border border-slate-850 hover:border-indigo-500/50 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 group-hover:scale-105 transition duration-200"><x-lucide-calendar class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-sm font-semibold text-white">Jadwal Pelajaran</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola jadwal anti bentrok.</p>
                    </div>
                </a>
                <a href="#" class="p-4 bg-slate-900 border border-slate-850 hover:border-indigo-500/50 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-rose-500/10 text-rose-400 group-hover:scale-105 transition duration-200"><x-lucide-settings class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-sm font-semibold text-white">Pengaturan Sistem</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Set jam masuk & preferensi.</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- System Alerts / Status -->
        <div class="bg-slate-900/40 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Status Sistem</h3>
            <div class="space-y-3">
                <div class="p-3 bg-slate-900 rounded-xl flex items-center justify-between border border-slate-850">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                        <span class="text-xs font-semibold text-white">Database</span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium">MariaDB 10.11</span>
                </div>
                <div class="p-3 bg-slate-900 rounded-xl flex items-center justify-between border border-slate-850">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                        <span class="text-xs font-semibold text-white">Versi Framework</span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium">Laravel 13.20</span>
                </div>
                <div class="p-3 bg-slate-900 rounded-xl flex items-center justify-between border border-slate-850">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                        <span class="text-xs font-semibold text-white">Waktu Server</span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium">{{ date('H:i') }} WIB</span>
                </div>
            </div>
        </div>
    </div>
</div>
