<div
    x-data="{
        isOpen: false,
        searchQuery: '',
        selectedIndex: 0,
        menus: [
            // Super Admin
            { title: 'Dashboard Super Admin', category: 'Super Admin', url: '/admin/dashboard', icon: 'layout-dashboard', keywords: 'admin dashboard ringkasan statistik' },
            { title: 'Manajemen Akun User', category: 'Super Admin', url: '/admin/manajemen-user', icon: 'users', keywords: 'user pengguna akun login role hak akses' },
            { title: 'Manajemen Guru & Pendidik', category: 'Super Admin', url: '/admin/manajemen-guru', icon: 'user-check', keywords: 'guru ustadz pendidik nip guru aktif' },
            { title: 'Manajemen Data Siswa', category: 'Super Admin', url: '/admin/manajemen-siswa', icon: 'graduation-cap', keywords: 'siswa murid santri biodata kelas' },
            { title: 'Manajemen Rombel Kelas', category: 'Super Admin', url: '/admin/manajemen-kelas', icon: 'school', keywords: 'kelas rombel halaqah tingkat' },
            { title: 'Mata Pelajaran & Kurikulum', category: 'Super Admin', url: '/admin/manajemen-mapel', icon: 'book-open', keywords: 'mapel pelajaran kurikulum merdeka' },
            { title: 'Jadwal Pelajaran & KBM', category: 'Super Admin', url: '/admin/manajemen-jadwal', icon: 'calendar', keywords: 'jadwal kbm waktu mengajar jam' },
            { title: 'Pengaturan Sistem & Sekolah', category: 'Super Admin', url: '/admin/manajemen-pengaturan', icon: 'settings', keywords: 'setting konfigurasi logo nama sekolah' },
            { title: 'Audit Trail & Aktivitas Sistem', category: 'Super Admin', url: '/admin/audit-log', icon: 'shield-check', keywords: 'audit log riwayat aktivitas keamanan' },
            { title: 'System Error & Exception Log', category: 'Super Admin', url: '/admin/system-error-log', icon: 'alert-triangle', keywords: 'error log debug sistem exception' },

            // Tata Usaha
            { title: 'Dashboard Tata Usaha', category: 'Tata Usaha', url: '/tata-usaha/dashboard', icon: 'layout-dashboard', keywords: 'tu tata usaha dashboard' },
            { title: 'Kalender Akademik & Hari Libur', category: 'Tata Usaha', url: '/tata-usaha/kalender-akademik', icon: 'calendar-days', keywords: 'kalender libur semester tahun ajaran' },
            { title: 'Generator Surat & PDF Arsip', category: 'Tata Usaha', url: '/tata-usaha/manajemen-surat', icon: 'file-text', keywords: 'surat generator mutasi aktif keterangan arsip' },
            { title: 'Direktori Karyawan & Staf', category: 'Tata Usaha', url: '/tata-usaha/manajemen-karyawan', icon: 'briefcase', keywords: 'karyawan staf kepegawaian tata usaha' },
            { title: 'Jadwal Piket Guru & Jam Masuk', category: 'Tata Usaha', url: '/tata-usaha/piket-guru', icon: 'clock', keywords: 'piket guru jam datang checkin' },
            { title: 'Plotting Siswa & Halaqah', category: 'Tata Usaha', url: '/tata-usaha/plotting-siswa-kelas', icon: 'users-round', keywords: 'plotting siswa rombel halaqah tahfidz' },
            { title: 'Dual Kenaikan & Kelulusan Kelas', category: 'Tata Usaha', url: '/tata-usaha/kenaikan-kelas', icon: 'arrow-up-right', keywords: 'kenaikan kelas kelulusan alumni naik' },
            { title: 'Presensi Karyawan & Guru Terpusat', category: 'Tata Usaha', url: '/tata-usaha/input-absensi-karyawan', icon: 'user-check', keywords: 'absensi guru karyawan tata usaha hadir' },
            { title: 'Buku Induk Alumni', category: 'Tata Usaha', url: '/tata-usaha/data-alumni', icon: 'archive', keywords: 'alumni lulusan tamatan riwayat' },

            // Finance
            { title: 'Dashboard Keuangan', category: 'Keuangan', url: '/finance/dashboard', icon: 'wallet', keywords: 'finance kas keuangan uang kasir saldo' },
            { title: 'Manajemen & Rilis Tagihan SPP', category: 'Keuangan', url: '/finance/tagihan', icon: 'file-spreadsheet', keywords: 'tagihan spp iuran nominal rilis masal' },
            { title: 'Kasir Input Pembayaran (Resi)', category: 'Keuangan', url: '/finance/input-pembayaran', icon: 'receipt', keywords: 'bayar kasir input pembayaran resi kuitansi' },
            { title: 'Tabungan Santri / Siswa', category: 'Keuangan', url: '/finance/tabungan-siswa', icon: 'piggy-bank', keywords: 'tabungan setor tarik saldo santri' },
            { title: 'Peminjaman & Kasbon Guru', category: 'Keuangan', url: '/finance/peminjaman', icon: 'hand-coins', keywords: 'kasbon pinjaman cicilan guru karyawan' },
            { title: 'Pengajuan Dana Anggaran BOS', category: 'Keuangan', url: '/finance/pengajuan-dana', icon: 'file-check', keywords: 'proposal pengajuan dana anggaran bos belanja' },
            { title: 'Manajemen & Slip Gaji Guru', category: 'Keuangan', url: '/finance/gaji-guru', icon: 'banknote', keywords: 'gaji slip penghasilan ustadz transfer' },
            { title: 'Buku Kas Umum (Arus Kas)', category: 'Keuangan', url: '/finance/arus-kas', icon: 'book-open-check', keywords: 'arus kas masuk keluar buku kas umum bku' },
            { title: 'Laporan Pemasukan Kas', category: 'Keuangan', url: '/finance/laporan-pemasukan', icon: 'trending-up', keywords: 'laporan pemasukan export excel pdf' },
            { title: 'Laporan Pengeluaran Operasional', category: 'Keuangan', url: '/finance/laporan-pengeluaran', icon: 'trending-down', keywords: 'laporan pengeluaran belanja export' },
            { title: 'Laporan Rekap Tunggakan Siswa', category: 'Keuangan', url: '/finance/laporan-tunggakan', icon: 'alert-circle', keywords: 'laporan tunggakan belum lunas jatuh tempo' },

            // Guru
            { title: 'Dashboard Guru', category: 'Guru', url: '/guru/dashboard', icon: 'layout-dashboard', keywords: 'guru mengajar dashboard ustadz' },
            { title: 'Absensi Mandiri Check-In/Out', category: 'Guru', url: '/guru/absensi-diri', icon: 'clock', keywords: 'presensi diri check in pulang datang' },
            { title: 'Presensi Harian Santri Kelas', category: 'Guru', url: '/guru/absensi-siswa', icon: 'clipboard-check', keywords: 'absen siswa hadir alpa sakit izin' },
            { title: 'Input Nilai Sumatif & Formatif', category: 'Guru', url: '/guru/input-nilai-sumatif', icon: 'edit-3', keywords: 'nilai sumatif formatif ujian ulangan' },
            { title: 'Input Penilaian Tahfidz Quran', category: 'Guru', url: '/guru/input-nilai-tahfidz', icon: 'book-marked', keywords: 'tahfidz mutabaah tajwid juz surat ayat' },
            { title: 'Kelola & Cetak Rapor Siswa', category: 'Guru', url: '/guru/kelola-rapor', icon: 'award', keywords: 'rapor cetak pdf nilai akhir semester' },
            { title: 'Jadwal Mengajar Saya', category: 'Guru', url: '/guru/jadwal-mengajar', icon: 'calendar', keywords: 'jadwal mengajar jam kelas mapel' },
            { title: 'Manajemen Remedial Siswa', category: 'Guru', url: '/guru/remedial', icon: 'rotate-ccw', keywords: 'remedial perbaikan nilai tuntas' },
            { title: 'Slip Gaji Saya', category: 'Guru', url: '/guru/slip-gaji-saya', icon: 'receipt', keywords: 'slip gaji saya pribadi ustadz' },

            // Siswa / Murid
            { title: 'Tagihan & Riwayat Pembayaran SPP', category: 'Murid', url: '/murid/tagihan', icon: 'credit-card', keywords: 'tagihan spp bayar santri riwayat resi' },
            { title: 'Kehadiran Presensi Saya', category: 'Murid', url: '/murid/kehadiran', icon: 'calendar-check', keywords: 'absen saya kehadiran santri rekap' },
            { title: 'E-Rapor Hasil Belajar', category: 'Murid', url: '/murid/rapor', icon: 'file-badge', keywords: 'rapor nilai saya hasil ujian prestasi' },
            { title: 'Rekap Setoran Tahfidz Quran', category: 'Murid', url: '/murid/tahfidz', icon: 'book-open', keywords: 'tahfidz setoran hafalan juz surat' }
        ],
        get filteredMenus() {
            if (!this.searchQuery.trim()) {
                return this.menus.slice(0, 8);
            }
            const q = this.searchQuery.toLowerCase();
            return this.menus.filter(m => 
                m.title.toLowerCase().includes(q) || 
                m.category.toLowerCase().includes(q) || 
                m.keywords.toLowerCase().includes(q)
            ).slice(0, 10);
        },
        open() {
            this.isOpen = true;
            this.searchQuery = '';
            this.selectedIndex = 0;
            this.$nextTick(() => {
                const el = document.getElementById('command-palette-input');
                if (el) el.focus();
            });
        },
        close() {
            this.isOpen = false;
        },
        selectNext() {
            if (this.selectedIndex < this.filteredMenus.length - 1) {
                this.selectedIndex++;
            }
        },
        selectPrev() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
            }
        },
        navigateSelected() {
            const item = this.filteredMenus[this.selectedIndex];
            if (item) {
                window.location.href = item.url;
            }
        }
    }"
    @keydown.window.ctrl.k.prevent="open()"
    @keydown.window.meta.k.prevent="open()"
    @open-command-palette.window="open()"
    x-cloak
