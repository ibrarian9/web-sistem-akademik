<!-- 3 Core Metric Cards (Cash Inflow, Cash Outflow, Net Balance) -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="relative group h-full flex flex-col">
        <x-stat-card 
            title="Total Kas Masuk" 
            :value="'Rp ' . number_format($totalInflow, 0, ',', '.')" 
            :subtitle="$tab === 'masuk' ? '✓ Filter aktif (Klik untuk reset)' : 'SPP, infaq yayasan, & tabungan (Klik filter)'"
            icon="trending-up" 
            variant="emerald" 
            wire:click="filterByCard('masuk')"
            role="button"
            tabindex="0"
            class="h-full flex-1 cursor-pointer select-none hover:shadow-md hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 {{ $tab === 'masuk' ? 'ring-4 ring-emerald-400 ring-offset-2 shadow-md' : '' }}"
        >
            <div class="flex items-center justify-between text-[11px] font-bold text-white/90">
                <span>{{ $tab === 'masuk' ? 'Filter aktif (klik reset)' : 'Klik filter transaksi masuk' }}</span>
                <x-lucide-chevron-right class="w-3.5 h-3.5" />
            </div>
        </x-stat-card>
        @if ($tab === 'masuk')
            <span class="absolute top-2 right-2 px-2 py-0.5 bg-white text-emerald-800 text-[10px] font-black rounded-full shadow-2xs uppercase tracking-wider">
                Aktif
            </span>
        @endif
    </div>

    <div class="relative group h-full flex flex-col">
        <x-stat-card 
            title="Total Kas Keluar" 
            :value="'Rp ' . number_format($totalOutflow, 0, ',', '.')" 
            :subtitle="$tab === 'keluar' ? '✓ Filter aktif (Klik untuk reset)' : 'Beban operasional, gaji, kasbon (Klik filter)'"
            icon="trending-down" 
            variant="rose" 
            wire:click="filterByCard('keluar')"
            role="button"
            tabindex="0"
            class="h-full flex-1 cursor-pointer select-none hover:shadow-md hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 {{ $tab === 'keluar' ? 'ring-4 ring-rose-400 ring-offset-2 shadow-md' : '' }}"
        >
            <div class="flex items-center justify-between text-[11px] font-bold text-white/90">
                <span>{{ $tab === 'keluar' ? 'Filter aktif (klik reset)' : 'Klik filter transaksi keluar' }}</span>
                <x-lucide-chevron-right class="w-3.5 h-3.5" />
            </div>
        </x-stat-card>
        @if ($tab === 'keluar')
            <span class="absolute top-2 right-2 px-2 py-0.5 bg-white text-rose-800 text-[10px] font-black rounded-full shadow-2xs uppercase tracking-wider">
                Aktif
            </span>
        @endif
    </div>

    <div class="relative group h-full flex flex-col">
        <x-stat-card 
            title="Surplus / Saldo Kas Bersih" 
            :value="($netCashFlow < 0 ? '- Rp ' : 'Rp ') . number_format(abs($netCashFlow), 0, ',', '.')" 
            :subtitle="$tab === 'semua' ? ($netCashFlow >= 0 ? 'Surplus kas periode ini (Semua Arus Kas)' : 'Defisit kas periode ini (Semua Arus Kas)') : 'Klik untuk tampilkan semua arus kas'"
            icon="wallet" 
            :variant="$netCashFlow >= 0 ? 'soft-teal' : 'rose'" 
            wire:click="selectTab('semua')"
            role="button"
            tabindex="0"
            class="h-full flex-1 cursor-pointer select-none hover:shadow-md hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 {{ $tab === 'semua' ? 'ring-2 ring-stone-400 ring-offset-1 shadow-xs' : '' }}"
        >
            <div class="flex items-center justify-between text-[11px] font-bold {{ $netCashFlow >= 0 ? 'text-teal-800' : 'text-white/90' }}">
                <span>{{ $tab === 'semua' ? 'Semua arus kas aktif' : 'Klik tampilkan semua arus kas' }}</span>
                <x-lucide-list-filter class="w-3.5 h-3.5" />
            </div>
        </x-stat-card>
        @if ($tab === 'semua')
            <span class="absolute top-2 right-2 px-2 py-0.5 {{ $netCashFlow >= 0 ? 'bg-teal-700 text-white' : 'bg-white text-rose-800' }} text-[10px] font-black rounded-full shadow-2xs uppercase tracking-wider">
                Semua
            </span>
        @endif
    </div>
</div>
