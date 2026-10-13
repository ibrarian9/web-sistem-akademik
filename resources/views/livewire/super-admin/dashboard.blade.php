<div class="space-y-6 font-sans"
    x-data="{
        classChart: null,
        roleChart: null,
        quranState: {
            surahTitle: 'Surah Al-Baqarah : Ayat 255',
            surahName: 'Al-Baqarah',
            ayahNumber: 255,
            arabicText: 'ٱللَّهُ لَآ إِلَٰهَ إِلَّا هُوَ ٱلْحَىُّ ٱلْقَيُّومُ ۚ لَا تَأْخُذُهُۥ سِنَةٌۭ وَلَا نَوْمٌۭ',
            translation: 'Allah, tidak ada tuhan selain Dia. Yang Mahahidup, Yang terus-menerus mengurus makhluk-Nya.',
            isPlaying: false
        },
        initDashboard() {
            this.initCharts();
            window.addEventListener('quran-audio-state', (e) => {
                if (e.detail) {
                    this.quranState = {
                        surahTitle: e.detail.surahTitle || this.quranState.surahTitle,
                        surahName: e.detail.surahName || this.quranState.surahName,
                        ayahNumber: e.detail.ayahNumber || this.quranState.ayahNumber,
                        arabicText: e.detail.arabicText || this.quranState.arabicText,
                        translation: e.detail.translation || this.quranState.translation,
                        isPlaying: e.detail.isPlaying || false
                    };
                }
            });
        },
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
    x-init="initDashboard()"
