<div class="relative" x-data="{ open: false }">
    <!-- Bell Trigger Button -->
    <button @click="open = !open" @click.outside="open = false" class="relative p-2 text-stone-500 hover:text-stone-700 bg-white border border-stone-200 rounded-xl transition duration-150">
        <x-lucide-bell class="w-[18px] h-[18px]" />
        
        @if ($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[9px] font-bold leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-red-500 rounded-full">
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
         class="absolute right-0 mt-2.5 w-80 bg-white border border-stone-200 rounded-2xl shadow-xl py-3 z-50 space-y-2"
         style="display: none;">
        
        <div class="flex items-center justify-between px-4 pb-2 border-b border-stone-200">
            <h3 class="text-xs font-bold text-stone-700 uppercase tracking-wider">Notifikasi</h3>
            
            @if ($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs font-semibold text-green-600 hover:text-green-700 uppercase tracking-wider">
                    Tandai Semua Dibaca
                </button>
            @endif
        </div>

        <div class="max-h-64 overflow-y-auto divide-y divide-stone-100 custom-scrollbar">
            @forelse ($notifications as $notif)
                <div class="p-3 hover:bg-stone-50 flex items-start justify-between gap-2 group transition duration-150">
                    <div class="space-y-0.5 flex-1">
                        <h4 class="text-sm font-semibold text-stone-800 leading-snug">{{ $notif['judul'] }}</h4>
                        <p class="text-xs text-stone-500 leading-normal">{{ $notif['pesan'] }}</p>
                        <span class="text-xs text-stone-400 font-medium block pt-0.5">{{ $notif['time'] }}</span>
                    </div>
                    <button wire:click="markAsRead({{ $notif['id'] }})" class="p-1 text-stone-400 hover:text-green-600 transition duration-150 rounded" title="Tandai dibaca">
                        <x-lucide-check class="w-4 h-4" />
                    </button>
                </div>
            @empty
                <div class="py-8 text-center text-stone-400 font-medium text-sm">
                    Tidak ada notifikasi baru.
                </div>
            @endforelse
        </div>
    </div>
</div>
