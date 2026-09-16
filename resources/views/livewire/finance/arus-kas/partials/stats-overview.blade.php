<!-- 3 Core Metric Cards (Cash Inflow, Cash Outflow, Net Balance) -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="relative group">
        <x-stat-card 
            title="Total Kas Masuk" 
            :value="'Rp ' . number_format($totalInflow, 0, ',', '.')" 
            :subtitle="$tab === 'masuk' ? '✓ Filter aktif (Klik untuk reset)' : 'SPP, infaq yayasan, & tabungan (Klik filter)'"
            icon="trending-up" 
            variant="emerald" 
            wire:click="filterByCard('masuk')"
            role="button"
            tabindex="0"
            class="cursor-pointer select-none hover:shadow-md hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 {{ $tab === 'masuk' ? 'ring-4 ring-emerald-400 ring-offset-2 shadow-md' : '' }}"
        />
        @if ($tab === 'masuk')
            <span class="absolute top-2 right-2 px-2 py-0.5 bg-white text-emerald-800 text-[10px] font-black rounded-full shadow-2xs uppercase tracking-wider">
                Aktif
            </span>
        @endif
    </div>

    <div class="relative group">
        <x-stat-card 
            title="Total Kas Keluar" 
            :value="'Rp ' . number_format($totalOutflow, 0, ',', '.')" 
            :subtitle="$tab === 'keluar' ? '✓ Filter aktif (Klik untuk reset)' : 'Beban operasional, gaji, kasbon (Klik filter)'"
            icon="trending-down" 
            variant="rose" 
            wire:click="filterByCard('keluar')"
            role="button"
            tabindex="0"
            class="cursor-pointer select-none hover:shadow-md hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 {{ $tab === 'keluar' ? 'ring-4 ring-rose-400 ring-offset-2 shadow-md' : '' }}"
        />
        @if ($tab === 'keluar')
            <span class="absolute top-2 right-2 px-2 py-0.5 bg-white text-rose-800 text-[10px] font-black rounded-full shadow-2xs uppercase tracking-wider">
                Aktif
            </span>
        @endif
    </div>

    <div class="relative group">
        <x-stat-card 
            title="Surplus / Saldo Kas Bersih" 
            :value="($netCashFlow < 0 ? '- Rp ' : 'Rp ') . number_format(abs($netCashFlow), 0, ',', '.')" 
            :subtitle="$tab === 'semua' ? ($netCashFlow >= 0 ? 'Surplus kas periode ini (Semua Arus Kas)' : 'Defisit kas periode ini (Semua Arus Kas)') : 'Klik untuk tampilkan semua arus kas'"
            icon="wallet" 
            :variant="$netCashFlow >= 0 ? 'white' : 'rose'" 
            wire:click="selectTab('semua')"
            role="button"
            tabindex="0"
            class="cursor-pointer select-none hover:shadow-md hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 {{ $tab === 'semua' ? 'ring-2 ring-stone-400 ring-offset-1 shadow-xs' : '' }}"
        />
        @if ($tab === 'semua')
            <span class="absolute top-2 right-2 px-2 py-0.5 bg-stone-100 text-stone-700 text-[10px] font-black rounded-full border border-stone-300 shadow-2xs uppercase tracking-wider">
                Semua
            </span>
        @endif
    </div>
</div>
