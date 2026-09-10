<!-- Controls: Search & Event Selector -->
<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:max-w-xl">
        <!-- Search bar -->
        <div class="w-full flex-1">
            <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari aktivitas, nama user, atau IP..." />
        </div>
        
        <!-- Event selector -->
        <select wire:model.live="filterEvent" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
            <option value="">Semua Event</option>
            @foreach ($events as $evt)
                <option value="{{ $evt }}">{{ ucfirst($evt) }}</option>
            @endforeach
        </select>

        <!-- Role selector -->
        <select wire:model.live="filterRole" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
            <option value="">Semua Role</option>
            @foreach ($roles as $r)
                <option value="{{ $r->nama }}">{{ ucwords(str_replace('_', ' ', $r->nama)) }}</option>
            @endforeach
        </select>
    </div>
    
    <div class="flex items-center gap-2">
        <span class="text-xs text-stone-600 font-bold">Tampilkan:</span>
        <select wire:model.live="perPage" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
            <option value="10">10 Baris</option>
            <option value="20">20 Baris</option>
            <option value="50">50 Baris</option>
            <option value="100">100 Baris</option>
        </select>
    </div>
</div>
