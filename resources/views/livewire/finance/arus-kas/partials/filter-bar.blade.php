<!-- Top Tab & Toolbar Row -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
    <!-- Main Tab Selector -->
    <div class="flex items-center p-1 bg-stone-100 border border-stone-200 rounded-xl overflow-x-auto shadow-2xs">
        <button type="button" 
            wire:click="selectTab('semua')" 
            class="px-4 py-2 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-1.5 {{ $tab === 'semua' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
            <x-lucide-list class="w-3.5 h-3.5" />
            <span>Semua Arus Kas</span>
        </button>
        <button type="button" 
            wire:click="selectTab('masuk')" 
            class="px-4 py-2 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-1.5 {{ $tab === 'masuk' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
            <x-lucide-arrow-down-left class="w-3.5 h-3.5" />
            <span>Kas Masuk Saja</span>
        </button>
        <button type="button" 
            wire:click="selectTab('keluar')" 
            class="px-4 py-2 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-1.5 {{ $tab === 'keluar' ? 'bg-rose-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
            <x-lucide-arrow-up-right class="w-3.5 h-3.5" />
            <span>Kas Keluar Saja</span>
        </button>
    </div>

    <!-- Search Bar -->
    <div class="w-full lg:max-w-md">
        <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari transaksi, siswa, no resi, penerima..." />
    </div>
</div>

<!-- Secondary Sub-stream Pills Row (Contextual based on Tab) -->
<div class="flex items-center gap-2 overflow-x-auto pb-1">
    <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wider shrink-0">Filter Stream:</span>
    <button type="button" wire:click="selectStream('semua')" class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $stream === 'semua' ? 'bg-stone-900 text-white border-stone-900' : 'bg-stone-50 text-stone-600 border-stone-200 hover:bg-stone-100' }}">
        Semua
    </button>

    @if ($tab === 'semua' || $tab === 'masuk')
        <button type="button" wire:click="selectStream('spp')" class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $stream === 'spp' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100' }}">
            SPP & Tagihan Siswa
        </button>
        <button type="button" wire:click="selectStream('infaq')" class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $stream === 'infaq' ? 'bg-amber-600 text-white border-amber-600' : 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100' }}">
            Kas Masuk Yayasan (Infaq)
        </button>
        <button type="button" wire:click="selectStream('tabungan')" class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $stream === 'tabungan' ? 'bg-purple-600 text-white border-purple-600' : 'bg-purple-50 text-purple-800 border-purple-200 hover:bg-purple-100' }}">
            Setoran Tabungan
        </button>
    @endif

    @if ($tab === 'semua' || $tab === 'keluar')
        <button type="button" wire:click="selectStream('operasional')" class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $stream === 'operasional' ? 'bg-rose-600 text-white border-rose-600' : 'bg-rose-50 text-rose-800 border-rose-200 hover:bg-rose-100' }}">
            Operasional Yayasan
        </button>
        <button type="button" wire:click="selectStream('gaji')" class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $stream === 'gaji' ? 'bg-violet-600 text-white border-violet-600' : 'bg-violet-50 text-violet-800 border-violet-200 hover:bg-violet-100' }}">
            Gaji Guru
        </button>
        <button type="button" wire:click="selectStream('kasbon')" class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $stream === 'kasbon' ? 'bg-teal-600 text-white border-teal-600' : 'bg-teal-50 text-teal-800 border-teal-200 hover:bg-teal-100' }}">
            Kasbon Guru
        </button>
    @endif
</div>

<!-- Comprehensive Filter Toolbar Row -->
<div class="border-t border-stone-100 pt-3 space-y-3">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <!-- Left Filter Controls -->
        <div class="flex items-center gap-2.5 flex-wrap flex-1">
            <!-- Date Range Filter -->
            <div class="flex items-center gap-1.5">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Periode:</span>
                <x-date-filter model="filterPeriode" startDateModel="startDate" endDateModel="endDate" />
            </div>

            <!-- Contextual Category Filter (Kas Masuk) -->
            @if ($tab === 'semua' || $tab === 'masuk')
                <div class="flex items-center gap-1.5">
                    <select wire:model.live="filterKategoriMasuk" class="bg-stone-50 border border-stone-200 rounded-xl px-2.5 py-1.5 text-stone-700 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                        <option value="">Semua Kategori Masuk</option>
                        @foreach ($kategoriMasukOptions as $optMasuk)
                            <option value="{{ $optMasuk }}">{{ $optMasuk }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Contextual Category Filter (Kas Keluar) -->
            @if ($tab === 'semua' || $tab === 'keluar')
                <div class="flex items-center gap-1.5">
                    <select wire:model.live="filterKategoriKeluar" class="bg-stone-50 border border-stone-200 rounded-xl px-2.5 py-1.5 text-stone-700 text-xs font-bold focus:ring-2 focus:ring-rose-600 focus:bg-white transition shadow-2xs">
                        <option value="">Semua Kategori Keluar</option>
                        @foreach ($kategoriKeluarOptions as $optKeluar)
                            <option value="{{ $optKeluar['id'] }}">{{ $optKeluar['nama'] }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Payment Method Filter -->
            <div class="flex items-center gap-1.5">
                <select wire:model.live="filterMetode" class="bg-stone-50 border border-stone-200 rounded-xl px-2.5 py-1.5 text-stone-700 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    <option value="semua">Semua Metode Pembayaran</option>
                    <option value="tunai">Tunai / Cash</option>
                    <option value="transfer">Transfer Bank / Payroll</option>
                    <option value="qris">QRIS / Non-Tunai</option>
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

        <!-- Right Action Controls (Export & Counter) -->
        <div class="flex items-center gap-3 flex-wrap">
            <div class="text-xs font-bold text-stone-600">
                Menampilkan <span class="text-stone-900 font-extrabold">{{ $paginatedTransactions->total() }}</span> transaksi
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" wire:click="exportPdf" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-xl text-xs font-bold border border-rose-200 transition shadow-2xs cursor-pointer" title="Cetak Jurnal PDF Sesuai Filter">
                    <x-lucide-printer class="w-3.5 h-3.5 text-rose-600" />
                    <span>PDF</span>
                </button>
                <button type="button" wire:click="exportExcel" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-xl text-xs font-bold border border-emerald-200 transition shadow-2xs cursor-pointer" title="Ekspor Jurnal Excel Sesuai Filter">
                    <x-lucide-file-spreadsheet class="w-3.5 h-3.5 text-emerald-600" />
                    <span>Excel</span>
                </button>
            </div>
        </div>
    </div>
</div>
