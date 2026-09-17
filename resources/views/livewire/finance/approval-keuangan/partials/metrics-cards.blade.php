<!-- Metrics Cards -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
    <!-- Menunggu -->
    <button wire:click="$set('filterStatus', 'menunggu')" type="button"
            class="text-left p-4 rounded-2xl border transition-all duration-200 {{ $filterStatus === 'menunggu' ? 'bg-amber-50/70 border-amber-300 ring-2 ring-amber-400 shadow-sm' : 'bg-white border-stone-200 hover:border-stone-300 hover:shadow-sm' }}">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Menunggu Persetujuan</span>
            <span class="p-2 rounded-xl bg-amber-100 text-amber-600">
                <x-lucide-clock class="w-4 h-4" />
            </span>
        </div>
        <p class="text-2xl font-black text-amber-700 mt-2">{{ $counts['menunggu'] }}</p>
        <p class="text-xs text-amber-600 font-medium mt-0.5">Membutuhkan tindakan</p>
    </button>

    <!-- Disetujui -->
    <button wire:click="$set('filterStatus', 'disetujui')" type="button"
            class="text-left p-4 rounded-2xl border transition-all duration-200 {{ $filterStatus === 'disetujui' ? 'bg-green-50/70 border-green-300 ring-2 ring-green-400 shadow-sm' : 'bg-white border-stone-200 hover:border-stone-300 hover:shadow-sm' }}">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Disetujui</span>
            <span class="p-2 rounded-xl bg-green-100 text-green-600">
                <x-lucide-check-circle class="w-4 h-4" />
            </span>
        </div>
        <p class="text-2xl font-black text-green-700 mt-2">{{ $counts['disetujui'] }}</p>
        <p class="text-xs text-green-600 font-medium mt-0.5">Berhasil dieksekusi</p>
    </button>

    <!-- Ditolak -->
    <button wire:click="$set('filterStatus', 'ditolak')" type="button"
            class="text-left p-4 rounded-2xl border transition-all duration-200 {{ $filterStatus === 'ditolak' ? 'bg-red-50/70 border-red-300 ring-2 ring-red-400 shadow-sm' : 'bg-white border-stone-200 hover:border-stone-300 hover:shadow-sm' }}">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Ditolak</span>
            <span class="p-2 rounded-xl bg-red-100 text-red-600">
                <x-lucide-x-circle class="w-4 h-4" />
            </span>
        </div>
        <p class="text-2xl font-black text-red-700 mt-2">{{ $counts['ditolak'] }}</p>
        <p class="text-xs text-red-600 font-medium mt-0.5">Tidak diterapkan</p>
    </button>

    <!-- Dibatalkan -->
    <button wire:click="$set('filterStatus', 'dibatalkan')" type="button"
            class="text-left p-4 rounded-2xl border transition-all duration-200 {{ $filterStatus === 'dibatalkan' ? 'bg-stone-100 border-stone-400 ring-2 ring-stone-400 shadow-sm' : 'bg-white border-stone-200 hover:border-stone-300 hover:shadow-sm' }}">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Dibatalkan</span>
            <span class="p-2 rounded-xl bg-stone-100 text-stone-600">
                <x-lucide-ban class="w-4 h-4" />
            </span>
        </div>
        <p class="text-2xl font-black text-stone-700 mt-2">{{ $counts['dibatalkan'] }}</p>
        <p class="text-xs text-stone-500 font-medium mt-0.5">Dibatalkan pemohon</p>
    </button>

    <!-- Semua -->
    <button wire:click="$set('filterStatus', 'semua')" type="button"
            class="text-left p-4 rounded-2xl border transition-all duration-200 {{ $filterStatus === 'semua' ? 'bg-stone-100 border-stone-400 ring-2 ring-stone-400 shadow-sm' : 'bg-white border-stone-200 hover:border-stone-300 hover:shadow-sm' }}">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Total Riwayat</span>
            <span class="p-2 rounded-xl bg-stone-100 text-stone-600">
                <x-lucide-list class="w-4 h-4" />
            </span>
        </div>
        <p class="text-2xl font-black text-stone-800 mt-2">{{ $counts['total'] }}</p>
        <p class="text-xs text-stone-500 font-medium mt-0.5">Seluruh pengajuan</p>
    </button>
</div>
