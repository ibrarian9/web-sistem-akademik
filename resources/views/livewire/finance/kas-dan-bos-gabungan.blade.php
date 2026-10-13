<div class="space-y-6 font-sans pb-16" 
     x-data="{ previewOpen: false, previewSrc: '', previewTitle: '' }" 
     x-init="$watch('$wire.showPreviewBuktiModal', val => { if(val) { previewSrc = $wire.previewBuktiUrl; previewTitle = $wire.previewBuktiTitle; previewOpen = true; } })" 
     @keydown.escape.window="previewOpen = false">

    <!-- Header Title Bar -->
    <x-page-header 
        title="Arus Kas & Dana BOS" 
        subtitle="Buku kas konsolidasi yang menggabungkan seluruh arus kas operasional sekolah dan realisasi Dana BOS dalam satu pembukuan."
        badge="KONSOLIDASI KEUANGAN"
        badgeVariant="emerald"
        icon="layers"
    >
        <x-slot:actions>
            <x-button variant="secondary" size="md" icon="file-text" wire:click="exportExcel">
                Ekspor Excel
            </x-button>
            <x-button variant="secondary" size="md" icon="printer" wire:click="exportPdf">
                Cetak PDF
            </x-button>
            <x-button variant="primary" size="md" icon="arrow-up-right" href="{{ route('finance.arus-kas') }}">
                Arus Kas Mandiri
            </x-button>
            <x-button variant="primary" size="md" icon="arrow-up-right" href="{{ route('finance.dana-bos') }}">
                Dana BOS Mandiri
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Penggunaan Buku Arus Kas & Dana BOS"
        :steps="[
            [
                'title' => '1. Tinjau Saldo Konsolidasi & Klik Kartu untuk Filter', 
                'desc' => 'Klik kartu Total Pemasukan untuk memfilter transaksi masuk, klik kartu Total Pengeluaran untuk memfilter transaksi keluar, atau klik kartu Saldo Bersih untuk melihat semua.'
            ],
            [
                'title' => '2. Filter Fleksibel Berdasarkan Sumber & Arah', 
                'desc' => 'Pilih Sumber Dana (Semua Sumber, Kas Sekolah, atau Dana BOS) dan Arah Arus (Pemasukan, Pengeluaran, atau Semua) untuk melihat pembukuan spesifik yang Anda butuhkan.'
            ],
            [
                'title' => '3. Pemeriksaan Bukti Fisik Transaksi', 
                'desc' => 'Klik tombol Lihat Bukti pada baris transaksi untuk memeriksa foto nota, struk kasir, atau kuitansi tanpa harus berpindah halaman.'
            ]
        ]"
        notes="Menu ini adalah laporan konsolidasi pembukuan. Pencatatan transaksi baru tetap dapat dilakukan melalui menu Arus Kas atau menu Dana BOS masing-masing."
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- 4 EXECUTIVE METRIC CARDS (INTERACTIVE & FILTERABLE) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Pemasukan Gabungan (Click to Filter Pemasukan) -->
        <x-stat-card 
            title="Total Pemasukan Gabungan" 
            :value="'Rp ' . number_format($metrics['total_masuk'], 0, ',', '.')" 
            :subtitle="'Kas Sekolah: Rp ' . number_format($metrics['masuk_kas'], 0, ',', '.') . ' | BOS: Rp ' . number_format($metrics['masuk_bos'], 0, ',', '.')"
            icon="trending-up" 
            variant="soft-emerald" 
            :badge="$filterJenis === 'masuk' ? 'Filter Aktif' : 'Total Inflow'"
            wire:click="filterByFlow('masuk')"
            role="button"
            tabindex="0"
            class="cursor-pointer select-none hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 {{ $filterJenis === 'masuk' ? 'ring-4 ring-emerald-500/80 ring-offset-2 shadow-md' : 'hover:shadow-xs' }}"
        />

        <!-- 2. Total Pengeluaran Gabungan (Click to Filter Pengeluaran) -->
        <x-stat-card 
            title="Total Pengeluaran Gabungan" 
            :value="'Rp ' . number_format($metrics['total_keluar'], 0, ',', '.')" 
            :subtitle="'Kas Sekolah: Rp ' . number_format($metrics['keluar_kas'], 0, ',', '.') . ' | BOS: Rp ' . number_format($metrics['keluar_bos'], 0, ',', '.')"
            icon="trending-down" 
            variant="soft-rose" 
            :badge="$filterJenis === 'keluar' ? 'Filter Aktif' : 'Total Outflow'"
            wire:click="filterByFlow('keluar')"
            role="button"
            tabindex="0"
            class="cursor-pointer select-none hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 {{ $filterJenis === 'keluar' ? 'ring-4 ring-rose-500/80 ring-offset-2 shadow-md' : 'hover:shadow-xs' }}"
        />

        <!-- 3. Surplus / Defisit Bersih (Click to Show All Flows) -->
        <x-stat-card 
            title="Saldo Bersih (Net Cash)" 
            :value="'Rp ' . number_format($metrics['net_balance'], 0, ',', '.')" 
            :subtitle="'Net Kas: Rp ' . number_format($metrics['net_kas'], 0, ',', '.') . ' | Net BOS: Rp ' . number_format($metrics['net_bos'], 0, ',', '.')"
            icon="wallet" 
            :variant="$metrics['net_balance'] >= 0 ? 'soft-emerald' : 'soft-rose'"
            :badge="$metrics['net_balance'] >= 0 ? 'Surplus' : 'Defisit'"
            wire:click="setJenis('semua')"
            role="button"
            tabindex="0"
            class="cursor-pointer select-none hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 {{ $filterJenis === 'semua' ? 'ring-2 ring-stone-300' : 'hover:shadow-xs' }}"
        />

        <!-- 4. Total Transaksi Terdata (Click to Reset Filter) -->
        <x-stat-card 
            title="Total Transaksi Terdata" 
            :value="number_format($metrics['count_total'], 0, ',', '.') . ' Transaksi'" 
            :subtitle="$metrics['count_masuk'] . ' Pemasukan | ' . $metrics['count_keluar'] . ' Pengeluaran'"
            icon="layers" 
            variant="soft-indigo" 
            :badge="$filterSumber === 'semua' ? 'Semua Sumber' : ($filterSumber === 'kas_umum' ? 'Kas Sekolah' : 'Dana BOS')"
            wire:click="resetFilters"
            role="button"
            tabindex="0"
            class="cursor-pointer select-none hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 hover:shadow-xs"
        />
    </div>

    <!-- VISUAL ANALYTICS: DUAL-BAR MONTHLY TREND & SOURCE COMPARISON -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 1. Dual Bar Monthly Trend (2 Cols) -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-stone-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                        <x-lucide-bar-chart-3 class="w-4 h-4 text-emerald-700" />
                        <span>Tren Arus Kas Gabungan (6 Bulan Terakhir)</span>
                    </h3>
                    <p class="text-[11px] text-stone-500 font-medium mt-0.5">Monitoring perbandingan akumulasi pemasukan vs pengeluaran (Kas Sekolah + BOS).</p>
                </div>
                <!-- Chart Legend -->
                <div class="flex items-center gap-3 text-[11px] font-bold">
                    <span class="flex items-center gap-1.5 text-stone-700">
                        <span class="w-2.5 h-2.5 rounded-sm bg-emerald-500 inline-block"></span> Pemasukan
                    </span>
                    <span class="flex items-center gap-1.5 text-stone-700">
                        <span class="w-2.5 h-2.5 rounded-sm bg-rose-500 inline-block"></span> Pengeluaran
                    </span>
                </div>
            </div>

            <!-- Dual Bar Chart Display -->
            <div class="pt-4 pb-2">
                <div class="grid grid-cols-6 gap-2 sm:gap-4 items-end h-56 border-b border-stone-200 px-2">
                    @foreach ($monthlyChartData as $mData)
                        <div class="flex flex-col items-center h-full justify-end group relative">
                            <!-- Tooltip on hover -->
                            <div class="opacity-0 group-hover:opacity-100 pointer-events-none absolute -top-24 bg-stone-900 text-white text-[10px] font-bold py-2.5 px-3.5 rounded-xl shadow-xl transition duration-150 z-20 whitespace-nowrap text-left space-y-1">
                                <div class="font-extrabold text-stone-300 border-b border-stone-700 pb-1 text-center">{{ $mData['label'] }}</div>
                                <div class="text-emerald-400">Total Masuk: Rp {{ number_format($mData['inflow'], 0, ',', '.') }}</div>
                                <div class="text-[9px] text-stone-400 pl-2">Kas: Rp {{ number_format($mData['kas_inflow'], 0, ',', '.') }} | BOS: Rp {{ number_format($mData['bos_inflow'], 0, ',', '.') }}</div>
                                <div class="text-rose-400">Total Keluar: Rp {{ number_format($mData['outflow'], 0, ',', '.') }}</div>
                                <div class="text-[9px] text-stone-400 pl-2">Kas: Rp {{ number_format($mData['kas_outflow'], 0, ',', '.') }} | BOS: Rp {{ number_format($mData['bos_outflow'], 0, ',', '.') }}</div>
                                <div class="{{ $mData['net'] >= 0 ? 'text-emerald-300' : 'text-rose-300' }} font-black pt-0.5 border-t border-stone-800">
                                    Net: {{ $mData['net'] >= 0 ? '+' : '-' }}Rp {{ number_format(abs($mData['net']), 0, ',', '.') }}
                                </div>
                            </div>

                            <!-- Dual Bar Columns Container -->
                            <div class="w-full flex items-end justify-center gap-1 sm:gap-1.5 h-full">
                                <!-- Inflow Bar (Green) -->
                                <div class="w-1/2 max-w-[22px] bg-emerald-500 rounded-t-lg transition-all duration-300 group-hover:opacity-90 shadow-2xs" 
                                     style="height: {{ max(6, $mData['inflow_pct']) }}%;" 
                                     title="Pemasukan: Rp {{ number_format($mData['inflow'], 0, ',', '.') }}"></div>
                                <!-- Outflow Bar (Red) -->
                                <div class="w-1/2 max-w-[22px] bg-rose-500 rounded-t-lg transition-all duration-300 group-hover:opacity-90 shadow-2xs" 
                                     style="height: {{ max(6, $mData['outflow_pct']) }}%;" 
                                     title="Pengeluaran: Rp {{ number_format($mData['outflow'], 0, ',', '.') }}"></div>
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
                <span>* Akumulasi real-time gabungan buku kas sekolah dan dana BOS</span>
                <span class="font-bold text-stone-700">Skala Tertinggi: Rp {{ number_format($maxMonthVal, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- 2. Komposisi Sumber Dana: Kas Sekolah vs Dana BOS (1 Col) -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2 border-b border-stone-100 pb-3">
                    <x-lucide-pie-chart class="w-4 h-4 text-indigo-700" />
                    <span>Komparasi Sumber Dana</span>
                </h3>
                <p class="text-[11px] text-stone-500 font-medium mt-1">Perbandingan porsi Kas Sekolah dan Dana BOS pada periode aktif.</p>
            </div>

            @php
                $totIn = max(1, $metrics['total_masuk']);
                $totOut = max(1, $metrics['total_keluar']);
                $kasInPct = round(($metrics['masuk_kas'] / $totIn) * 100);
                $bosInPct = round(($metrics['masuk_bos'] / $totIn) * 100);
                $kasOutPct = round(($metrics['keluar_kas'] / $totOut) * 100);
                $bosOutPct = round(($metrics['keluar_bos'] / $totOut) * 100);
            @endphp

            <!-- Comparison Inflow Section -->
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-extrabold text-stone-800 uppercase tracking-wider text-[11px]">Porsi Pemasukan</span>
                    <span class="text-[11px] font-bold text-stone-500">Total: Rp {{ number_format($metrics['total_masuk'], 0, ',', '.') }}</span>
                </div>
                
                <!-- Progress bar comparison -->
                <div class="h-3 w-full bg-stone-100 rounded-full overflow-hidden flex shadow-inner">
                    <div class="bg-indigo-600 h-full transition-all duration-300" style="width: {{ $kasInPct }}%;" title="Kas Sekolah: {{ $kasInPct }}%"></div>
                    <div class="bg-amber-500 h-full transition-all duration-300" style="width: {{ $bosInPct }}%;" title="Dana BOS: {{ $bosInPct }}%"></div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                    <div class="p-2.5 bg-indigo-50/70 border border-indigo-100 rounded-xl">
                        <div class="flex items-center gap-1.5 text-indigo-900 font-extrabold text-[11px]">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                            <span>Kas Sekolah ({{ $kasInPct }}%)</span>
                        </div>
                        <div class="text-xs font-black text-indigo-950 mt-1">
                            Rp {{ number_format($metrics['masuk_kas'], 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="p-2.5 bg-amber-50/70 border border-amber-100 rounded-xl">
                        <div class="flex items-center gap-1.5 text-amber-900 font-extrabold text-[11px]">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>Dana BOS ({{ $bosInPct }}%)</span>
                        </div>
                        <div class="text-xs font-black text-amber-950 mt-1">
                            Rp {{ number_format($metrics['masuk_bos'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparison Outflow Section -->
            <div class="space-y-2 pt-3 border-t border-stone-100">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-extrabold text-stone-800 uppercase tracking-wider text-[11px]">Porsi Pengeluaran</span>
                    <span class="text-[11px] font-bold text-stone-500">Total: Rp {{ number_format($metrics['total_keluar'], 0, ',', '.') }}</span>
                </div>

                <!-- Progress bar comparison -->
                <div class="h-3 w-full bg-stone-100 rounded-full overflow-hidden flex shadow-inner">
                    <div class="bg-indigo-600 h-full transition-all duration-300" style="width: {{ $kasOutPct }}%;" title="Kas Sekolah: {{ $kasOutPct }}%"></div>
                    <div class="bg-amber-500 h-full transition-all duration-300" style="width: {{ $bosOutPct }}%;" title="Dana BOS: {{ $bosOutPct }}%"></div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                    <div class="p-2.5 bg-indigo-50/70 border border-indigo-100 rounded-xl">
                        <div class="flex items-center gap-1.5 text-indigo-900 font-extrabold text-[11px]">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                            <span>Kas Sekolah ({{ $kasOutPct }}%)</span>
                        </div>
                        <div class="text-xs font-black text-indigo-950 mt-1">
                            Rp {{ number_format($metrics['keluar_kas'], 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="p-2.5 bg-amber-50/70 border border-amber-100 rounded-xl">
                        <div class="flex items-center gap-1.5 text-amber-900 font-extrabold text-[11px]">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>Dana BOS ({{ $bosOutPct }}%)</span>
                        </div>
                        <div class="text-xs font-black text-amber-950 mt-1">
                            Rp {{ number_format($metrics['keluar_bos'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Comparison Summary -->
            <div class="pt-3 border-t border-stone-100 flex items-center justify-between text-[11px] font-bold text-stone-600">
                <span>Net Kas: <strong class="{{ $metrics['net_kas'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">Rp {{ number_format($metrics['net_kas'], 0, ',', '.') }}</strong></span>
                <span>Net BOS: <strong class="{{ $metrics['net_bos'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">Rp {{ number_format($metrics['net_bos'], 0, ',', '.') }}</strong></span>
            </div>
        </div>
    </div>

    <!-- MAIN PANEL: FILTER & UNIFIED LEDGER TABLE -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 sm:p-6 shadow-xs space-y-5">
        
        <!-- Controls Bar: Sumber Dana & Arah Arus Tabs -->
        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 pb-4 border-b border-stone-200">
            <!-- Sumber Dana Filter Tabs -->
            <div class="flex items-center gap-1.5 p-1 bg-stone-100/90 border border-stone-200/90 rounded-xl overflow-x-auto">
                <button type="button" 
                        wire:click="setSumber('semua')" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition select-none cursor-pointer whitespace-nowrap {{ $filterSumber === 'semua' ? 'bg-white text-emerald-900 shadow-2xs border border-stone-200 font-extrabold' : 'text-stone-600 hover:text-stone-900 hover:bg-white/60' }}">
                    Semua Sumber ({{ $metrics['count_total'] }})
                </button>
                <button type="button" 
                        wire:click="setSumber('kas_umum')" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition select-none cursor-pointer whitespace-nowrap flex items-center gap-1.5 {{ $filterSumber === 'kas_umum' ? 'bg-white text-indigo-900 shadow-2xs border border-stone-200 font-extrabold' : 'text-stone-600 hover:text-stone-900 hover:bg-white/60' }}">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span>Kas Operasional ({{ $metrics['count_kas'] }})</span>
                </button>
                <button type="button" 
                        wire:click="setSumber('dana_bos')" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition select-none cursor-pointer whitespace-nowrap flex items-center gap-1.5 {{ $filterSumber === 'dana_bos' ? 'bg-white text-amber-900 shadow-2xs border border-stone-200 font-extrabold' : 'text-stone-600 hover:text-stone-900 hover:bg-white/60' }}">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Dana BOS ({{ $metrics['count_bos'] }})</span>
                </button>
            </div>

            <!-- Arah Arus Filter Tabs -->
            <div class="flex items-center gap-1.5 p-1 bg-stone-100/90 border border-stone-200/90 rounded-xl overflow-x-auto">
                <button type="button" 
                        wire:click="setJenis('semua')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition select-none cursor-pointer whitespace-nowrap {{ $filterJenis === 'semua' ? 'bg-white text-stone-900 shadow-2xs border border-stone-200 font-extrabold' : 'text-stone-600 hover:text-stone-900 hover:bg-white/60' }}">
                    Semua Arus
                </button>
                <button type="button" 
                        wire:click="setJenis('masuk')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition select-none cursor-pointer whitespace-nowrap flex items-center gap-1 {{ $filterJenis === 'masuk' ? 'bg-emerald-600 text-white shadow-2xs font-extrabold' : 'text-emerald-700 hover:bg-emerald-50' }}">
                    <x-lucide-arrow-down-left class="w-3.5 h-3.5" />
                    <span>Pemasukan Saja</span>
                </button>
                <button type="button" 
                        wire:click="setJenis('keluar')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition select-none cursor-pointer whitespace-nowrap flex items-center gap-1 {{ $filterJenis === 'keluar' ? 'bg-rose-600 text-white shadow-2xs font-extrabold' : 'text-rose-700 hover:bg-rose-50' }}">
                    <x-lucide-arrow-up-right class="w-3.5 h-3.5" />
                    <span>Pengeluaran Saja</span>
                </button>
            </div>
        </div>

        <!-- Filter Bar: Search, Category, Date Range -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <!-- Search Keyword -->
            <div class="{{ $filterPeriode === 'custom' ? 'lg:col-span-3' : ($this->activeFilterCount > 0 ? 'lg:col-span-4' : 'lg:col-span-4') }}">
                <label class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">Pencarian:</label>
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari keterangan, resi, petugas..." />
            </div>

            <!-- Kategori Dropdown -->
            <div class="{{ $filterPeriode === 'custom' ? 'lg:col-span-3' : ($this->activeFilterCount > 0 ? 'lg:col-span-4' : 'lg:col-span-4') }}">
                <label class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">Kategori Transaksi:</label>
                <select wire:model.live="filterKategori" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Kategori</option>
                    @foreach ($categoryGroups as $groupLabel => $cats)
                        @if (!empty($cats))
                            <optgroup label="{{ $groupLabel }}">
                                @foreach ($cats as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Periode Waktu Preset -->
            <div class="{{ $filterPeriode === 'custom' ? 'lg:col-span-2' : ($this->activeFilterCount > 0 ? 'lg:col-span-3' : 'lg:col-span-4') }}">
                <label class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">Periode Waktu:</label>
                <select wire:model.live="filterPeriode" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="semua">Semua Periode</option>
                    <option value="hari_ini">Hari Ini</option>
                    <option value="kemarin">Kemarin</option>
                    <option value="minggu_ini">Minggu Ini</option>
                    <option value="bulan_ini">Bulan Ini</option>
                    <option value="bulan_lalu">Bulan Lalu</option>
                    <option value="tahun_ini">Tahun Ini</option>
                    <option value="custom">Rentang Tanggal Kustom</option>
                </select>
            </div>

            <!-- Custom Date Range -->
            @if ($filterPeriode === 'custom')
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">Dari:</label>
                    <input type="date" wire:model.live="startDate" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">Sampai:</label>
                    <input type="date" wire:model.live="endDate" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                </div>
            @endif

            <!-- Reset Button -->
            @if ($this->activeFilterCount > 0)
                <div class="{{ $filterPeriode === 'custom' ? 'lg:col-span-12 flex justify-end' : 'lg:col-span-1 flex items-end' }}">
                    <x-button variant="secondary" size="md" icon="refresh-cw" wire:click="resetFilters" class="w-full justify-center">
                        Reset
                    </x-button>
                </div>
            @endif
        </div>

        <!-- TABLE SECTION -->
        <x-table loadingTarget="filterSumber, filterJenis, filterKategori, filterPeriode, startDate, endDate, search, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-28">Tanggal</x-table.th>
                    <x-table.th class="w-36 whitespace-nowrap">Sumber Dana</x-table.th>
                    <x-table.th align="center" class="w-28">Jenis Arus</x-table.th>
                    <x-table.th class="min-w-[140px]">Kategori</x-table.th>
                    <x-table.th class="min-w-[220px]">Uraian & Keterangan Transaksi</x-table.th>
                    <x-table.th align="right" class="w-36 text-emerald-200">Pemasukan (Rp)</x-table.th>
                    <x-table.th align="right" class="w-36 text-rose-200">Pengeluaran (Rp)</x-table.th>
                    <x-table.th align="center" class="w-28">Petugas / Bukti</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($transactions as $t)
                    <tr class="hover:bg-emerald-50/40 transition">
                        <!-- Tanggal -->
                        <td class="p-3.5 border-r border-stone-200">
                            <span class="font-extrabold text-stone-900 text-xs block">
                                {{ $t->tanggal ? $t->tanggal->translatedFormat('d/m/Y') : '-' }}
                            </span>
                            @if ($t->no_resi)
                                <span class="text-[10px] text-stone-500 font-mono block mt-0.5">
                                    {{ $t->no_resi }}
                                </span>
                            @endif
                        </td>

                        <!-- Sumber Dana Badge (Muat Satu Baris) -->
                        <td class="p-3.5 border-r border-stone-200 whitespace-nowrap">
                            @if ($t->sumber === 'dana_bos')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-amber-50 text-amber-900 border border-amber-300 whitespace-nowrap">
                                    <x-lucide-box class="w-3.5 h-3.5 text-amber-600 shrink-0" />
                                    <span>Dana BOS</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-indigo-50 text-indigo-900 border border-indigo-200 whitespace-nowrap">
                                    <x-lucide-landmark class="w-3.5 h-3.5 text-indigo-600 shrink-0" />
                                    <span>Kas Sekolah</span>
                                </span>
                            @endif
                        </td>

                        <!-- Jenis Arus Badge -->
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($t->type === 'masuk')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <x-lucide-arrow-down-left class="w-3 h-3 text-emerald-700" />
                                    <span>Masuk</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                    <x-lucide-arrow-up-right class="w-3 h-3 text-rose-700" />
                                    <span>Keluar</span>
                                </span>
                            @endif
                        </td>

                        <!-- Kategori -->
                        <td class="p-3.5 border-r border-stone-200">
                            <span class="text-xs font-extrabold text-stone-900 block truncate" title="{{ $t->kategori }}">
                                {{ $t->kategori }}
                            </span>
                        </td>

                        <!-- Uraian & Keterangan Transaksi -->
                        <td class="p-3.5 border-r border-stone-200">
                            <span class="text-xs text-stone-800 font-medium block leading-snug">
                                {{ $t->keterangan }}
                            </span>
                        </td>

                        <!-- Nominal Pemasukan -->
                        <td class="p-3.5 text-right font-black text-xs border-r border-stone-200 {{ $t->nominal_masuk > 0 ? 'text-emerald-700 bg-emerald-50/20' : 'text-stone-300' }}">
                            @if ($t->nominal_masuk > 0)
                                +Rp {{ number_format($t->nominal_masuk, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>

                        <!-- Nominal Pengeluaran -->
                        <td class="p-3.5 text-right font-black text-xs border-r border-stone-200 {{ $t->nominal_keluar > 0 ? 'text-rose-700 bg-rose-50/20' : 'text-stone-300' }}">
                            @if ($t->nominal_keluar > 0)
                                -Rp {{ number_format($t->nominal_keluar, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>

                        <!-- Petugas / Bukti Transaksi -->
                        <td class="p-3.5 text-center">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <span class="text-[10px] text-stone-500 font-semibold truncate max-w-[100px]" title="{{ $t->petugas }}">
                                    {{ $t->petugas ?: '-' }}
                                </span>
                                @if ($t->bukti)
                                    <button type="button" 
                                            wire:click="openPreviewBukti('{{ Storage::url($t->bukti) }}', 'Bukti Transaksi: {{ addslashes($t->kategori) }}')" 
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-300 rounded text-[10px] font-bold transition cursor-pointer">
                                        <x-lucide-image class="w-3 h-3 text-emerald-600" />
                                        <span>Bukti</span>
                                    </button>
                                @elseif (in_array(auth()->user()->role?->nama, ['finance', 'super_admin', 'founder']) && !auth()->user()->isSuperAdmin2() && in_array(substr($t->id, 0, strpos($t->id, '_')), ['spp', 'infaq', 'op', 'bos', 'gaji']))
                                    <button type="button" 
                                            wire:click="openUploadBuktiModal('{{ $t->id }}', '{{ addslashes($t->kategori) }}', '{{ addslashes($t->keterangan) }}', {{ $t->nominal_masuk > 0 ? $t->nominal_masuk : $t->nominal_keluar }})" 
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 hover:text-emerald-900 border border-dashed border-emerald-300 rounded text-[10px] font-bold transition cursor-pointer shadow-2xs"
                                            title="Upload Foto Bukti">
                                        <x-lucide-camera class="w-3 h-3 text-emerald-600" />
                                        <span>+ Bukti</span>
                                    </button>
                                @else
                                    <span class="text-[10px] text-stone-400 font-mono italic">-</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="8" title="Tidak ada transaksi ditemukan" message="Tidak ada catatan kas umum atau Dana BOS yang cocok dengan kriteria filter." />
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- CLIENT-SIDE LIGHTBOX MODAL (ALPINE.JS) -->
    <div x-show="previewOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/80 backdrop-blur-xs flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="relative bg-white rounded-2xl max-w-2xl w-full p-4 sm:p-6 shadow-2xl space-y-4 border border-stone-200"
             @click.away="previewOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                <div class="flex items-center gap-2">
                    <x-lucide-file-text class="w-5 h-5 text-emerald-600" />
                    <h3 class="text-sm font-black text-stone-900" x-text="previewTitle"></h3>
                </div>
                <button type="button" @click="previewOpen = false" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition cursor-pointer">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            <div class="flex items-center justify-center bg-stone-50 rounded-xl p-2 border border-stone-200 max-h-[70vh] overflow-hidden">
                <img :src="previewSrc" alt="Bukti Transaksi" class="max-h-[65vh] object-contain rounded-lg shadow-xs">
            </div>

            <div class="flex items-center justify-between pt-2">
                <a :href="previewSrc" target="_blank" download class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-lg transition">
                    <x-lucide-download class="w-3.5 h-3.5" />
                    <span>Unduh Gambar</span>
                </a>
                <button type="button" @click="previewOpen = false" class="px-4 py-1.5 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold rounded-lg transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL UPLOAD BUKTI TRANSAKSI -->
    <x-floating-card 
        :show="$showUploadBuktiModal" 
        title="Upload Foto Bukti Transaksi" 
        subtitle="Unggah dokumen bukti transaksi (kuitansi, struk belanja, nota, atau bukti transfer)."
        icon="camera"
        maxWidth="max-w-md"
        closeAction="closeUploadBuktiModal"
    >
        <form wire:submit.prevent="saveUploadedBukti" class="space-y-4">
            <!-- Info Transaksi Ringkas -->
            <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-xl space-y-1.5 text-xs">
                <div class="flex items-center justify-between text-stone-500 font-medium">
                    <span>Kategori:</span>
                    <span class="font-bold text-stone-800">{{ $uploadTargetKategori }}</span>
                </div>
                <div class="flex items-center justify-between text-stone-500 font-medium">
                    <span>Nominal:</span>
                    <span class="font-extrabold text-emerald-700">Rp {{ number_format($uploadTargetNominal ?? 0, 0, ',', '.') }}</span>
                </div>
                @if($uploadTargetKeterangan)
                    <div class="pt-1.5 border-t border-stone-200/80 text-[11px] text-stone-600 line-clamp-2">
                        {{ $uploadTargetKeterangan }}
                    </div>
                @endif
            </div>

            <!-- File Upload Area -->
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1.5">
                    Pilih Foto Bukti
                </label>

                @if ($uploadBuktiFile)
                    <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ $uploadBuktiFile->temporaryUrl() }}" alt="Pratinjau" class="w-12 h-12 object-cover rounded-lg border border-emerald-300 shadow-2xs shrink-0" />
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-stone-900 truncate block">{{ $uploadBuktiFile->getClientOriginalName() }}</span>
                                <span class="text-[10px] text-stone-500 font-semibold">{{ number_format($uploadBuktiFile->getSize() / 1024, 1) }} KB</span>
                            </div>
                        </div>
                        <button type="button" wire:click="$set('uploadBuktiFile', null)" class="px-2 py-1 text-[11px] font-bold text-rose-700 bg-white border border-rose-200 rounded-lg hover:bg-rose-50 cursor-pointer shrink-0">
                            Batal
                        </button>
                    </div>
                @else
                    <label class="border-2 border-dashed border-stone-300 hover:border-emerald-500 rounded-xl p-5 flex flex-col items-center justify-center gap-2 cursor-pointer bg-stone-50/50 hover:bg-emerald-50/20 transition group">
                        <div class="w-10 h-10 rounded-full bg-stone-100 group-hover:bg-emerald-100 flex items-center justify-center text-stone-500 group-hover:text-emerald-700 transition">
                            <x-lucide-upload class="w-5 h-5" />
                        </div>
                        <div class="text-center">
                            <span class="text-xs font-bold text-stone-800 group-hover:text-emerald-800 block">Klik untuk memilih file</span>
                            <span class="text-[11px] text-stone-500 font-medium">Maks. 2MB (JPG, JPEG, PNG, atau WEBP)</span>
                        </div>
                        <input type="file" wire:model="uploadBuktiFile" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" />
                    </label>
                @endif

                <div wire:loading wire:target="uploadBuktiFile" class="text-[11px] text-emerald-700 font-bold mt-2 flex items-center gap-1.5">
                    <x-lucide-loader-2 class="w-3.5 h-3.5 animate-spin" />
                    <span>Sedang memuat foto...</span>
                </div>

                @error('uploadBuktiFile')
                    <span class="text-[11px] text-rose-600 font-bold mt-1.5 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-stone-200">
                <x-button type="button" variant="secondary" size="sm" wire:click="closeUploadBuktiModal">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="sm" icon="check" wire:loading.attr="disabled" wire:target="uploadBuktiFile, saveUploadedBukti">
                    <span wire:loading.remove wire:target="saveUploadedBukti">Simpan Bukti</span>
                    <span wire:loading wire:target="saveUploadedBukti">Menyimpan...</span>
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
