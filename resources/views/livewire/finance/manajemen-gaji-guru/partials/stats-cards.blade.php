{{-- Summary KPI Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-stone-500 block">Total Beban Anggaran</span>
            <span class="text-lg font-black text-stone-900 mt-0.5 block">Rp {{ number_format($statTotalAnggaran, 0, ',', '.') }}</span>
            <span class="text-[10px] text-stone-400 font-medium">Data terfilter saat ini</span>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shadow-2xs shrink-0">
            <x-lucide-calculator class="w-5 h-5" />
        </div>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 block">Gaji Sudah Dibayar</span>
            <span class="text-lg font-black text-emerald-950 mt-0.5 block">Rp {{ number_format($statTotalDibayar, 0, ',', '.') }}</span>
            <span class="text-[10px] text-emerald-600 font-semibold">{{ $statCountDibayar }} Pegawai Selesai</span>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-xs shrink-0">
            <x-lucide-check-circle-2 class="w-5 h-5" />
        </div>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 block">Gaji Masih Draf</span>
            <span class="text-lg font-black text-amber-950 mt-0.5 block">Rp {{ number_format($statTotalDraft, 0, ',', '.') }}</span>
            <span class="text-[10px] text-amber-600 font-semibold">{{ $statCountDraft }} Pegawai Menunggu</span>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shadow-2xs shrink-0">
            <x-lucide-clock class="w-5 h-5" />
        </div>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-700 block">Potongan Kasbon</span>
            <span class="text-lg font-black text-rose-950 mt-0.5 block">Rp {{ number_format($statTotalKasbon, 0, ',', '.') }}</span>
            <span class="text-[10px] text-rose-500 font-medium">Cicilan terpotong</span>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center shadow-2xs shrink-0">
            <x-lucide-piggy-bank class="w-5 h-5" />
        </div>
    </div>
</div>
