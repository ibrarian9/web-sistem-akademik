<!-- VISUAL ANALYTICS: DUAL BAR MONTHLY INFLOW VS OUTFLOW CHART -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- 1. Dual Bar Monthly Comparison (2 Cols) -->
    <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4 flex flex-col justify-between">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-stone-100 pb-3">
            <div>
                <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                    <x-lucide-bar-chart-3 class="w-4 h-4 text-emerald-700" />
                    <span>Komparasi Arus Kas Masuk vs Keluar (6 Bulan Terakhir)</span>
                </h3>
                <p class="text-[11px] text-stone-500 font-medium mt-0.5">Monitoring perbandingan likuiditas dan surplus kas per bulan.</p>
            </div>
            <!-- Chart Legend -->
            <div class="flex items-center gap-3 text-[11px] font-bold">
                <span class="flex items-center gap-1.5 text-stone-700">
                    <span class="w-2.5 h-2.5 rounded-sm bg-emerald-500 inline-block"></span> Kas Masuk
                </span>
                <span class="flex items-center gap-1.5 text-stone-700">
                    <span class="w-2.5 h-2.5 rounded-sm bg-rose-500 inline-block"></span> Kas Keluar
                </span>
            </div>
        </div>

        <!-- Dual Bar Chart Display -->
        <div class="pt-4 pb-2">
            <div class="grid grid-cols-6 gap-2 sm:gap-4 items-end h-56 border-b border-stone-200 px-2">
                @foreach ($monthlyChartData as $mData)
                    <div class="flex flex-col items-center h-full justify-end group relative">
                        <!-- Tooltip on hover -->
                        <div class="opacity-0 group-hover:opacity-100 pointer-events-none absolute -top-16 bg-stone-900 text-white text-[10px] font-bold py-2 px-3 rounded-xl shadow-xl transition duration-150 z-20 whitespace-nowrap text-left space-y-0.5">
                            <div class="font-extrabold text-stone-300 border-b border-stone-700 pb-1 text-center">{{ $mData['label'] }}</div>
                            <div class="text-emerald-400">Masuk: Rp {{ number_format($mData['inflow'], 0, ',', '.') }}</div>
                            <div class="text-rose-400">Keluar: Rp {{ number_format($mData['outflow'], 0, ',', '.') }}</div>
                            <div class="{{ $mData['net'] >= 0 ? 'text-emerald-300' : 'text-rose-300' }} font-black pt-0.5 border-t border-stone-800">
                                Net: {{ $mData['net'] >= 0 ? '+' : '-' }}Rp {{ number_format(abs($mData['net']), 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Dual Bar Columns Container -->
                        <div class="w-full flex items-end justify-center gap-1 sm:gap-1.5 h-full">
                            <!-- Inflow Bar (Green) -->
                            <div class="w-1/2 max-w-[20px] bg-emerald-500 rounded-t-lg transition-all duration-300 group-hover:opacity-90 shadow-2xs" style="height: {{ max(6, $mData['inflow_pct']) }}%;" title="Masuk: Rp {{ number_format($mData['inflow'], 0, ',', '.') }}"></div>
                            <!-- Outflow Bar (Red) -->
                            <div class="w-1/2 max-w-[20px] bg-rose-500 rounded-t-lg transition-all duration-300 group-hover:opacity-90 shadow-2xs" style="height: {{ max(6, $mData['outflow_pct']) }}%;" title="Keluar: Rp {{ number_format($mData['outflow'], 0, ',', '.') }}"></div>
                        </div>

                        <!-- Bar Month Label -->
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-tight truncate w-full text-center mt-2">
                            {{ $mData['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Footer summary badge -->
        <div class="pt-2 flex items-center justify-between text-xs text-stone-500 font-medium">
            <span>* Angka terakumulasi real-time dari buku kas sekolah</span>
            <span class="font-bold text-stone-700">Skala Tertinggi: Rp {{ number_format($maxMonthVal, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- 2. Breakdown Alokasi Stream Likuiditas (1 Col) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2 border-b border-stone-100 pb-3">
            <x-lucide-pie-chart class="w-4 h-4 text-emerald-700" />
            <span>Rincian Sumber & Beban</span>
        </h3>

        <!-- Kas Masuk Breakdown -->
        <div class="space-y-2">
            <h4 class="text-[11px] font-extrabold text-emerald-700 uppercase tracking-wider">Sumber Penerimaan (Kas Masuk):</h4>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between items-center bg-emerald-50/50 p-2 rounded-xl border border-emerald-100">
                    <span class="font-bold text-stone-700">SPP & Tagihan Siswa</span>
                    <span class="font-black text-emerald-800">Rp {{ number_format($totalTagihanSpp, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center bg-emerald-50/50 p-2 rounded-xl border border-emerald-100">
                    <span class="font-bold text-stone-700">Kas Infaq & Donasi</span>
                    <span class="font-black text-emerald-800">Rp {{ number_format($totalKasYayasan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center bg-emerald-50/50 p-2 rounded-xl border border-emerald-100">
                    <span class="font-bold text-stone-700">Setoran Tabungan</span>
                    <span class="font-black text-emerald-800">Rp {{ number_format($totalTabunganSetor, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Kas Keluar Breakdown -->
        <div class="space-y-2 pt-2 border-t border-stone-100">
            <h4 class="text-[11px] font-extrabold text-rose-700 uppercase tracking-wider">Pos Pengeluaran (Kas Keluar):</h4>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between items-center bg-rose-50/50 p-2 rounded-xl border border-rose-100">
                    <span class="font-bold text-stone-700">Operasional Yayasan</span>
                    <span class="font-black text-rose-700">Rp {{ number_format($totalOperasional, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center bg-rose-50/50 p-2 rounded-xl border border-rose-100">
                    <span class="font-bold text-stone-700">Gaji & Honor Guru</span>
                    <span class="font-black text-rose-700">Rp {{ number_format($totalGaji, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center bg-rose-50/50 p-2 rounded-xl border border-rose-100">
                    <span class="font-bold text-stone-700">Pencairan Kasbon</span>
                    <span class="font-black text-rose-700">Rp {{ number_format($totalKasbon, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
