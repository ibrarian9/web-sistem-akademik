<!-- Filter & Search Bar -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
    <!-- Search Student -->
    <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama siswa atau NIS..." />

    <!-- Filter Bulan -->
    <select wire:model.live="filterBulan" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
        <option value="">Semua Periode</option>
        @foreach ($bulanOptions as $bln)
            <option value="{{ $bln }}">{{ $bln }}</option>
        @endforeach
    </select>

    <!-- Filter Kelas -->
    <select wire:model.live="filterKelas" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
        <option value="">Semua Kelas</option>
        @foreach ($classes as $c)
            <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
        @endforeach
    </select>

    <!-- Filter Jenis -->
    <select wire:model.live="filterJenis" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
        <option value="">Semua Kategori</option>
        @foreach ($jenisTagihans as $jt)
            <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
        @endforeach
    </select>

    <!-- Filter Status -->
    <select wire:model.live="filterStatus" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
        <option value="">Semua Status</option>
        <option value="belum_bayar">Belum Bayar</option>
        <option value="sebagian">Sebagian</option>
        <option value="lunas">Lunas</option>
    </select>
</div>

<!-- Date Range Filter Row -->
<div class="flex items-center justify-between gap-4 border-t border-stone-100 pt-3 flex-wrap">
    <div class="flex items-center gap-2">
        <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Periode:</span>
        <x-date-filter model="filterPeriode" startDateModel="startDate" endDateModel="endDate" />
    </div>

    @if (count($selectedIds) > 0)
        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
            {{ count($selectedIds) }} siswa dipilih
        </span>
    @endif
</div>
