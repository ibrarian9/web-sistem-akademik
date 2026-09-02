<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Arus Kas (Cash Flow)" 
        subtitle="Buku kas & jurnal terpadu arus masuk (SPP, Infaq, Tabungan) serta arus keluar (Operasional, Gaji, Kasbon)."
        badge="BUKU KAS UTAMA"
        badgeVariant="emerald"
        icon="layers"
    >
        <x-slot:actions>
            <x-button variant="primary" size="sm" icon="plus" wire:click="openIncomeModal">
                Catat Kas Masuk
            </x-button>
            <x-button variant="danger-solid" size="sm" icon="minus" wire:click="openExpenseModal">
                Catat Kas Keluar
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Manajemen Arus Kas (Cash Flow)"
        :steps="[
            ['title' => 'Grafik Komparasi Arus Kas', 'desc' => 'Tinjau perbandingan kas masuk (hijau) vs kas keluar (merah) 6 bulan terakhir untuk memantau surplus/defisit likuiditas.'],
            ['title' => 'Tab Filter Cepat', 'desc' => 'Gunakan tab Semua Arus Kas, Kas Masuk Saja, atau Kas Keluar Saja untuk memfilter jurnal pembukuan.'],
            ['title' => 'Pencatatan Cepat', 'desc' => 'Gunakan tombol di atas untuk mendokumentasikan penerimaan infaq/donasi yayasan atau pengeluaran operasional sekolah.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- 3 Core Metric Cards (Cash Inflow, Cash Outflow, Net Balance) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card 
            title="Total Kas Masuk" 
            :value="'Rp ' . number_format($totalInflow, 0, ',', '.')" 
            subtitle="SPP, infaq yayasan, & setoran tabungan"
            icon="trending-up" 
            variant="emerald" 
        />
        <x-stat-card 
            title="Total Kas Keluar" 
            :value="'Rp ' . number_format($totalOutflow, 0, ',', '.')" 
            subtitle="Beban operasional, gaji guru, & kasbon"
            icon="trending-down" 
            variant="rose" 
        />
        <x-stat-card 
            title="Surplus / Saldo Kas Bersih" 
            :value="($netCashFlow < 0 ? '- Rp ' : 'Rp ') . number_format(abs($netCashFlow), 0, ',', '.')" 
            :subtitle="$netCashFlow >= 0 ? 'Surplus kas periode ini' : 'Defisit kas periode ini'"
            icon="wallet" 
            :variant="$netCashFlow >= 0 ? 'white' : 'rose'" 
        />
    </div>

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

    <!-- MAIN JURNAL BUKU KAS TABLE PANEL -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
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

        <!-- Date Range Filter Row -->
        <div class="flex items-center justify-between gap-4 border-t border-stone-100 pt-3 flex-wrap">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Periode:</span>
                <x-date-filter model="filterPeriode" startDateModel="startDate" endDateModel="endDate" />
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <div class="text-xs font-bold text-stone-600">
                    Menampilkan <span class="text-stone-900 font-extrabold">{{ $paginatedTransactions->total() }}</span> transaksi kas
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

        <!-- Unified Cash Flow Jurnal Table -->
        <x-table loadingTarget="search, tab, stream, filterPeriode, startDate, endDate, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-32">Tanggal</x-table.th>
                    <x-table.th align="center" class="w-24">Tipe</x-table.th>
                    <x-table.th class="w-44">Stream / Sumber</x-table.th>
                    <x-table.th class="w-40">Kategori / Pos</x-table.th>
                    <x-table.th align="right" class="w-36">Kas Masuk (Rp)</x-table.th>
                    <x-table.th align="right" class="w-36">Kas Keluar (Rp)</x-table.th>
                    <x-table.th class="min-w-[180px]">Keterangan / Rincian</x-table.th>
                    <x-table.th align="center" class="w-32">Metode / Resi</x-table.th>
                    <x-table.th align="center" class="w-20">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($paginatedTransactions as $item)
                    <tr class="hover:bg-stone-50/80 transition">
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-bold text-xs text-stone-900">{{ $item->tanggal->translatedFormat('d M Y') }}</div>
                            @if($item->tanggal->format('H:i') !== '00:00')
                                <div class="text-[10px] text-stone-400 font-mono">{{ $item->tanggal->format('H:i') }} WIB</div>
                            @endif
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($item->type === 'masuk')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <x-lucide-arrow-down-left class="w-3 h-3 text-emerald-600" /> Masuk
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200">
                                    <x-lucide-arrow-up-right class="w-3 h-3 text-rose-600" /> Keluar
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <x-badge :variant="$item->stream_badge" size="xs">
                                {{ $item->stream_label }}
                            </x-badge>
                        </td>
                        <td class="p-3.5 border-r border-stone-200 font-bold text-stone-800 text-xs">
                            {{ $item->kategori }}
                        </td>
                        <td class="p-3.5 text-right font-black text-emerald-700 text-xs border-r border-stone-200">
                            {{ $item->nominal_masuk > 0 ? ('Rp ' . number_format($item->nominal_masuk, 0, ',', '.')) : '-' }}
                        </td>
                        <td class="p-3.5 text-right font-black text-rose-700 text-xs border-r border-stone-200">
                            {{ $item->nominal_keluar > 0 ? ('Rp ' . number_format($item->nominal_keluar, 0, ',', '.')) : '-' }}
                        </td>
                        <td class="p-3.5 text-xs text-stone-700 font-medium border-r border-stone-200">
                            {{ $item->keterangan }}
                        </td>
                        <td class="p-3.5 text-center text-xs text-stone-600 border-r border-stone-200 font-medium">
                            <span class="font-bold block">{{ $item->metode_resi }}</span>
                            @if ($item->no_resi)
                                <span class="text-[10px] font-mono text-stone-400 block">{{ $item->no_resi }}</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            @if ($item->can_delete)
                                @if ($item->type === 'masuk')
                                    <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteIncome({{ $item->raw_id }})" data-confirm="Hapus catatan penerimaan kas ini?" title="Hapus Kas Masuk">
                                        Hapus
                                    </x-button>
                                @else
                                    <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteExpense({{ $item->raw_id }})" data-confirm="Hapus catatan pengeluaran kas ini?" title="Hapus Kas Keluar">
                                        Hapus
                                    </x-button>
                                @endif
                            @elseif ($item->stream === 'spp' && $item->raw_id)
                                <a href="{{ route('finance.cetak-resi', $item->raw_id) }}" target="_blank" class="p-1.5 bg-stone-100 hover:bg-emerald-100 text-stone-700 hover:text-emerald-900 rounded-lg inline-flex items-center justify-center border border-stone-300 transition" title="Cetak Resi">
                                    <x-lucide-printer class="w-3.5 h-3.5" />
                                </a>
                            @else
                                <span class="text-[10px] text-stone-400 font-mono italic">Sistem</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="9" title="Belum ada catatan arus kas" message="Tidak ada transaksi pembukuan kas yang sesuai dengan filter yang dipilih." />
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $paginatedTransactions->links() }}
        </div>
    </div>

    <!-- MODAL 1: Catat Kas Masuk Yayasan -->
    <x-floating-card 
        :show="$showIncomeModal" 
        title="Catat Kas Masuk Yayasan" 
        subtitle="Dokumentasikan penerimaan non-SPP (Infaq, Sedekah Subuh, Maghrib Mengaji, Donatur, Hibah)."
        badge="KAS MASUK YAYASAN"
        badgeVariant="emerald"
        icon="arrow-down-left"
        maxWidth="max-w-lg"
        closeAction="closeIncomeModal"
    >
        <form wire:submit.prevent="saveIncome" class="space-y-4">
            <div>
                <label for="income_tgl" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Tanggal Penerimaan</label>
                <input type="date" id="income_tgl" wire:model="tanggal_masuk" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
                @error('tanggal_masuk') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="income_kat" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Kategori Penerimaan</label>
                <select id="income_kat" wire:model="kategori_masuk" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    @foreach ($kategoriMasukOptions as $kat)
                        <option value="{{ $kat }}">{{ $kat }}</option>
                    @endforeach
                </select>
                @error('kategori_masuk') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="income_nom" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Nominal Penerimaan (Rp)</label>
                <input type="number" id="income_nom" wire:model="jumlah_masuk" min="1000" step="1000" placeholder="Contoh: 500000" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
                @error('jumlah_masuk') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="income_ket" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Keterangan / Nama Donatur</label>
                <textarea id="income_ket" wire:model="keterangan_masuk" rows="3" placeholder="Tulis nama donatur, acara, atau keterangan infaq..." class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs"></textarea>
                @error('keterangan_masuk') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                <x-button type="button" variant="secondary" size="sm" wire:click="closeIncomeModal">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="sm" icon="check">
                    Simpan Kas Masuk
                </x-button>
            </div>
        </form>
    </x-floating-card>

    <!-- MODAL 2: Catat Kas Keluar Operasional -->
    <x-floating-card 
        :show="$showExpenseModal" 
        title="Catat Kas Keluar Operasional" 
        subtitle="Dokumentasikan beban pengeluaran operasional (ATK, Sarpras, Listrik/Air, Konsumsi, Acara)."
        badge="KAS KELUAR YAYASAN"
        badgeVariant="rose"
        icon="arrow-up-right"
        maxWidth="max-w-lg"
        closeAction="closeExpenseModal"
    >
        <form wire:submit.prevent="saveExpense" class="space-y-4">
            <div>
                <label for="expense_tgl" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Tanggal Transaksi</label>
                <input type="date" id="expense_tgl" wire:model="tanggal_keluar" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
                @error('tanggal_keluar') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="expense_kat" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Kategori Pengeluaran</label>
                <select id="expense_kat" wire:model="kategori_pengeluaran_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    @foreach ($kategoriKeluarOptions as $c)
                        <option value="{{ $c['id'] }}">{{ $c['nama'] }}</option>
                    @endforeach
                </select>
                @error('kategori_pengeluaran_id') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="expense_nom" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Nominal Pengeluaran (Rp)</label>
                <input type="number" id="expense_nom" wire:model="jumlah_keluar" min="1000" step="1000" placeholder="Contoh: 150000" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
                @error('jumlah_keluar') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="expense_ket" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Keterangan / Uraian Belanja</label>
                <textarea id="expense_ket" wire:model="keterangan_keluar" rows="3" placeholder="Tulis rincian pembelian ATK, perbaikan sarpras, konsumsi..." class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs"></textarea>
                @error('keterangan_keluar') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                <x-button type="button" variant="secondary" size="sm" wire:click="closeExpenseModal">
                    Batal
                </x-button>
                <x-button type="submit" variant="danger-solid" size="sm" icon="check">
                    Simpan Kas Keluar
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
