<div class="space-y-8">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Super Admin & Pengendalian Sistem"
        :steps="[
            ['title' => 'Statistik Sistem', 'desc' => 'Tinjau total pengguna aktif, kelas berjalan, serta ikhtisar keuangan yayasan.'],
            ['title' => 'Akses Cepat Pengelolaan', 'desc' => 'Gunakan pintasan navigasi untuk mengelola user, keuangan, audit log, dan pengaturan global.'],
            ['title' => 'Status Server', 'desc' => 'Pantau kesehatan basis data dan versi framework sistem sekolah secara berkala.']
        ]"
    />

    <!-- Welcome Header -->
    <div class="flex flex-col gap-1">
        <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Selamat Datang, {{ auth()->user()->nama }}</h2>
        <p class="text-sm text-stone-500">Berikut adalah ringkasan status operasional yayasan hari ini.</p>
    </div>

    <!-- Stat Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stat-card title="Total Siswa Aktif" :value="$totalSiswa" icon="users" color="green" />
        <x-stat-card title="Total Guru & Staf" :value="$totalGuru" icon="user-check" color="blue" />
        <x-stat-card title="Total Kelas" :value="$totalKelas" icon="calendar" color="amber" />
        <x-stat-card title="Tunggakan SPP" :value="$totalTunggakan" icon="wallet" color="red" />
    </div>

    <!-- Action Items / Quick Links -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Shortcut menu card -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider">Akses Cepat Pengelolaan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="#" class="p-4 bg-stone-50 border border-stone-200 hover:border-green-300 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-green-50 text-green-600 border border-green-200 group-hover:scale-105 transition duration-200"><x-lucide-users class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-sm font-semibold text-stone-800">Manajemen User</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Kelola akun pengguna & role.</p>
                    </div>
                </a>
                <a href="#" class="p-4 bg-stone-50 border border-stone-200 hover:border-green-300 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 group-hover:scale-105 transition duration-200"><x-lucide-wallet class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-sm font-semibold text-stone-800">Keuangan</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Laporan pemasukan & pengeluaran.</p>
                    </div>
                </a>
                <a href="#" class="p-4 bg-stone-50 border border-stone-200 hover:border-green-300 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 group-hover:scale-105 transition duration-200"><x-lucide-activity class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-sm font-semibold text-stone-800">Audit Log</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Monitor semua aktivitas sistem.</p>
                    </div>
                </a>
                <a href="#" class="p-4 bg-stone-50 border border-stone-200 hover:border-green-300 rounded-2xl flex items-start gap-3 group transition duration-200">
                    <span class="p-2.5 rounded-xl bg-stone-100 text-stone-600 border border-stone-200 group-hover:scale-105 transition duration-200"><x-lucide-settings class="w-5 h-5" /></span>
                    <div>
                        <h4 class="text-sm font-semibold text-stone-800">Pengaturan Sistem</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Set jam masuk & preferensi.</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- System Alerts / Status -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider">Status Sistem</h3>
            <div class="space-y-3">
                <div class="p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                        <span class="text-sm font-semibold text-stone-700">Database</span>
                    </div>
                    <span class="text-xs text-stone-500 font-medium">MariaDB 10.11</span>
                </div>
                <div class="p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                        <span class="text-sm font-semibold text-stone-700">Versi Framework</span>
                    </div>
                    <span class="text-xs text-stone-500 font-medium">Laravel 13.20</span>
                </div>
                <div class="p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                        <span class="text-sm font-semibold text-stone-700">Waktu Server</span>
                    </div>
                    <span class="text-xs text-stone-500 font-medium">{{ date('H:i') }} WIB</span>
                </div>
            </div>
        </div>
    </div>
</div>
