@php
    $unreadCount = auth()->check() ? auth()->user()->notifikasis()->whereNull('dibaca_pada')->count() : 0;
    $notifications = auth()->check() ? auth()->user()->notifikasis()->latest()->take(5)->get() : collect();
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <!-- Bell Button -->
    <button @click="open = !open" 
        class="relative p-2 text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 rounded-xl transition duration-200 focus:outline-none">
        <x-lucide-bell class="w-5 h-5" />
        @if ($unreadCount > 0)
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-indigo-500 rounded-full ring-2 ring-slate-900 animate-pulse"></span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2.5 w-80 bg-slate-900 border border-slate-800 rounded-2xl shadow-xl shadow-slate-950/50 py-2 z-30 focus:outline-none"
        style="display: none;">
        
        <div class="flex items-center justify-between px-4 py-2 border-b border-slate-800">
            <span class="text-xs font-bold text-white tracking-wide uppercase">Notifikasi</span>
            @if ($unreadCount > 0)
                <span class="text-[10px] bg-indigo-600/10 text-indigo-400 font-semibold px-2 py-0.5 rounded-full border border-indigo-500/20">
                    {{ $unreadCount }} Baru
                </span>
            @endif
        </div>

        <div class="max-h-64 overflow-y-auto divide-y divide-slate-800/50 custom-scrollbar">
            @forelse ($notifications as $notif)
                <a href="#" class="block px-4 py-3 hover:bg-slate-800/40 transition duration-200">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 shrink-0">
                            @switch($notif->jenis)
                                @case('tunggakan')
                                    <span class="inline-flex p-1.5 rounded-lg bg-rose-500/10 text-rose-400 border border-rose-500/20"><x-lucide-alert-triangle class="w-3.5 h-3.5" /></span>
                                    @break
                                @case('rapor_terbit')
                                    <span class="inline-flex p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><x-lucide-award class="w-3.5 h-3.5" /></span>
                                    @break
                                @case('absensi')
                                    <span class="inline-flex p-1.5 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20"><x-lucide-clock class="w-3.5 h-3.5" /></span>
                                    @break
                                @default
                                    <span class="inline-flex p-1.5 rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/20"><x-lucide-info class="w-3.5 h-3.5" /></span>
                            @endswitch
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-white truncate">{{ $notif->judul }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-2 leading-relaxed">{{ $notif->isi_pesan }}</p>
                            <p class="text-[9px] text-slate-500 mt-1 font-medium">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="py-8 text-center">
                    <x-lucide-bell-off class="w-8 h-8 text-slate-600 mx-auto stroke-1.5" />
                    <p class="text-xs text-slate-500 mt-2 font-medium">Tidak ada notifikasi baru</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
