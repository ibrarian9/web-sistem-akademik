<div class="space-y-6 font-sans"
    x-data="{
        classChart: null,
        roleChart: null,
        initCharts() {
            if (typeof window.Chart === 'undefined') return;

            // 1. Class Distribution Bar Chart
            const classCtx = document.getElementById('adminClassDistributionChart');
            if (classCtx) {
                if (this.classChart) this.classChart.destroy();
                this.classChart = new window.Chart(classCtx, {
                    type: 'bar',
                    data: {
                        labels: @js($classLabels),
                        datasets: [{
                            label: 'Jumlah Santri',
                            data: @js($classStudentCounts),
                            backgroundColor: '#059669',
                            borderRadius: 8,
                            hoverBackgroundColor: '#047857'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => `${ctx.raw} Santri Aktif`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f5f5f4' },
                                ticks: { stepSize: 5, font: { size: 10 } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11, weight: 'bold' } }
                            }
                        }
                    }
                });
            }

            // 2. User Role Distribution Doughnut Chart
            const roleCtx = document.getElementById('adminRoleDistributionChart');
            if (roleCtx) {
                if (this.roleChart) this.roleChart.destroy();
                this.roleChart = new window.Chart(roleCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @js($roleLabels),
                        datasets: [{
                            data: @js($roleUserCounts),
                            backgroundColor: ['#059669', '#3b82f6', '#f59e0b', '#8b5cf6', '#64748b'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    usePointStyle: true,
                                    font: { family: 'inherit', size: 10, weight: 'bold' }
                                }
                            }
                        }
                    }
                });
            }
        }
    }"
    x-init="initCharts()"
>
    <!-- Header Title Bar -->
    <x-page-header 
        title="Selamat Datang, {{ auth()->user()->nama }}" 
        subtitle="Pusat kendali eksekutif, rekapitulasi data akademik yayasan, dan tata kelola sistem terpadu."
        badge="PANEL SUPER ADMIN"
        badgeVariant="emerald"
        icon="shield-check"
    />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Super Admin & Pengendalian Sistem"
        :steps="[
            ['title' => 'Statistik Sistem', 'desc' => 'Tinjau total pengguna aktif, kelas berjalan, serta ikhtisar keuangan yayasan.'],
            ['title' => 'Akses Cepat Pengelolaan', 'desc' => 'Gunakan pintasan navigasi untuk mengelola user, keuangan, audit log, dan pengaturan global.'],
            ['title' => 'Status Server', 'desc' => 'Pantau kesehatan basis data dan versi framework sistem sekolah secara berkala.']
        ]"
    />

    <!-- Stat Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card title="Total Siswa Aktif" :value="$totalSiswa" icon="users" color="green" />
        <x-stat-card title="Total Guru & Staf" :value="$totalGuru" icon="user-check" color="blue" />
        <x-stat-card title="Total Kelas" :value="$totalKelas" icon="calendar" color="amber" />
        <x-stat-card title="Tunggakan SPP" :value="$totalTunggakan" icon="wallet" color="red" />
    </div>

    <!-- Visual Interactive Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Class Student Count Bar Chart -->
        <div class="lg:col-span-2">
            <x-chart-card 
                title="Sebaran Santri per Rombel Kelas" 
                subtitle="Populasi siswa aktif yang terdaftar di masing-masing kelas."
                icon="bar-chart-2"
                badge="SEBARAN ROMBEL"
                badgeVariant="emerald"
                canvasId="adminClassDistributionChart"
                height="260px"
            />
        </div>

        <!-- Role User Distribution Doughnut Chart -->
        <div>
            <x-chart-card 
                title="Distribusi Akun & Hak Akses" 
                subtitle="Komposisi pengguna terdaftar berdasarkan peran."
                icon="pie-chart"
                badge="ROLE PENGGUNA"
                badgeVariant="stone"
                canvasId="adminRoleDistributionChart"
                height="260px"
            />
        </div>
    </div>

    <!-- Action Items / Quick Links -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Shortcut menu card -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-xs font-extrabold text-stone-800 uppercase tracking-wider">Akses Cepat Pengelolaan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('super-admin.user') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-emerald-400 rounded-2xl flex items-start gap-3.5 group transition duration-200 hover:shadow-xs">
                    <span class="p-2.5 rounded-xl bg-emerald-100 text-emerald-950 border border-emerald-300 group-hover:scale-105 transition duration-200 shadow-2xs">
                        <x-lucide-users class="w-5 h-5 text-emerald-900" />
                    </span>
                    <div>
                        <h4 class="text-xs font-extrabold text-stone-900 group-hover:text-emerald-800 transition">Manajemen User</h4>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">Kelola akun pengguna, hak akses & role.</p>
                    </div>
                </a>

                <a href="{{ route('super-admin.capaian-guru') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-emerald-400 rounded-2xl flex items-start gap-3.5 group transition duration-200 hover:shadow-xs">
                    <span class="p-2.5 rounded-xl bg-purple-100 text-purple-950 border border-purple-300 group-hover:scale-105 transition duration-200 shadow-2xs">
                        <x-lucide-award class="w-5 h-5 text-purple-900" />
                    </span>
                    <div>
                        <h4 class="text-xs font-extrabold text-stone-900 group-hover:text-purple-800 transition">Evaluasi Capaian Guru</h4>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">Penilaian portofolio & sertifikasi guru.</p>
                    </div>
                </a>

                <a href="{{ route('super-admin.audit-log') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-emerald-400 rounded-2xl flex items-start gap-3.5 group transition duration-200 hover:shadow-xs">
                    <span class="p-2.5 rounded-xl bg-amber-100 text-amber-950 border border-amber-300 group-hover:scale-105 transition duration-200 shadow-2xs">
                        <x-lucide-activity class="w-5 h-5 text-amber-900" />
                    </span>
                    <div>
                        <h4 class="text-xs font-extrabold text-stone-900 group-hover:text-amber-800 transition">Audit Log</h4>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">Monitor seluruh riwayat aktivitas sistem.</p>
                    </div>
                </a>

                <a href="{{ route('super-admin.pengaturan') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-emerald-400 rounded-2xl flex items-start gap-3.5 group transition duration-200 hover:shadow-xs">
                    <span class="p-2.5 rounded-xl bg-stone-100 text-stone-700 border border-stone-300 group-hover:scale-105 transition duration-200 shadow-2xs">
                        <x-lucide-settings class="w-5 h-5 text-stone-800" />
                    </span>
                    <div>
                        <h4 class="text-xs font-extrabold text-stone-900 group-hover:text-stone-700 transition">Pengaturan Sistem</h4>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">Konfigurasi instansi & identitas dokumen.</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- System Alerts / Status -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-xs font-extrabold text-stone-800 uppercase tracking-wider">Status Sistem & Server</h3>
            <div class="space-y-3 text-xs">
                <div class="p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="font-bold text-stone-800">Database Engine</span>
                    </div>
                    <x-badge variant="emerald" size="xs">MariaDB Connected</x-badge>
                </div>
                <div class="p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                        <span class="font-bold text-stone-800">Versi Framework</span>
                    </div>
                    <span class="font-mono text-stone-600 font-bold">Laravel {{ app()->version() }}</span>
                </div>
                <div class="p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                        <span class="font-bold text-stone-800">Versi PHP</span>
                    </div>
                    <span class="font-mono text-stone-600 font-bold">PHP {{ PHP_VERSION }}</span>
                </div>
                <div class="p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                        <span class="font-bold text-stone-800">Zona Waktu</span>
                    </div>
                    <span class="font-mono text-stone-600 font-bold">{{ config('app.timezone') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
