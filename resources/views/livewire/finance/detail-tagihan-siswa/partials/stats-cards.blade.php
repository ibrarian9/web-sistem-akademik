<!-- Financial Stat Cards for This Student -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-1.5">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Tagihan</span>
            <div class="p-2 bg-blue-50 text-blue-700 rounded-xl border border-blue-200">
                <x-lucide-file-text class="w-4 h-4" />
            </div>
        </div>
        <div class="text-2xl font-black text-stone-900">
            Rp {{ number_format($totalNominal, 0, ',', '.') }}
        </div>
        <div class="text-[11px] text-stone-500 font-medium">Akumulasi seluruh tagihan</div>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-1.5">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Terbayar</span>
            <div class="p-2 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200">
                <x-lucide-check-circle class="w-4 h-4" />
            </div>
        </div>
        <div class="text-2xl font-black text-emerald-800">
            Rp {{ number_format($totalTerbayar, 0, ',', '.') }}
        </div>
        <div class="text-[11px] text-emerald-700 font-medium">{{ $countLunas }} tagihan lunas</div>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-1.5">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Sisa Tunggakan</span>
            <div class="p-2 bg-rose-50 text-rose-700 rounded-xl border border-rose-200">
                <x-lucide-alert-circle class="w-4 h-4" />
            </div>
        </div>
        <div class="text-2xl font-black text-rose-800">
            Rp {{ number_format($totalSisa, 0, ',', '.') }}
        </div>
        <div class="text-[11px] text-rose-700 font-medium">{{ $countBelumLunas }} tagihan belum lunas</div>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-1.5">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Status Pembayaran</span>
            <div class="p-2 bg-purple-50 text-purple-700 rounded-xl border border-purple-200">
                <x-lucide-pie-chart class="w-4 h-4" />
            </div>
        </div>
        <div class="text-2xl font-black {{ $totalSisa == 0 && $totalNominal > 0 ? 'text-emerald-800' : 'text-amber-800' }}">
            @if ($totalNominal == 0)
                0%
            @else
                {{ round(($totalTerbayar / max(1, $totalNominal)) * 100) }}%
            @endif
        </div>
        <div class="text-[11px] text-stone-500 font-medium">Persentase pelunasan</div>
    </div>
</div>