>
    <!-- Header Title Bar dengan Konteks Periode Aktif -->
    <x-page-header 
        title="Selamat Datang, {{ auth()->user()->nama }}" 
        subtitle="Pusat kendali eksekutif, rekapitulasi data akademik yayasan, dan tata kelola sistem terpadu."
        badge="PANEL SUPER ADMIN"
        icon="shield-check"
    >
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>TA {{ $tahunAjaranAktif }} (Semester {{ $semesterAktif }})</span>
                </span>
                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-stone-100 text-stone-700 border border-stone-200 rounded-xl text-xs font-bold shadow-2xs">
                    <x-lucide-shield-check class="w-3.5 h-3.5 text-emerald-600" />
                    <span>Sistem Aktif</span>
                </span>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Info Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Super Admin & Pengendalian Sistem"
        :steps="[
            ['title' => 'Statistik Sistem', 'desc' => 'Tinjau total pengguna aktif, kelas berjalan, rasio guru santri, serta ikhtisar keuangan yayasan.'],
            ['title' => 'Aktivitas Terbaru & Audit', 'desc' => 'Pantau riwayat aksi pengguna secara langsung dan telusuri log keamanan sistem.'],
            ['title' => 'Lantunan Al-Qur\'an Acak', 'desc' => 'Dengarkan lantunan ayat dan surat suci yang diputar secara acak untuk keberkahan aktivitas madrasah.']
        ]"
    />

    <!-- Executive Stat Grid (4 Simple & Informative Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card 
            title="Total Siswa Aktif" 
            :value="$totalSiswa" 
            :subtitle="'Rasio ' . $rasioSiswaGuru . ' guru'" 
            icon="users" 
            variant="soft-emerald" 
            class="h-full" 
        />
        <x-stat-card 
            title="Total Guru & Staf" 
            :value="$totalGuru" 
            subtitle="Tenaga pendidik terdaftar" 
            icon="user-check" 
            variant="white" 
            class="h-full" 
        />
        <x-stat-card 
            title="Total Kelas" 
            :value="$totalKelas" 
            subtitle="Rombongan belajar aktif" 
            icon="calendar" 
            variant="white" 
            class="h-full" 
        />
        <a href="{{ route('finance.laporan.tunggakan') }}" class="block h-full group focus:outline-hidden">
            <x-stat-card 
                title="Tunggakan SPP" 
                :value="$totalTunggakan" 
                subtitle="Jatuh tempo s/d akhir bulan ini" 
                icon="wallet" 
                variant="soft-rose" 
                class="h-full group-hover:border-rose-300 group-hover:shadow-xs transition duration-200" 
            />
        </a>
    </div>

    <!-- MUTIARA AYAT & LANTUNAN AL-QUR'AN (ACAK SURAT & ACAK AYAT) -->
    <div class="bg-gradient-to-br from-emerald-900 via-teal-900 to-emerald-950 text-white rounded-2xl p-5 sm:p-6 shadow-md border border-emerald-800/80 relative overflow-hidden">
        <!-- Background Pattern Deco -->
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-emerald-500/10 blur-2xl pointer-events-none"></div>
        <div class="absolute right-6 top-6 text-emerald-400/10 pointer-events-none">
            <x-lucide-book-open class="w-32 h-32" />
        </div>

        <div class="relative z-10 space-y-4">
            <!-- Header Card Mutiara Ayat -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-emerald-700/60">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-emerald-800/80 border border-emerald-600/50 text-emerald-300 shadow-2xs">
                        <x-lucide-sparkles class="w-4 h-4 text-emerald-300" />
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs font-black uppercase tracking-wider text-emerald-200">Mutiara Ayat & Lantunan Al-Qur'an</h3>
                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-400/30">Acak Otomatis</span>
                        </div>
                        <p class="text-[11px] text-emerald-100/70 font-medium">Lantunan ayat suci dan renungan hikmah yang berganti secara acak setiap saat.</p>
                    </div>
                </div>

                <!-- Action Controls: Putar/Jeda & Acak Baru -->
                <div class="flex items-center gap-2 shrink-0">
                    <button 
                        type="button" 
                        @click="$dispatch('quran-play-toggle')" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-xs transition hover:scale-102 cursor-pointer active:scale-98"
                    >
                        <template x-if="quranState.isPlaying">
                            <span class="flex items-center gap-1.5">
                                <x-lucide-pause class="w-3.5 h-3.5" />
                                <span>Jeda Lantunan</span>
                            </span>
                        </template>
                        <template x-if="!quranState.isPlaying">
                            <span class="flex items-center gap-1.5">
                                <x-lucide-play class="w-3.5 h-3.5 fill-current" />
                                <span>Putar Lantunan</span>
                            </span>
                        </template>
                    </button>

                    <button 
                        type="button" 
                        @click="$dispatch('quran-play-random')" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-xl text-xs font-bold transition hover:scale-102 cursor-pointer active:scale-98"
                        title="Putar Ayat dan Surat Acak Lainnya"
                    >
                        <x-lucide-shuffle class="w-3.5 h-3.5 text-emerald-300" />
                        <span>Acak Ayat Baru</span>
                    </button>
                </div>
            </div>

            <!-- Verse Body Display -->
            <div class="space-y-3 pt-1">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-extrabold text-emerald-300 tracking-wide text-sm" x-text="quranState.surahTitle">
                        Surah Al-Baqarah : Ayat 255
                    </span>
                    <span class="text-[10px] text-emerald-300/80 font-mono">
                        Qari: Misyari Rasyid Al-Afasy
                    </span>
                </div>

                <!-- Teks Arab Ayat -->
                <div class="p-4 bg-emerald-950/60 rounded-xl border border-emerald-800/60">
                    <p class="text-right text-base sm:text-xl font-serif text-emerald-50 leading-loose tracking-wide" dir="rtl" x-text="quranState.arabicText">
                        ٱللَّهُ لَآ إِلَٰهَ إِلَّا هُوَ ٱلْحَىُّ ٱلْقَيُّومُ ۚ لَا تَأْخُذُهُۥ سِنَةٌۭ وَلَا نَوْمٌۭ
                    </p>
                </div>

                <!-- Terjemahan Bahasa Indonesia -->
                <p class="text-xs sm:text-sm text-emerald-100/90 italic font-medium leading-relaxed" x-text="'&ldquo;' + quranState.translation + '&rdquo;'">
                    "Allah, tidak ada tuhan selain Dia. Yang Mahahidup, Yang terus-menerus mengurus makhluk-Nya."
                </p>
            </div>
        </div>
    </div>

    <!-- Visual Interactive Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Class Student Count Bar Chart -->
        <div class="lg:col-span-2">
            <x-chart-card 
                title="Sebaran Santri per Rombel Kelas" 
                subtitle="Populasi siswa aktif yang terdaftar di masing-masing kelas."
                icon="bar-chart-2"
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
                canvasId="adminRoleDistributionChart"
                height="260px"
            />
        </div>
    </div>

    <!-- Executive Information Row: Aktivitas Sistem Terbaru & Status Server -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Aktivitas Sistem Terbaru (Audit Feed) -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <div class="flex items-center gap-2">
                    <x-lucide-activity class="w-4 h-4 text-emerald-600" />
                    <h3 class="text-xs font-extrabold text-stone-800 uppercase tracking-wider">Aktivitas Sistem Terbaru</h3>
                </div>
                <a href="{{ route('super-admin.audit-log') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline inline-flex items-center gap-1 transition">
                    <span>Lihat Seluruh Audit Log</span>
                    <x-lucide-arrow-right class="w-3.5 h-3.5" />
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($recentActivities as $act)
                    <div class="p-3 bg-stone-50/80 hover:bg-emerald-50/30 border border-stone-200/90 rounded-xl flex items-start justify-between gap-3 transition">
                        <div class="flex items-start gap-3 min-w-0">
                            <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-black shrink-0 mt-0.5">
                                {{ strtoupper(substr($act['user_name'], 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-xs font-extrabold text-stone-900 truncate">{{ $act['user_name'] }}</span>
                                    <span class="text-[10px] px-1.5 py-0.2 bg-stone-200 text-stone-700 font-bold rounded">{{ $act['role_name'] }}</span>
                                </div>
                                <p class="text-[11px] text-stone-600 font-medium mt-0.5 break-words">{{ $act['description'] }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] text-stone-400 font-bold shrink-0 whitespace-nowrap mt-0.5">
                            {{ $act['time_ago'] }}
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-stone-400 text-xs font-medium">
                        Belum ada aktivitas tercatat pada sistem.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Status Server & Parameter Sistem -->
        <div class="bg-white border border-stone-200 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-stone-100">
                <x-lucide-server class="w-4 h-4 text-emerald-600" />
                <h3 class="text-xs font-extrabold text-stone-800 uppercase tracking-wider">Status Server & Lingkungan</h3>
            </div>
            
            <div class="space-y-2.5 text-xs">
                <div class="p-2.5 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="font-bold text-stone-800">Basis Data</span>
                    </div>
                    <span class="font-mono text-[11px] text-emerald-700 font-bold">MariaDB Online</span>
                </div>
                <div class="p-2.5 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <span class="font-bold text-stone-800">Versi Framework</span>
                    <span class="font-mono text-[11px] text-stone-600 font-bold">Laravel {{ app()->version() }}</span>
                </div>
                <div class="p-2.5 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <span class="font-bold text-stone-800">Versi PHP</span>
                    <span class="font-mono text-[11px] text-stone-600 font-bold">PHP {{ PHP_VERSION }}</span>
                </div>
                <div class="p-2.5 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <span class="font-bold text-stone-800">Total Akun Sistem</span>
                    <span class="font-mono text-[11px] text-stone-900 font-black">{{ $totalUser }} Pengguna</span>
                </div>
                <div class="p-2.5 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200">
                    <span class="font-bold text-stone-800">Zona Waktu</span>
                    <span class="font-mono text-[11px] text-stone-600 font-bold">{{ config('app.timezone') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Akses Cepat Pengelolaan (6 Clean Shortcuts) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
        <h3 class="text-xs font-extrabold text-stone-800 uppercase tracking-wider">Akses Cepat Pengelolaan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
            <a href="{{ route('super-admin.user') }}" class="p-3.5 bg-stone-50 border border-stone-200 hover:border-emerald-400 rounded-xl flex items-start gap-3 group transition duration-200 hover:shadow-xs">
                <span class="p-2 rounded-lg bg-emerald-100 text-emerald-950 border border-emerald-300 group-hover:scale-105 transition duration-200 shadow-2xs">
                    <x-lucide-users class="w-4 h-4 text-emerald-900" />
                </span>
                <div>
                    <h4 class="text-xs font-extrabold text-stone-900 group-hover:text-emerald-800 transition">Manajemen User</h4>
                    <p class="text-[11px] text-stone-500 font-medium mt-0.5">Akun, hak akses & role pengguna.</p>
                </div>
            </a>

            <a href="{{ route('super-admin.monitoring-akademik') }}" class="p-3.5 bg-stone-50 border border-stone-200 hover:border-emerald-400 rounded-xl flex items-start gap-3 group transition duration-200 hover:shadow-xs">
                <span class="p-2 rounded-lg bg-blue-100 text-blue-950 border border-blue-300 group-hover:scale-105 transition duration-200 shadow-2xs">
                    <x-lucide-graduation-cap class="w-4 h-4 text-blue-900" />
                </span>
                <div>
                    <h4 class="text-xs font-extrabold text-stone-900 group-hover:text-blue-800 transition">Monitoring Akademik</h4>
                    <p class="text-[11px] text-stone-500 font-medium mt-0.5">Pemantauan nilai & kurikulum.</p>
                </div>
            </a>

            <a href="{{ route('super-admin.kelas') }}" class="p-3.5 bg-stone-50 border border-stone-200 hover:border-emerald-400 rounded-xl flex items-start gap-3 group transition duration-200 hover:shadow-xs">
                <span class="p-2 rounded-lg bg-amber-100 text-amber-950 border border-amber-300 group-hover:scale-105 transition duration-200 shadow-2xs">
                    <x-lucide-layout-grid class="w-4 h-4 text-amber-900" />
                </span>
                <div>
                    <h4 class="text-xs font-extrabold text-stone-900 group-hover:text-amber-800 transition">Rombel & Santri</h4>
                    <p class="text-[11px] text-stone-500 font-medium mt-0.5">Kelola kelas dan data siswa.</p>
                </div>
            </a>

            <a href="{{ route('finance.kas-gabungan') }}" class="p-3.5 bg-stone-50 border border-stone-200 hover:border-emerald-400 rounded-xl flex items-start gap-3 group transition duration-200 hover:shadow-xs">
                <span class="p-2 rounded-lg bg-teal-100 text-teal-950 border border-teal-300 group-hover:scale-105 transition duration-200 shadow-2xs">
                    <x-lucide-wallet class="w-4 h-4 text-teal-900" />
                </span>
                <div>
                    <h4 class="text-xs font-extrabold text-stone-900 group-hover:text-teal-800 transition">Arus Kas & Dana Bos</h4>
                    <p class="text-[11px] text-stone-500 font-medium mt-0.5">Pembukuan kas dan BOS terpadu.</p>
                </div>
            </a>

            <a href="{{ route('super-admin.capaian-guru') }}" class="p-3.5 bg-stone-50 border border-stone-200 hover:border-emerald-400 rounded-xl flex items-start gap-3 group transition duration-200 hover:shadow-xs">
                <span class="p-2 rounded-lg bg-purple-100 text-purple-950 border border-purple-300 group-hover:scale-105 transition duration-200 shadow-2xs">
                    <x-lucide-award class="w-4 h-4 text-purple-900" />
                </span>
                <div>
                    <h4 class="text-xs font-extrabold text-stone-900 group-hover:text-purple-800 transition">Evaluasi Guru</h4>
                    <p class="text-[11px] text-stone-500 font-medium mt-0.5">Portofolio & capaian pendidik.</p>
                </div>
            </a>

            <a href="{{ route('super-admin.pengaturan') }}" class="p-3.5 bg-stone-50 border border-stone-200 hover:border-emerald-400 rounded-xl flex items-start gap-3 group transition duration-200 hover:shadow-xs">
                <span class="p-2 rounded-lg bg-stone-100 text-stone-700 border border-stone-300 group-hover:scale-105 transition duration-200 shadow-2xs">
                    <x-lucide-settings class="w-4 h-4 text-stone-800" />
                </span>
                <div>
                    <h4 class="text-xs font-extrabold text-stone-900 group-hover:text-stone-700 transition">Pengaturan Sistem</h4>
                    <p class="text-[11px] text-stone-500 font-medium mt-0.5">Identitas sekolah & konfigurasi.</p>
                </div>
            </a>
        </div>
    </div>
</div>