>
    <!-- Backdrop & Modal Container -->
    <div 
        x-show="isOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-start justify-center pt-16 sm:pt-24 p-4"
        @keydown.escape.window="close()"
        style="display: none;"
    >
        <div 
            @click.away="close()"
            x-transition:enter="transition ease-out duration-200 transform"
            x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
            class="bg-white border border-stone-200 rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden flex flex-col"
        >
            <!-- Search Bar Input Header -->
            <div class="px-5 py-4 border-b border-stone-200 flex items-center gap-3 bg-stone-50/70">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input 
                    id="command-palette-input"
                    type="text" 
                    x-model="searchQuery" 
                    @keydown.down.prevent="selectNext()"
                    @keydown.up.prevent="selectPrev()"
                    @keydown.enter.prevent="navigateSelected()"
                    placeholder="Ketik nama menu, fitur, atau aksi..." 
                    class="w-full bg-transparent border-none text-stone-900 text-sm font-semibold placeholder-stone-400 focus:outline-none focus:ring-0"
                />
                <span class="text-[10px] font-bold text-stone-400 bg-white border border-stone-200 px-2 py-1 rounded-lg shrink-0 shadow-2xs">ESC</span>
            </div>

            <!-- Results List -->
            <div class="p-2 max-h-80 overflow-y-auto divide-y divide-stone-100">
                <template x-for="(item, index) in filteredMenus" :key="item.url">
                    <a 
                        :href="item.url" 
                        @mouseenter="selectedIndex = index"
                        class="flex items-center justify-between p-3 rounded-2xl transition cursor-pointer"
                        :class="selectedIndex === index ? 'bg-emerald-600 text-white shadow-xs' : 'hover:bg-stone-100 text-stone-700'"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <div 
                                class="p-2 rounded-xl"
                                :class="selectedIndex === index ? 'bg-emerald-700 text-white' : 'bg-stone-100 text-emerald-700'"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-black truncate" :class="selectedIndex === index ? 'text-white' : 'text-stone-900'" x-text="item.title"></h4>
                                <span class="text-[10px] font-bold opacity-80 uppercase tracking-wider" :class="selectedIndex === index ? 'text-emerald-100' : 'text-stone-400'" x-text="item.category"></span>
                            </div>
                        </div>

                        <span class="text-xs font-semibold shrink-0" :class="selectedIndex === index ? 'text-emerald-200' : 'text-stone-400'">Buka &rarr;</span>
                    </a>
                </template>

                <template x-if="filteredMenus.length === 0">
                    <div class="py-8 text-center text-stone-400 text-xs">
                        Tidak ditemukan menu atau aksi untuk "<span class="font-bold" x-text="searchQuery"></span>"
                    </div>
                </template>
            </div>

            <!-- Footer Key Hints -->
            <div class="px-5 py-2.5 bg-stone-50 border-t border-stone-200 flex items-center justify-between text-[11px] text-stone-400 font-medium">
                <div class="flex items-center gap-2">
                    <span class="flex items-center gap-1 font-bold"><kbd class="bg-white border border-stone-200 px-1.5 py-0.5 rounded shadow-2xs">↑</kbd> <kbd class="bg-white border border-stone-200 px-1.5 py-0.5 rounded shadow-2xs">↓</kbd> Navigasi</span>
                    <span class="flex items-center gap-1 font-bold"><kbd class="bg-white border border-stone-200 px-1.5 py-0.5 rounded shadow-2xs">↵</kbd> Buka</span>
                </div>
                <span>Pintasan Cepat SIAKAD</span>
            </div>
        </div>
    </div>
</div>
