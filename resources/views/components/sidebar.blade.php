@php
    $role = auth()->user()->role->nama ?? '';
    
    // Define navigation items based on role
    $menuItems = match ($role) {
        'super_admin' => [
            ['title' => 'Dashboard', 'route' => 'super-admin.dashboard', 'icon' => 'home'],
            ['title' => 'Manajemen Siswa', 'route' => 'super-admin.siswa', 'icon' => 'users'],
            ['title' => 'Manajemen Guru', 'route' => 'super-admin.guru', 'icon' => 'user-check'],
            ['title' => 'Kelas & Mapel', 'route' => 'super-admin.kelas', 'icon' => 'layers'],
            ['title' => 'Jadwal Pelajaran', 'route' => 'super-admin.jadwal', 'icon' => 'calendar'],
            ['title' => 'Komponen Nilai', 'route' => 'super-admin.komponen-nilai', 'icon' => 'sliders'],
            ['title' => 'Notifikasi Masal', 'route' => 'super-admin.dashboard', 'icon' => 'bell'],
            ['title' => 'Audit Log', 'route' => 'super-admin.audit-log', 'icon' => 'activity'],
            ['title' => 'Pengaturan', 'route' => 'super-admin.pengaturan', 'icon' => 'settings'],
        ],
        'guru' => [
            ['title' => 'Dashboard', 'route' => 'guru.dashboard', 'icon' => 'home'],
            ['title' => 'Input Nilai Siswa', 'route' => 'guru.input-nilai', 'icon' => 'edit-3'],
            ['title' => 'Absensi Siswa', 'route' => 'guru.absensi-siswa', 'icon' => 'clipboard'],
            ['title' => 'Absensi Diri', 'route' => 'guru.absensi-diri', 'icon' => 'clock'],
            ['title' => 'Jadwal Mengajar', 'route' => 'guru.jadwal-mengajar', 'icon' => 'calendar'],
            ['title' => 'Notifikasi', 'route' => 'guru.dashboard', 'icon' => 'bell'],
        ],
        'murid' => [
            ['title' => 'Dashboard', 'route' => 'murid.dashboard', 'icon' => 'home'],
            ['title' => 'Rapor & Nilai', 'route' => 'murid.rapor', 'icon' => 'award'],
            ['title' => 'Kehadiran Saya', 'route' => 'murid.kehadiran', 'icon' => 'clipboard'],
            ['title' => 'Jadwal Pelajaran', 'route' => 'murid.jadwal', 'icon' => 'calendar'],
            ['title' => 'Tagihan SPP', 'route' => 'murid.tagihan', 'icon' => 'credit-card'],
            ['title' => 'Riwayat Aktivitas', 'route' => 'murid.dashboard', 'icon' => 'activity'],
            ['title' => 'Notifikasi', 'route' => 'murid.dashboard', 'icon' => 'bell'],
        ],
        'finance' => [
            ['title' => 'Dashboard', 'route' => 'finance.dashboard', 'icon' => 'home'],
            ['title' => 'Manajemen Tagihan', 'route' => 'finance.tagihan', 'icon' => 'file-text'],
            ['title' => 'Input Pembayaran', 'route' => 'finance.input-pembayaran', 'icon' => 'plus-circle'],
            ['title' => 'Arus Kas (Pengeluaran)', 'route' => 'finance.arus-kas', 'icon' => 'trending-down'],
            ['title' => 'Gaji Guru', 'route' => 'finance.dashboard', 'icon' => 'wallet'],
            ['title' => 'Dana BOS', 'route' => 'finance.dana-bos', 'icon' => 'box'],
            ['title' => 'Notifikasi', 'route' => 'finance.dashboard', 'icon' => 'bell'],
        ],
        default => [],
    };
@endphp

<aside class="fixed inset-y-0 left-0 z-20 flex flex-col w-64 bg-slate-900 border-r border-slate-800 transition-all duration-300">
    <!-- Header/Brand -->
    <div class="flex items-center gap-3 px-6 h-16 border-b border-slate-800">
        <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-600/10 border border-indigo-500/20">
            <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 019.918 5.842 50.45 50.45 0 00-2.658.814m-15.482 0a50.53 50.53 0 0115.482 0m-15.482 0v3.06c0 5.625 3.338 10.71 8.232 12.839m0-22.742V20.9" />
            </svg>
        </div>
        <div>
            <h2 class="text-sm font-bold text-white tracking-wide">SIAKAD</h2>
            <p class="text-[10px] text-slate-500 font-medium uppercase tracking-wider">{{ str_replace('_', ' ', $role) }}</p>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scrollbar">
        @foreach ($menuItems as $item)
            @php
                $isActive = request()->routeIs($item['route']);
            @endphp
            <a href="{{ route($item['route']) }}" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group
                      {{ $isActive 
                          ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' 
                          : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                
                @switch($item['icon'])
                    @case('home') <x-lucide-home class="w-4 h-4" /> @break
                    @case('users') <x-lucide-users class="w-4 h-4" /> @break
                    @case('user-check') <x-lucide-user-check class="w-4 h-4" /> @break
                    @case('layers') <x-lucide-layers class="w-4 h-4" /> @break
                    @case('calendar') <x-lucide-calendar class="w-4 h-4" /> @break
                    @case('sliders') <x-lucide-sliders class="w-4 h-4" /> @break
                    @case('bell') <x-lucide-bell class="w-4 h-4" /> @break
                    @case('activity') <x-lucide-activity class="w-4 h-4" /> @break
                    @case('settings') <x-lucide-settings class="w-4 h-4" /> @break
                    @case('edit-3') <x-lucide-edit-3 class="w-4 h-4" /> @break
                    @case('clipboard') <x-lucide-clipboard class="w-4 h-4" /> @break
                    @case('clock') <x-lucide-clock class="w-4 h-4" /> @break
                    @case('award') <x-lucide-award class="w-4 h-4" /> @break
                    @case('credit-card') <x-lucide-credit-card class="w-4 h-4" /> @break
                    @case('file-text') <x-lucide-file-text class="w-4 h-4" /> @break
                    @case('plus-circle') <x-lucide-plus-circle class="w-4 h-4" /> @break
                    @case('trending-down') <x-lucide-trending-down class="w-4 h-4" /> @break
                    @case('wallet') <x-lucide-wallet class="w-4 h-4" /> @break
                    @case('box') <x-lucide-box class="w-4 h-4" /> @break
                @endswitch

                <span>{{ $item['title'] }}</span>
            </a>
        @endforeach
    </nav>

    <!-- User Profile Footer -->
    <div class="p-4 border-t border-slate-800 bg-slate-950/40">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-indigo-400 text-sm select-none">
                {{ substr(auth()->user()->nama ?? 'U', 0, 2) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->nama ?? 'User' }}</p>
                <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email ?? auth()->user()->username }}</p>
            </div>
        </div>
        
        <!-- Logout Form -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                class="w-full flex items-center justify-center gap-2 py-2 px-4 rounded-xl border border-slate-800 hover:border-rose-500/20 text-xs font-semibold text-slate-400 hover:text-rose-400 hover:bg-rose-500/5 transition duration-200">
                <x-lucide-log-out class="w-3.5 h-3.5" />
                <span>Keluar Aplikasi</span>
            </button>
        </form>
    </div>
</aside>
