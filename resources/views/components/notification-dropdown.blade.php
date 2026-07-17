@php
    $unreadCount = auth()->check() ? auth()->user()->notifikasis()->whereNull('dibaca_pada')->count() : 0;
    $notifications = auth()->check() ? auth()->user()->notifikasis()->latest()->take(5)->get() : collect();
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <!-- Bell Button -->
    <button @click="open = !open" 
        class="relative p-2 text-stone-500 hover:text-stone-700 hover:bg-stone-100 rounded-xl transition duration-200 focus:outline-none">
        <x-lucide-bell class="w-5 h-5" />
        @if ($unreadCount > 0)
            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full ring-2 ring-white animate-pulse"></span>
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
        class="absolute right-0 mt-2.5 w-80 bg-white border border-stone-200 rounded-2xl shadow-xl py-2 z-30 focus:outline-none"
        style="display: none;">
        
        <div class="flex items-center justify-between px-4 py-2 border-b border-stone-200">
            <span class="text-xs font-bold text-stone-700 tracking-wide uppercase">Notifikasi</span>
            @if ($unreadCount > 0)
                <span class="text-xs bg-green-50 text-green-700 font-semibold px-2 py-0.5 rounded-full border border-green-200">
                    {{ $unreadCount }} Baru
                </span>
            @endif
        </div>

        <div class="max-h-64 overflow-y-auto divide-y divide-stone-100 custom-scrollbar">
            @forelse ($notifications as $notif)
                <a href="#" class="block px-4 py-3 hover:bg-stone-50 transition duration-200">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 shrink-0">
                            @switch($notif->jenis)
                                @case('tunggakan')
                                    <span class="inline-flex p-1.5 rounded-lg bg-red-50 text-red-600 border border-red-200"><x-lucide-alert-triangle class="w-3.5 h-3.5" /></span>
                                    @break
                                @case('rapor_terbit')
                                    <span class="inline-flex p-1.5 rounded-lg bg-green-50 text-green-600 border border-green-200"><x-lucide-award class="w-3.5 h-3.5" /></span>
                                    @break
                                @case('absensi')
                                    <span class="inline-flex p-1.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-200"><x-lucide-clock class="w-3.5 h-3.5" /></span>
                                    @break
                                @default
                                    <span class="inline-flex p-1.5 rounded-lg bg-blue-50 text-blue-600 border border-blue-200"><x-lucide-info class="w-3.5 h-3.5" /></span>
                            @endswitch
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-stone-800 truncate">{{ $notif->judul }}</p>
                            <p class="text-xs text-stone-500 mt-0.5 line-clamp-2 leading-relaxed">{{ $notif->isi_pesan }}</p>
                            <p class="text-xs text-stone-400 mt-1 font-medium">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="py-8 text-center">
                    <x-lucide-bell-off class="w-8 h-8 text-stone-300 mx-auto stroke-1.5" />
                    <p class="text-sm text-stone-400 mt-2 font-medium">Tidak ada notifikasi baru</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
