<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Gabungan Arus Kas Keluar" 
        subtitle="Pusat analitik &amp; rekapitulasi seluruh pengeluaran: Operasional Yayasan, Gaji Guru, serta Fasilitas Kasbon (Non-BOS)."
        badge="MONITORING &amp; KAS KELUAR"
        badgeVariant="rose"
        icon="trending-down"
    >
        <x-slot:actions>
            <x-button variant="outline" size="sm" icon="file-text" wire:click="exportPdf">
                Ekspor PDF
            </x-button>
            <x-button variant="danger-solid" size="sm" icon="plus" wire:click="openCreateModal">
                Catat Kas Keluar
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Monitoring & Gabungan Arus Kas Keluar"
        :steps="[
            ['title' => 'Grafik Tren Bulanan', 'desc' => 'Visualisasi tren arus kas keluar 6 bulan terakhir merangkum perbandingan beban operasional yayasan, payroll gaji, dan kasbon.'],
            ['title' => 'Filter Multi-Stream', 'desc' => 'Pilih tab stream (Operasional Yayasan, Gaji Guru, Kasbon) atau periode (Hari Ini, Kemarin, Minggu Ini, Bulan Ini) untuk audit spesifik.'],
            ['title' => 'Pencatatan Cepat', 'desc' => 'Klik Catat Kas Keluar untuk mendokumentasikan beban pengeluaran kas non-BOS seperti ATK, sarpras, listrik/air, atau konsumsi.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- 3-Stat Metric Cards Row (Non-BOS) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card 
            title="Total Seluruh Kas Keluar" 
            :value="'Rp ' . number_format($totalOutflowAll, 0, ',', '.')" 
            subtitle="Akumulasi operasional, gaji, & kasbon terpilih"
            icon="trending-down" 
            variant="rose" 
        />
        <x-stat-card 
            title="Beban Operasional Yayasan" 
            :value="'Rp ' . number_format($totalOperasional, 0, ',', '.')" 
            subtitle="ATK, sarpras, utilitas, konsumsi, dsb."
            icon="building" 
            variant="white" 
        />
        <x-stat-card 
            title="Realisasi Gaji & Honor Guru" 
            :value="'Rp ' . number_format($totalGaji, 0, ',', '.')" 
            subtitle="Gaji pokok & insentif terbayar"
            icon="wallet" 
            variant="white" 
        />
    </div>

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
                            <span>Gaji &amp; Honor Guru</span>
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

    <!-- MAIN UNIFIED DATA TABLE PANEL -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Stream Tabs & Search Row -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Stream Selector Tabs -->
            <div class="flex items-center p-1 bg-stone-100 border border-stone-200 rounded-xl overflow-x-auto shadow-2xs">
                <button type="button" 
                    wire:click="selectStream('semua')" 
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition shrink-0 {{ $stream === 'semua' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                    Semua Stream
                </button>
                <button type="button" 
                    wire:click="selectStream('operasional')" 
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-1.5 {{ $stream === 'operasional' ? 'bg-rose-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-300"></span>
                    <span>Operasional Yayasan</span>
                </button>
                <button type="button" 
                    wire:click="selectStream('gaji')" 
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-1.5 {{ $stream === 'gaji' ? 'bg-purple-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-300"></span>
                    <span>Gaji Guru</span>
                </button>
                <button type="button" 
                    wire:click="selectStream('peminjaman')" 
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-1.5 {{ $stream === 'peminjaman' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                    <span>Kasbon Guru</span>
                </button>
            </div>

            <!-- Search Bar & Category Filter -->
            <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap w-full lg:max-w-xl">
                <div class="w-full flex-1">
                    <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari transaksi, penerima, atau keterangan..." />
                </div>

                @if ($stream === 'semua' || $stream === 'operasional')
                    <select wire:model.live="filterKategori" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs shrink-0">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c['id'] }}">{{ $c['nama'] }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>

        <!-- Date Range Filter Row -->
        <div class="flex items-center justify-between gap-4 border-t border-stone-100 pt-3 flex-wrap">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Periode:</span>
                <x-date-filter model="filterPeriode" startDateModel="startDate" endDateModel="endDate" />
            </div>

            @if (count($selectedIds) > 0)
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-rose-700 bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-200">
                        {{ count($selectedIds) }} pengeluaran dipilih
                    </span>
                    <x-button variant="danger-solid" size="xs" icon="trash-2" wire:click="bulkDelete" data-confirm="Hapus seluruh pengeluaran yang dipilih?">
                        Hapus Terpilih
                    </x-button>
                </div>
            @endif
        </div>

        <!-- Unified Outflow Table -->
        <x-table loadingTarget="search, filterKategori, filterPeriode, startDate, endDate, stream, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    @if ($stream === 'operasional')
                        <th class="w-12 p-3.5 text-center border-r border-emerald-700/60">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                        </th>
                    @endif
                    <x-table.th class="w-36">Tanggal</x-table.th>
                    <x-table.th class="w-48">Stream / Sumber</x-table.th>
                    <x-table.th class="w-44">Kategori</x-table.th>
                    <x-table.th align="right" class="w-44">Nominal Beban</x-table.th>
                    <x-table.th class="min-w-[200px]">Keterangan / Penerima</x-table.th>
                    <x-table.th align="center" class="w-36">Petugas / PIC</x-table.th>
                    <x-table.th align="center" class="w-24">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($paginatedOutflows as $item)
                    <tr class="hover:bg-rose-50/30 transition {{ in_array($item->raw_id, $selectedIds) ? 'bg-rose-50/60 font-semibold' : '' }}">
                        @if ($stream === 'operasional')
                            <td class="p-3.5 text-center border-r border-stone-200">
                                <input type="checkbox" wire:model.live="selectedIds" value="{{ $item->raw_id }}" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                            </td>
                        @endif
                        <td class="p-3.5 text-stone-600 text-xs font-semibold border-r border-stone-200">
                            {{ $item->tanggal->format('d/m/Y') }}
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <x-badge :variant="$item->stream_badge" size="xs">
                                {{ $item->stream_label }}
                            </x-badge>
                        </td>
                        <td class="p-3.5 border-r border-stone-200 font-bold text-stone-800 text-xs">
                            {{ $item->kategori }}
                        </td>
                        <td class="p-3.5 text-right font-black text-rose-700 text-xs border-r border-stone-200">
                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-xs text-stone-700 font-medium border-r border-stone-200">
                            {{ $item->keterangan }}
                        </td>
                        <td class="p-3.5 text-center text-xs font-semibold text-stone-600 border-r border-stone-200">
                            {{ $item->petugas }}
                        </td>
                        <td class="p-3.5 text-center">
                            @if ($item->can_delete)
                                <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteExpense({{ $item->raw_id }})" data-confirm="Apakah Anda yakin ingin menghapus catatan pengeluaran ini?" title="Hapus Pengeluaran">
                                    Hapus
                                </x-button>
                            @else
                                <span class="text-[10px] text-stone-400 font-mono italic">Terkunci</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="$stream === 'operasional' ? 8 : 7" title="Belum ada transaksi kas keluar" message="Tidak ada catatan beban pengeluaran yang sesuai dengan filter yang dipilih." />
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $paginatedOutflows->links() }}
        </div>
    </div>

    <!-- Floating Card: Catat Kas Keluar Operasional Baru -->
    <x-floating-card 
        :show="$showCreateModal" 
        title="Catat Kas Keluar Operasional" 
        subtitle="Dokumentasikan pengeluaran kas non-BOS (ATK, Sarpras, Listrik/Air, Konsumsi, dsb.)."
        badge="KAS KELUAR YAYASAN"
        badgeVariant="rose"
        icon="arrow-up-right"
        maxWidth="max-w-lg"
        closeAction="closeCreateModal"
    >
        <form wire:submit.prevent="saveExpense" class="space-y-4">
            <!-- Tanggal Pengeluaran -->
            <div>
                <label for="tanggal" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Tanggal Transaksi</label>
                <input type="date" id="tanggal" wire:model="tanggal" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
                @error('tanggal') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Kategori Pengeluaran -->
            <div>
                <label for="kategori_pengeluaran_id" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Kategori Pengeluaran</label>
                <select id="kategori_pengeluaran_id" wire:model="kategori_pengeluaran_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    @foreach ($categories as $c)
                        <option value="{{ $c['id'] }}">{{ $c['nama'] }}</option>
                    @endforeach
                </select>
                @error('kategori_pengeluaran_id') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Nominal Pengeluaran -->
            <div>
                <label for="jumlah" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Nominal Pengeluaran (Rp)</label>
                <input type="number" id="jumlah" wire:model="jumlah" min="1000" step="1000" placeholder="Contoh: 150000" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
                @error('jumlah') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Keterangan / Deskripsi Beban -->
            <div>
                <label for="keterangan" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Keterangan / Uraian Belanja</label>
                <textarea id="keterangan" wire:model="keterangan" rows="3" placeholder="Tulis rincian pembelian ATK, perbaikan sarpras, konsumsi rapat..." class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs"></textarea>
                @error('keterangan') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                <x-button type="button" variant="secondary" size="sm" wire:click="closeCreateModal">
                    Batal
                </x-button>
                <x-button type="submit" variant="danger-solid" size="sm" icon="check">
                    Simpan Kas Keluar
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
