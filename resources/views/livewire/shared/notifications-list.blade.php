<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Notifikasi & Pengumuman</h2>
            <p class="text-sm text-stone-500 font-medium">Kotak masuk pemberitahuan sistem dan pengumuman akademik Anda.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button wire:click="markAllAsRead" 
                    class="inline-flex items-center justify-center gap-2 py-2 px-4 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-sm font-semibold text-stone-700 shadow-sm transition duration-150">
                <x-lucide-check-check class="w-4 h-4 text-green-600" />
                <span>Tandai Semua Dibaca</span>
            </button>
        </div>
    </div>

    <!-- Filters & List Card -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <!-- Tabs -->
        <div class="flex border-b border-stone-200 px-6 bg-stone-50/50">
            <button wire:click="$set('filter', 'all')" 
                    class="py-4 px-4 text-sm font-bold border-b-2 transition duration-150 -mb-px flex items-center gap-2
                           {{ $filter === 'all' ? 'border-green-600 text-green-700' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                <span>Semua</span>
            </button>
            <button wire:click="$set('filter', 'unread')" 
                    class="py-4 px-4 text-sm font-bold border-b-2 transition duration-150 -mb-px flex items-center gap-2
                           {{ $filter === 'unread' ? 'border-green-600 text-green-700' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                <span>Belum Dibaca</span>
            </button>
            <button wire:click="$set('filter', 'read')" 
                    class="py-4 px-4 text-sm font-bold border-b-2 transition duration-150 -mb-px flex items-center gap-2
                           {{ $filter === 'read' ? 'border-green-600 text-green-700' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                <span>Sudah Dibaca</span>
            </button>
        </div>

        <!-- Notification list -->
        <div class="divide-y divide-stone-100">
            @forelse ($notifications as $notif)
                @php
                    $isUnread = is_null($notif->dibaca_pada);
                    $iconName = 'bell';
                    $iconColor = 'bg-stone-50 text-stone-600 border-stone-200';
                    
                    switch($notif->jenis) {
                        case 'tunggakan':
                            $iconName = 'credit-card';
                            $iconColor = 'bg-red-50 text-red-700 border-red-100';
                            break;
                        case 'rapor_terbit':
                            $iconName = 'award';
                            $iconColor = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                            break;
                        case 'absensi':
                            $iconName = 'clipboard';
                            $iconColor = 'bg-amber-50 text-amber-700 border-amber-100';
                            break;
                        case 'pengumuman':
                            $iconName = 'bell';
                            $iconColor = 'bg-sky-50 text-sky-700 border-sky-100';
                            break;
                    }

                    // Direct links mapping based on user role
                    $link = null;
                    $roleName = auth()->user()->role->nama ?? '';
                    if ($notif->tabel_terkait === 'tagihan') {
                        $link = $roleName === 'murid' ? route('murid.tagihan') : ($roleName === 'finance' ? route('finance.tagihan') : null);
                    } elseif ($notif->tabel_terkait === 'rapor') {
                        $link = $roleName === 'murid' ? route('murid.rapor') : null;
                    } elseif ($notif->tabel_terkait === 'absensi_siswa') {
                        $link = $roleName === 'murid' ? route('murid.kehadiran') : null;
                    } elseif ($notif->tabel_terkait === 'nilai') {
                        $link = $roleName === 'murid' ? route('murid.rapor') : null;
                    }
                @endphp
                <div class="p-6 flex items-start gap-4 transition duration-150 relative {{ $isUnread ? 'bg-green-50/10' : '' }}">
                    <!-- Unread mark dot -->
                    @if ($isUnread)
                        <div class="absolute left-2.5 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-green-600" title="Belum dibaca"></div>
                    @endif

                    <!-- Icon Wrapper -->
                    <div class="w-10 h-10 rounded-xl border flex items-center justify-center shrink-0 {{ $iconColor }}">
                        @switch($iconName)
                            @case('credit-card') <x-lucide-credit-card class="w-5 h-5" /> @break
                            @case('award') <x-lucide-award class="w-5 h-5" /> @break
                            @case('clipboard') <x-lucide-clipboard class="w-5 h-5" /> @break
                            @default <x-lucide-bell class="w-5 h-5" />
                        @endswitch
                    </div>

                    <!-- Text Contents -->
                    <div class="flex-1 space-y-1 min-w-0">
                        <div class="flex items-start justify-between gap-4">
                            <h4 class="text-sm font-bold text-stone-800 leading-snug {{ $isUnread ? 'font-extrabold' : '' }}">
                                {{ $notif->judul }}
                            </h4>
                            <span class="text-xs text-stone-400 font-medium shrink-0 pt-0.5">
                                {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-stone-600 leading-relaxed max-w-3xl">
                            {{ $notif->isi_pesan }}
                        </p>

                        <!-- Action Link -->
                        @if ($link)
                            <div class="pt-2">
                                <a href="{{ $link }}" 
                                   class="inline-flex items-center gap-1.5 text-xs font-bold text-green-700 hover:text-green-800 transition duration-150">
                                    <span>Lihat Halaman Terkait</span>
                                    <x-lucide-arrow-right class="w-3.5 h-3.5" />
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Individual mark read button -->
                    @if ($isUnread)
                        <button wire:click="markAsRead({{ $notif->id }})" 
                                class="p-2 text-stone-400 hover:text-green-600 hover:bg-stone-50 rounded-xl transition duration-150 shrink-0" 
                                title="Tandai dibaca">
                            <x-lucide-check class="w-4 h-4" />
                        </button>
                    @endif
                </div>
            @empty
                <div class="py-16 text-center">
                    <div class="w-12 h-12 rounded-full bg-stone-100 flex items-center justify-center mx-auto text-stone-400 mb-3">
                        <x-lucide-bell-off class="w-6 h-6" />
                    </div>
                    <h3 class="text-sm font-bold text-stone-700">Tidak ada notifikasi</h3>
                    <p class="text-xs text-stone-400 mt-1">Notifikasi yang sesuai dengan filter Anda akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination Footer -->
        @if ($notifications->hasPages())
            <div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
