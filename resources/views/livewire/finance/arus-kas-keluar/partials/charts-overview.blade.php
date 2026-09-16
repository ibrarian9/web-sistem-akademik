<!-- VISUAL ANALYTICS & CHARTS SECTION -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- 1. Monthly Outflow Trend Chart (2 Cols) -->
    <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4 flex flex-col justify-between">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-stone-100 pb-3">
            <div>
                <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                    <x-lucide-bar-chart-3 class="w-4 h-4 text-rose-600" />
                    <span>Tren Arus Kas Keluar (6 Bulan Terakhir)</span>
                </h3>
                <p class="text-[11px] text-stone-500 font-medium mt-0.5">Komposisi pengeluaran operasional yayasan, penggajian guru, dan kasbon.</p>
            </div>
            <!-- Chart Legend -->
            <div class="flex items-center gap-3 text-[11px] font-bold flex-wrap">
                <span class="flex items-center gap-1.5 text-stone-700">
                    <span class="w-2.5 h-2.5 rounded-sm bg-rose-500 inline-block"></span> Operasional
                </span>
                <span class="flex items-center gap-1.5 text-stone-700">
                    <span class="w-2.5 h-2.5 rounded-sm bg-purple-500 inline-block"></span> Gaji Guru
                </span>
                <span class="flex items-center gap-1.5 text-stone-700">
                    <span class="w-2.5 h-2.5 rounded-sm bg-emerald-500 inline-block"></span> Kasbon
                </span>
            </div>
        </div>

        <!-- Bar Chart Display -->
        <div class="pt-4 pb-2">
            <div class="grid grid-cols-6 gap-2 sm:gap-4 items-end h-56 border-b border-stone-200 px-2">
                @foreach ($monthlyChartData as $mData)
                    <div class="flex flex-col items-center h-full justify-end group relative">
                        <!-- Tooltip on hover -->
                        <div class="opacity-0 group-hover:opacity-100 pointer-events-none absolute -top-14 bg-stone-900 text-white text-[10px] font-bold py-1.5 px-2.5 rounded-xl shadow-xl transition duration-150 z-20 whitespace-nowrap text-center">
                            <div>{{ $mData['label'] }}</div>
                            <div class="text-rose-300 font-black">Rp {{ number_format($mData['total'], 0, ',', '.') }}</div>
                        </div>

                        <!-- Stacked Bar Column -->
                        <div class="w-full max-w-[48px] bg-stone-100 rounded-t-xl overflow-hidden flex flex-col-reverse transition-all duration-300 group-hover:scale-105 shadow-2xs" style="height: {{ max(10, $mData['height_percentage']) }}%;">
                            <!-- Operasional segment -->
                            @if ($mData['operasional'] > 0)
                                <div class="bg-rose-500 w-full" style="height: {{ $mData['op_pct'] }}%;" title="Operasional: Rp {{ number_format($mData['operasional'], 0, ',', '.') }}"></div>
                            @endif
                            <!-- Gaji segment -->
                            @if ($mData['gaji'] > 0)
                                <div class="bg-purple-500 w-full" style="height: {{ $mData['gaji_pct'] }}%;" title="Gaji: Rp {{ number_format($mData['gaji'], 0, ',', '.') }}"></div>
                            @endif
                            <!-- Kasbon segment -->
                            @if ($mData['peminjaman'] > 0)
                                <div class="bg-emerald-500 w-full" style="height: {{ $mData['loan_pct'] }}%;" title="Kasbon: Rp {{ number_format($mData['peminjaman'], 0, ',', '.') }}"></div>
                            @endif
                        </div>

                        <!-- Bar Nominal Value -->
                        <span class="text-[10px] font-mono font-bold text-stone-700 mt-2 truncate w-full text-center">
                            {{ $mData['total'] >= 1000000 ? round($mData['total'] / 1000000, 1) . 'M' : number_format($mData['total'] / 1000, 0) . 'k' }}
                        </span>
                        <!-- Bar Month Label -->
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-tight truncate w-full text-center">
                            {{ $mData['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Footer summary badge -->
        <div class="pt-2 flex items-center justify-between text-xs text-stone-500 font-medium">
            <span>* Angka dalam grafik terupdate otomatis dari transaksi kas internal sekolah</span>
            <span class="font-bold text-stone-700">Puncak Pengeluaran: Rp {{ number_format($maxMonthTotal, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- 2. Stream Breakdown & Top Categories Card (1 Col) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2 border-b border-stone-100 pb-3">
            <x-lucide-pie-chart class="w-4 h-4 text-emerald-700" />
            <span>Proporsi Alokasi Stream</span>
        </h3>

        @php
            $opShare = $totalOutflowAll > 0 ? round(($totalOperasional / $totalOutflowAll) * 100, 1) : 0;
            $gajiShare = $totalOutflowAll > 0 ? round(($totalGaji / $totalOutflowAll) * 100, 1) : 0;
            $loanShare = $totalOutflowAll > 0 ? round(($totalPeminjaman / $totalOutflowAll) * 100, 1) : 0;
        @endphp

        <!-- Stream Allocation Bars -->
        <div class="space-y-3">
            <div>
                <div class="flex justify-between text-xs font-bold text-stone-700 mb-1">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>Operasional Yayasan</span>
                    </span>
                    <span>{{ $opShare }}% (Rp {{ number_format($totalOperasional, 0, ',', '.') }})</span>
                </div>
                <div class="w-full h-2 bg-stone-100 rounded-full overflow-hidden">
                    <div class="h-full bg-rose-500 rounded-full" style="width: {{ $opShare }}%;"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-xs font-bold text-stone-700 mb-1">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        <span>Gaji & Honor Guru</span>
                    </span>
                    <span>{{ $gajiShare }}% (Rp {{ number_format($totalGaji, 0, ',', '.') }})</span>
                </div>
                <div class="w-full h-2 bg-stone-100 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-500 rounded-full" style="width: {{ $gajiShare }}%;"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-xs font-bold text-stone-700 mb-1">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Pencairan Kasbon</span>
                    </span>
                    <span>{{ $loanShare }}% (Rp {{ number_format($totalPeminjaman, 0, ',', '.') }})</span>
                </div>
                <div class="w-full h-2 bg-stone-100 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $loanShare }}%;"></div>
                </div>
            </div>
        </div>

        <!-- Top Spending Categories List -->
        <div class="pt-3 border-t border-stone-100 space-y-2">
            <h4 class="text-xs font-bold text-stone-600 uppercase tracking-wider">Kategori Operasional Terbesar:</h4>
            <div class="space-y-1.5">
                @forelse ($categoryBreakdown as $cItem)
                    <div class="flex items-center justify-between text-xs p-2 bg-stone-50 rounded-xl border border-stone-200">
                        <span class="font-bold text-stone-800">{{ $cItem['nama'] }}</span>
                        <div class="text-right">
                            <span class="font-black text-rose-700 block">Rp {{ number_format($cItem['nominal'], 0, ',', '.') }}</span>
                            <span class="text-[10px] text-stone-400 font-semibold">{{ $cItem['percentage'] }}%</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-stone-400 italic">Belum ada rincian kategori tercatat.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
