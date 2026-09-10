<!-- WIDGET MATRIKS 12 BULAN SPP SISWA -->
<div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-3">
    <div class="flex items-center justify-between flex-wrap gap-2 border-b border-stone-100 pb-3">
        <div class="flex items-center gap-2">
            <div class="p-2 bg-indigo-50 text-indigo-700 rounded-xl border border-indigo-200">
                <x-lucide-calendar class="w-4 h-4" />
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-tight">Matriks Status SPP 12 Bulan (T.A. {{ $activeTAName }})</h3>
                <p class="text-xs text-stone-500">Ringkasan status pembayaran SPP siswa per bulan dalam satu tampilan komprehensif.</p>
            </div>
        </div>
        <div class="flex items-center gap-3 text-[11px] font-bold">
            <span class="inline-flex items-center gap-1 text-emerald-700"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Lunas</span>
            <span class="inline-flex items-center gap-1 text-amber-700"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Dicicil</span>
            <span class="inline-flex items-center gap-1 text-rose-700"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Belum Bayar</span>
            <span class="inline-flex items-center gap-1 text-stone-400"><span class="w-2.5 h-2.5 rounded-full bg-stone-300"></span> Belum Terbit</span>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2.5">
        @foreach ($sppMatrix as $mKey => $mVal)
            <div class="p-3 rounded-xl border transition {{ $mVal['status'] === 'lunas' ? 'bg-emerald-50/60 border-emerald-200 text-emerald-950' : ($mVal['status'] === 'sebagian' ? 'bg-amber-50/60 border-amber-200 text-amber-950' : ($mVal['status'] === 'belum_bayar' ? 'bg-rose-50/60 border-rose-200 text-rose-950' : 'bg-stone-50 border-stone-200 text-stone-400')) }}">
                <div class="flex items-center justify-between gap-1 mb-1">
                    <span class="text-xs font-black uppercase">{{ $mKey }}</span>
                    @if ($mVal['status'] === 'lunas')
                        <x-lucide-check-circle class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    @elseif ($mVal['status'] === 'sebagian')
                        <x-lucide-clock class="w-3.5 h-3.5 text-amber-600 shrink-0" />
                    @elseif ($mVal['status'] === 'belum_bayar')
                        <x-lucide-alert-circle class="w-3.5 h-3.5 text-rose-600 shrink-0" />
                    @else
                        <x-lucide-minus-circle class="w-3.5 h-3.5 text-stone-300 shrink-0" />
                    @endif
                </div>

                @if ($mVal['has_bill'])
                    <div class="text-xs font-black">
                        Rp {{ number_format($mVal['nominal'], 0, ',', '.') }}
                    </div>
                    <div class="text-[10px] font-bold mt-1">
                        @if ($mVal['status'] === 'lunas')
                            <span class="text-emerald-700">Lunas</span>
                        @elseif ($mVal['status'] === 'sebagian')
                            <span class="text-amber-700">Sisa: Rp {{ number_format($mVal['sisa'], 0, ',', '.') }}</span>
                        @else
                            <span class="text-rose-700">Belum Bayar</span>
                        @endif
                    </div>
                @else
                    <div class="text-[11px] font-medium text-stone-400 italic">
                        Belum Diterbitkan
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
