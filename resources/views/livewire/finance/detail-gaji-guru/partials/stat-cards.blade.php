<!-- Financial KPI Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 block">Total Gaji Diterima (THP)</span>
            <span class="text-lg font-black text-emerald-950 mt-0.5 block">Rp {{ number_format($statTotalDibayar, 0, ',', '.') }}</span>
            <span class="text-[10px] text-emerald-600 font-semibold">{{ $statCountDibayar }} Periode Terbayar</span>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-xs shrink-0">
            <x-lucide-wallet class="w-5 h-5" />
        </div>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-stone-500 block">Total Gaji Pokok & Berkala</span>
            <span class="text-lg font-black text-stone-900 mt-0.5 block">Rp {{ number_format($statTotalPokok, 0, ',', '.') }}</span>
            <span class="text-[10px] text-stone-400 font-medium">Honorarium dasar</span>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-stone-100 text-stone-700 border border-stone-200 flex items-center justify-center shadow-2xs shrink-0">
            <x-lucide-calculator class="w-5 h-5" />
        </div>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-cyan-700 block">Total Tunjangan & Insentif</span>
            <span class="text-lg font-black text-cyan-950 mt-0.5 block">Rp {{ number_format($statTotalInsentif, 0, ',', '.') }}</span>
            <span class="text-[10px] text-cyan-600 font-semibold">Kinerja, ekskul, kehadiran</span>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-cyan-50 text-cyan-700 border border-cyan-200 flex items-center justify-center shadow-2xs shrink-0">
            <x-lucide-award class="w-5 h-5" />
        </div>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-700 block">Potongan Kasbon Terbayar</span>
            <span class="text-lg font-black text-rose-950 mt-0.5 block">Rp {{ number_format($statTotalKasbon, 0, ',', '.') }}</span>
            <span class="text-[10px] text-rose-500 font-medium">Cicilan pinjaman lunas</span>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center shadow-2xs shrink-0">
            <x-lucide-piggy-bank class="w-5 h-5" />
        </div>
    </div>
</div>
