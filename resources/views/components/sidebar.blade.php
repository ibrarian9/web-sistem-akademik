@php
    $role = auth()->user()->role->nama ?? '';
    
    // Define navigation items based on role
    $menuItems = match ($role) {
        'super_admin' => [
            ['title' => 'Dashboard', 'route' => 'super-admin.dashboard', 'icon' => 'home'],
            ['title' => 'Keuangan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Kelola Tagihan', 'route' => 'finance.tagihan', 'icon' => 'file-text'],
            ['title' => 'Laporan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Laporan Absensi Siswa', 'route' => 'super-admin.laporan.absensi-siswa', 'icon' => 'file-text'],
            ['title' => 'Laporan Absensi Guru', 'route' => 'super-admin.laporan.absensi-guru', 'icon' => 'clipboard'],
            ['title' => 'Laporan Rekap Nilai', 'route' => 'super-admin.laporan.rekap-nilai', 'icon' => 'award'],
            ['title' => 'Manajemen', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Manajemen User', 'route' => 'super-admin.user', 'icon' => 'users'],
            ['title' => 'Audit Log', 'route' => 'super-admin.audit-log', 'icon' => 'activity'],
            ['title' => 'Pengaturan Sistem', 'route' => 'super-admin.pengaturan', 'icon' => 'settings'],
            ['title' => 'Lainnya', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Notifikasi & Pengumuman', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
        'tata_usaha' => [
            ['title' => 'Dashboard', 'route' => 'tata-usaha.dashboard', 'icon' => 'home'],
            ['title' => 'Data Master', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Data Siswa', 'route' => 'tata-usaha.siswa', 'icon' => 'users'],
            ['title' => 'Data Guru', 'route' => 'tata-usaha.guru', 'icon' => 'user-check'],
            ['title' => 'Kelas & Mapel', 'route' => 'tata-usaha.kelas', 'icon' => 'layers'],
            ['title' => 'Penugasan Guru', 'route' => 'tata-usaha.dashboard', 'icon' => 'link'],
            ['title' => 'Jadwal & Akademik', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Jadwal Pelajaran', 'route' => 'tata-usaha.jadwal', 'icon' => 'calendar'],
            ['title' => 'Komponen Nilai', 'route' => 'tata-usaha.komponen-nilai', 'icon' => 'sliders'],
            ['title' => 'Laporan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Laporan Absensi Siswa', 'route' => 'tata-usaha.laporan.absensi-siswa', 'icon' => 'file-text'],
            ['title' => 'Rekap Absensi Guru', 'route' => 'tata-usaha.laporan.absensi-guru', 'icon' => 'clipboard'],
            ['title' => 'Laporan Rekap Nilai', 'route' => 'tata-usaha.laporan.rekap-nilai', 'icon' => 'award'],
            ['title' => 'Lainnya', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Notifikasi', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
        'guru' => [
            ['title' => 'Dashboard', 'route' => 'guru.dashboard', 'icon' => 'home'],
            ['title' => 'Kelas Saya', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Input Nilai Siswa', 'route' => 'guru.input-nilai', 'icon' => 'edit-3'],
            ['title' => 'Absensi Siswa', 'route' => 'guru.absensi-siswa', 'icon' => 'clipboard'],
            ['title' => 'Terbitkan Rapor', 'route' => 'guru.kelola-rapor', 'icon' => 'book-open'],
            ['title' => 'Kehadiran', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Absensi Diri', 'route' => 'guru.absensi-diri', 'icon' => 'clock'],
            ['title' => 'Jadwal Mengajar', 'route' => 'guru.jadwal-mengajar', 'icon' => 'calendar'],
            ['title' => 'Laporan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Laporan Absensi Siswa', 'route' => 'guru.laporan.absensi-siswa', 'icon' => 'file-text'],
            ['title' => 'Laporan Rekap Nilai', 'route' => 'guru.laporan.rekap-nilai', 'icon' => 'award'],
            ['title' => 'Lainnya', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Notifikasi', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
        'murid' => [
            ['title' => 'Dashboard', 'route' => 'murid.dashboard', 'icon' => 'home'],
            ['title' => 'Akademik', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Rapor & Nilai', 'route' => 'murid.rapor', 'icon' => 'award'],
            ['title' => 'Kehadiran Saya', 'route' => 'murid.kehadiran', 'icon' => 'clipboard'],
            ['title' => 'Jadwal Pelajaran', 'route' => 'murid.jadwal', 'icon' => 'calendar'],
            ['title' => 'Keuangan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Tagihan SPP', 'route' => 'murid.tagihan', 'icon' => 'credit-card'],
            ['title' => 'Lainnya', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Riwayat Aktivitas', 'route' => 'murid.riwayat-aktivitas', 'icon' => 'activity'],
            ['title' => 'Notifikasi', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
        'finance' => [
            ['title' => 'Dashboard', 'route' => 'finance.dashboard', 'icon' => 'home'],
            ['title' => 'Pemasukan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Overview Pembayaran', 'route' => 'finance.overview-pembayaran', 'icon' => 'eye'],
            ['title' => 'Manajemen Tagihan', 'route' => 'finance.tagihan', 'icon' => 'file-text'],
            ['title' => 'Input Pembayaran', 'route' => 'finance.input-pembayaran', 'icon' => 'plus-circle'],
            ['title' => 'Pengeluaran & Guru', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Arus Kas', 'route' => 'finance.arus-kas', 'icon' => 'trending-down'],
            ['title' => 'Gaji Guru', 'route' => 'finance.gaji-guru', 'icon' => 'wallet'],
            ['title' => 'Kasbon Guru', 'route' => 'finance.peminjaman', 'icon' => 'link'],
            ['title' => 'Laporan Keuangan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Laporan Tunggakan', 'route' => 'finance.laporan.tunggakan', 'icon' => 'file-text'],
            ['title' => 'Laporan Pemasukan', 'route' => 'finance.laporan.pemasukan', 'icon' => 'activity'],
            ['title' => 'Laporan Pengeluaran', 'route' => 'finance.laporan.pengeluaran', 'icon' => 'trending-down'],
            ['title' => 'Lainnya', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Dana BOS', 'route' => 'finance.dana-bos', 'icon' => 'box'],
            ['title' => 'Notifikasi', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
        default => [],
    };

    $roleLabel = match ($role) {
        'super_admin' => 'Kepala Yayasan',
        'tata_usaha' => 'Tata Usaha',
        'guru' => 'Guru',
        'murid' => 'Murid / Wali',
        'finance' => 'Bendahara',
        default => ucwords(str_replace('_', ' ', $role)),
    };
@endphp

<aside class="fixed inset-y-0 left-0 z-20 flex flex-col w-64 bg-white border-r border-stone-200 shadow-sm transition-all duration-300">
    <!-- Header/Brand -->
    <div class="flex items-center gap-3 px-6 h-16 border-b border-stone-200">
        <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-green-50 border border-green-200">
            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 019.918 5.842 50.45 50.45 0 00-2.658.814m-15.482 0a50.53 50.53 0 0115.482 0m-15.482 0v3.06c0 5.625 3.338 10.71 8.232 12.839m0-22.742V20.9" />
            </svg>
        </div>
        <div>
            <h2 class="text-sm font-bold text-stone-800 tracking-wide">SIAKAD</h2>
            <p class="text-xs text-stone-500 font-medium">{{ $roleLabel }}</p>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-5 space-y-1 overflow-y-auto custom-scrollbar">
        @foreach ($menuItems as $item)
            @if (isset($item['section']) && $item['section'])
                {{-- Section Label --}}
                <div class="pt-4 pb-1.5 px-3">
                    <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">{{ $item['title'] }}</span>
                </div>
            @else
                @php
                    $isActive = $item['route'] && request()->routeIs($item['route']);
                    // Prevent false positives on items sharing the dashboard route
                    if ($isActive && str_contains($item['route'], 'dashboard') && $item['title'] !== 'Dashboard') {
                        $isActive = false;
                    }
                @endphp
                <a href="{{ $item['route'] ? route($item['route']) : '#' }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group
                          {{ $isActive 
                              ? 'bg-green-50 text-green-700 border-l-[3px] border-green-600 shadow-sm' 
                              : 'text-stone-600 hover:bg-stone-100 hover:text-stone-800' }}">
                    
                    @switch($item['icon'])
                        @case('home') <x-lucide-home class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('users') <x-lucide-users class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('user-check') <x-lucide-user-check class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('layers') <x-lucide-layers class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('calendar') <x-lucide-calendar class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('sliders') <x-lucide-sliders class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('bell') <x-lucide-bell class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('activity') <x-lucide-activity class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('settings') <x-lucide-settings class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('edit-3') <x-lucide-edit-3 class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('clipboard') <x-lucide-clipboard class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('clock') <x-lucide-clock class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('award') <x-lucide-award class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('credit-card') <x-lucide-credit-card class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('file-text') <x-lucide-file-text class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('plus-circle') <x-lucide-plus-circle class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('trending-down') <x-lucide-trending-down class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('wallet') <x-lucide-wallet class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('box') <x-lucide-box class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('eye') <x-lucide-eye class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('bar-chart-2') <x-lucide-bar-chart-2 class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('link') <x-lucide-link class="w-[18px] h-[18px] shrink-0" /> @break
                    @endswitch

                    <span>{{ $item['title'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <!-- User Profile Footer -->
    <div class="p-4 border-t border-stone-200 bg-stone-50/60">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-full bg-green-50 border border-green-200 flex items-center justify-center font-bold text-green-700 text-sm select-none">
                {{ substr(auth()->user()->nama ?? 'U', 0, 2) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-stone-800 truncate">{{ auth()->user()->nama ?? 'User' }}</p>
                <p class="text-xs text-stone-500 truncate">{{ auth()->user()->email ?? auth()->user()->username }}</p>
            </div>
        </div>
        
        <!-- Logout Form -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-stone-200 hover:border-red-300 text-sm font-medium text-stone-500 hover:text-red-600 hover:bg-red-50 transition duration-200">
                <x-lucide-log-out class="w-4 h-4" />
                <span>Keluar Aplikasi</span>
            </button>
        </form>
    </div>
</aside>
