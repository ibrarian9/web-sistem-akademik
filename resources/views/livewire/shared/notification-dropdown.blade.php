<div class="relative" x-data="{ open: false }">
    <!-- Bell Trigger Button -->
    <button @click="open = !open" @click.outside="open = false" class="relative p-2 text-slate-400 hover:text-white bg-slate-900 border border-slate-800 rounded-xl transition duration-150">
        <x-lucide-bell class="w-4.5 h-4.5" />
        
        @if ($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[8px] font-black leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-indigo-600 rounded-full">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2.5 w-80 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl py-3 z-50 space-y-2"
         style="display: none;">
        
        <div class="flex items-center justify-between px-4 pb-2 border-b border-slate-850">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Notifikasi</h3>
            
            @if ($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[9px] font-bold text-indigo-400 hover:text-indigo-300 uppercase tracking-wider">
                    Tandai Semua Dibaca
                </button>
            @endif
        </div>

        <div class="max-h-64 overflow-y-auto divide-y divide-slate-850 custom-scrollbar">
            @forelse ($notifications as $notif)
                <div class="p-3 hover:bg-slate-950/40 flex items-start justify-between gap-2 group transition duration-150">
                    <div class="space-y-0.5 flex-1">
                        <h4 class="text-[11px] font-bold text-white leading-snug">{{ $notif['judul'] }}</h4>
                        <p class="text-[10px] text-slate-405 leading-normal">{{ $notif['pesan'] }}</p>
                        <span class="text-[8px] text-slate-500 font-medium block pt-0.5">{{ $notif['time'] }}</span>
                    </div>
                    <button wire:click="markAsRead({{ $notif['id'] }})" class="p-1 text-slate-600 hover:text-indigo-455 transition duration-150 rounded" title="Tandai dibaca">
                        <x-lucide-check class="w-3.5 h-3.5" />
                    </button>
                </div>
            @empty
                <div class="py-8 text-center text-slate-500 font-semibold text-[11px]">
                    Tidak ada notifikasi baru.
                </div>
            @endforelse
        </div>
    </div>
</div>
