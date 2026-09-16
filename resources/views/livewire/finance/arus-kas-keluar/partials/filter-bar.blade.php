<!-- Stream Tabs & Search Row -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
    <!-- Stream Selector Tabs -->
    <div class="flex items-center p-1 bg-stone-100 border border-stone-200 rounded-xl overflow-x-auto shadow-2xs">
        <button type="button" 
            wire:click="selectStream('semua')" 
            class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition shrink-0 {{ $stream === 'semua' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
            Semua Stream
        </button>
        <button type="button" 
            wire:click="selectStream('operasional')" 
            class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-1.5 {{ $stream === 'operasional' ? 'bg-rose-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
            <span class="w-1.5 h-1.5 rounded-full bg-rose-300"></span>
            <span>Operasional Yayasan</span>
        </button>
        <button type="button" 
            wire:click="selectStream('gaji')" 
            class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-1.5 {{ $stream === 'gaji' ? 'bg-purple-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
            <span class="w-1.5 h-1.5 rounded-full bg-purple-300"></span>
            <span>Gaji Guru</span>
        </button>
        <button type="button" 
            wire:click="selectStream('peminjaman')" 
            class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-1.5 {{ $stream === 'peminjaman' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
            <span>Kasbon Guru</span>
        </button>
    </div>

    <!-- Search Bar & Category Filter -->
    <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap w-full lg:max-w-xl">
        <div class="w-full flex-1">
            <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari transaksi, penerima, atau keterangan..." />
        </div>

        @if ($stream === 'semua' || $stream === 'operasional')
            <select wire:model.live="filterKategori" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs shrink-0">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c['id'] }}">{{ $c['nama'] }}</option>
                @endforeach
            </select>
        @endif
    </div>
</div>

<!-- Comprehensive Filter Toolbar Row -->
<div class="flex items-center justify-between gap-4 border-t border-stone-100 pt-3 flex-wrap">
    <div class="flex items-center gap-2.5 flex-wrap flex-1">
        <div class="flex items-center gap-1.5">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Periode:</span>
            <x-date-filter model="filterPeriode" startDateModel="startDate" endDateModel="endDate" />
        </div>

        <!-- Payment Method Filter -->
        <div class="flex items-center gap-1.5">
            <select wire:model.live="filterMetode" class="bg-stone-50 border border-stone-200 rounded-xl px-2.5 py-1.5 text-stone-700 text-xs font-bold focus:ring-2 focus:ring-rose-600 focus:bg-white transition shadow-2xs">
                <option value="semua">Semua Metode Beban</option>
                <option value="tunai">Tunai / Kasbon</option>
                <option value="transfer">Transfer Bank / Payroll</option>
                <option value="bank">Bank</option>
            </select>
        </div>

        <!-- Nominal Range Filters -->
        <div class="flex items-center gap-1 bg-stone-50 border border-stone-200 px-2 py-1 rounded-xl shadow-2xs">
            <span class="text-[11px] font-bold text-stone-400">Rp</span>
            <input type="number" wire:model.live.debounce.400ms="nominalMin" placeholder="Nominal Min" class="w-24 bg-transparent border-0 p-0 text-xs font-bold text-stone-800 placeholder-stone-400 focus:ring-0" />
            <span class="text-stone-300 text-xs">-</span>
            <input type="number" wire:model.live.debounce.400ms="nominalMax" placeholder="Nominal Max" class="w-24 bg-transparent border-0 p-0 text-xs font-bold text-stone-800 placeholder-stone-400 focus:ring-0" />
        </div>

        <!-- Reset Filter Button -->
        @if ($this->activeFilterCount > 0)
            <button type="button" wire:click="resetFilters" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold border border-stone-300 transition shadow-2xs cursor-pointer">
                <x-lucide-refresh-cw class="w-3 h-3 text-stone-500" />
                <span>Reset ({{ $this->activeFilterCount }})</span>
            </button>
        @endif
    </div>

    @if (count($selectedIds) > 0 && !auth()->user()->isSuperAdmin2() && auth()->user()->role?->nama !== 'finance')
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-rose-700 bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-200">
                {{ count($selectedIds) }} pengeluaran dipilih
            </span>
            <x-button variant="danger-solid" size="xs" icon="trash-2" wire:click="bulkDelete" data-confirm="Hapus seluruh pengeluaran yang dipilih?">
                Hapus Terpilih
            </x-button>
        </div>
    @endif
</div>
