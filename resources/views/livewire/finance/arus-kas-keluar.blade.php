<div class="space-y-6 font-sans" x-data="{ previewOpen: false, previewSrc: '', previewTitle: '' }" x-init="$watch('$wire.showPreviewBuktiModal', val => { if(val) { previewSrc = $wire.previewBuktiUrl; previewTitle = $wire.previewBuktiTitle; previewOpen = true; } })" @keydown.escape.window="previewOpen = false">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Gabungan Arus Kas Keluar" 
        subtitle="Pusat analitik & rekapitulasi seluruh pengeluaran: Operasional Yayasan, Gaji Guru, serta Fasilitas Kasbon (Non-BOS)."
        badge="MONITORING & KAS KELUAR"
        badgeVariant="rose"
        icon="trending-down"
    >
        <x-slot:actions>
            <x-button variant="outline" size="sm" icon="printer" wire:click="exportPdf" :disabled="$paginatedOutflows->total() === 0" title="{{ $paginatedOutflows->total() === 0 ? 'Tidak ada catatan pengeluaran untuk diekspor' : 'Ekspor Dokumen PDF Sesuai Filter' }}">
                Cetak PDF
            </x-button>
            <x-button variant="outline" size="sm" icon="file-spreadsheet" wire:click="exportExcel" :disabled="$paginatedOutflows->total() === 0" title="{{ $paginatedOutflows->total() === 0 ? 'Tidak ada catatan pengeluaran untuk diekspor' : 'Ekspor Spreadsheet Excel Sesuai Filter' }}">
                Ekspor Excel
            </x-button>
            @if(!auth()->user()->isSuperAdmin2())
            <x-button variant="danger-solid" size="sm" icon="plus" wire:click="openCreateModal">
                Catat Kas Keluar
            </x-button>
            @endif
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

        <!-- Comprehensive Filter Toolbar Row -->
        <div class="flex items-center justify-between gap-4 border-t border-stone-100 pt-3 flex-wrap">
            <div class="flex items-center gap-2.5 flex-wrap flex-1">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Periode:</span>
                    <x-date-filter model="filterPeriode" startDateModel="startDate" endDateModel="endDate" />
                </div>

                <!-- Payment Method Filter -->
                <div class="flex items-center gap-1.5">
                    <select wire:model.live="filterMetode" class="bg-stone-50 border border-stone-200 rounded-xl px-2.5 py-1.5 text-stone-700 text-xs font-bold focus:ring-2 focus:ring-rose-600 focus:bg-white transition shadow-2xs">
                        <option value="semua">Semua Metode Beban</option>
                        <option value="tunai">Tunai / Kasbon</option>
                        <option value="transfer">Transfer Bank / Payroll</option>
                        <option value="bank">Bank</option>
                    </select>
                </div>

                <!-- Nominal Range Filters -->
                <div class="flex items-center gap-1 bg-stone-50 border border-stone-200 px-2 py-1 rounded-xl shadow-2xs">
                    <span class="text-[11px] font-bold text-stone-400">Rp</span>
                    <input type="number" wire:model.live.debounce.400ms="nominalMin" placeholder="Nominal Min" class="w-24 bg-transparent border-0 p-0 text-xs font-bold text-stone-800 placeholder-stone-400 focus:ring-0" />
                    <span class="text-stone-300 text-xs">-</span>
                    <input type="number" wire:model.live.debounce.400ms="nominalMax" placeholder="Nominal Max" class="w-24 bg-transparent border-0 p-0 text-xs font-bold text-stone-800 placeholder-stone-400 focus:ring-0" />
                </div>

                <!-- Reset Filter Button -->
                @if ($this->activeFilterCount > 0)
                    <button type="button" wire:click="resetFilters" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold border border-stone-300 transition shadow-2xs cursor-pointer">
                        <x-lucide-refresh-cw class="w-3 h-3 text-stone-500" />
                        <span>Reset ({{ $this->activeFilterCount }})</span>
                    </button>
                @endif
            </div>

            @if (count($selectedIds) > 0 && !auth()->user()->isSuperAdmin2() && auth()->user()->role?->nama !== 'finance')
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
                    <x-table.th align="center" class="w-28">Bukti Struk</x-table.th>
                    <x-table.th align="center" class="w-36">Petugas / PIC</x-table.th>
                    <x-table.th align="center" class="w-32">Aksi</x-table.th>
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
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-bold text-xs text-stone-900">{{ $item->tanggal->translatedFormat('d M Y') }}</div>
                            @if($item->tanggal->format('H:i') !== '00:00')
                                <div class="text-[10px] text-stone-400 font-mono">{{ $item->tanggal->format('H:i') }} WIB</div>
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
                        <td class="p-3.5 text-right font-black text-rose-700 text-xs border-r border-stone-200">
                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-xs text-stone-700 font-medium border-r border-stone-200">
                            {{ $item->keterangan }}
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($item->bukti)
                                <div class="flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $item->bukti) }}" 
                                         alt="Bukti {{ $item->kategori }}"
                                         @click="previewSrc = '{{ asset('storage/' . $item->bukti) }}'; previewTitle = 'Bukti {{ addslashes($item->kategori) }} ({{ $item->tanggal->format('d/m/Y') }})'; previewOpen = true"
                                         class="w-10 h-10 object-cover rounded-lg border border-stone-200 shadow-2xs cursor-pointer hover:opacity-80 hover:scale-105 transition duration-150"
                                         title="Klik untuk memperbesar bukti"
                                         loading="lazy"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';" />
                                    <button type="button" 
                                         style="display: none;"
                                         @click="previewSrc = '{{ asset('storage/' . $item->bukti) }}'; previewTitle = 'Bukti {{ addslashes($item->kategori) }} ({{ $item->tanggal->format('d/m/Y') }})'; previewOpen = true"
                                         class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-lg text-[11px] font-bold shadow-2xs transition cursor-pointer">
                                        <x-lucide-image class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                        <span>Bukti</span>
                                    </button>
                                </div>
                            @elseif ($item->can_edit && !auth()->user()->isSuperAdmin2())
                                <button 
                                    type="button" 
                                    wire:click="openEditModal({{ $item->raw_id }})" 
                                    class="inline-flex items-center gap-1 px-2 py-1 bg-stone-50 hover:bg-stone-100 text-stone-500 hover:text-stone-800 border border-dashed border-stone-300 rounded-lg text-[10px] font-semibold transition cursor-pointer"
                                    title="Upload Bukti Struk/TF"
                                >
                                    <x-lucide-plus class="w-3 h-3 text-stone-400 shrink-0" />
                                    <span>+ Bukti</span>
                                </button>
                            @else
                                <span class="text-xs text-stone-400 font-medium italic">-</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center text-xs font-semibold text-stone-600 border-r border-stone-200">
                            {{ $item->petugas }}
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if ($item->can_edit && !auth()->user()->isSuperAdmin2())
                                    <x-button type="button" variant="outline" size="xs" icon="edit" wire:click="openEditModal({{ $item->raw_id }})" title="Edit Pengeluaran & Bukti">
                                        Edit
                                    </x-button>
                                @endif
                                @if ($item->can_delete && !auth()->user()->isSuperAdmin2())
                                    <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteExpense({{ $item->raw_id }})" data-confirm="{{ auth()->user()->role?->nama === 'finance' ? 'Ajukan permohonan penghapusan catatan pengeluaran ini ke Super Admin / Super Admin 2?' : 'Apakah Anda yakin ingin menghapus catatan pengeluaran ini?' }}" title="Hapus Pengeluaran">
                                        Hapus
                                    </x-button>
                                @elseif(!$item->can_edit)
                                    <span class="text-[10px] text-stone-400 font-mono italic">Terkunci</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="$stream === 'operasional' ? 9 : 8" title="Belum ada transaksi kas keluar" message="Tidak ada catatan beban pengeluaran yang sesuai dengan filter yang dipilih." />
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
                <div class="flex items-center justify-between mb-1.5">
                    <label for="kategori_pengeluaran_id" class="block text-xs font-bold text-stone-600 uppercase tracking-wider">Kategori Pengeluaran</label>
                    <button type="button" wire:click="$toggle('is_kategori_kustom')" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-900 hover:underline cursor-pointer">
                        {{ $is_kategori_kustom ? '← Pilih dari Daftar Kategori' : '+ Tambah Kategori Baru' }}
                    </button>
                </div>
                @if(!$is_kategori_kustom)
                    <select id="kategori_pengeluaran_id" wire:model="kategori_pengeluaran_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                        @foreach ($categories as $c)
                            <option value="{{ $c['id'] }}">{{ $c['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('kategori_pengeluaran_id') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                @else
                    <input type="text" id="kategori_keluar_kustom" wire:model="kategori_keluar_kustom" placeholder="Ketik nama kategori pengeluaran baru..." class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
                    @error('kategori_keluar_kustom') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                @endif
            </div>

            <!-- Nominal Pengeluaran -->
            <x-input-currency
                id="jumlah"
                name="jumlah"
                wire:model="jumlah"
                label="Nominal Pengeluaran (Rp)"
                placeholder="Contoh: 150.000"
                required
            />

            <!-- Keterangan / Deskripsi Beban -->
            <div>
                <label for="keterangan" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Keterangan / Uraian Belanja</label>
                <textarea id="keterangan" wire:model="keterangan" rows="3" placeholder="Tulis rincian pembelian ATK, perbaikan sarpras, konsumsi rapat..." class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs"></textarea>
                @error('keterangan') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Upload Foto Bukti Pengeluaran (Struk / Nota / TF) -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-stone-600 uppercase tracking-wider">
                        Foto Bukti Pengeluaran (Struk / Nota / TF)
                    </label>
                    <span class="text-[10px] text-stone-400 font-semibold">Opsional • Maks. 2MB • Foto/Gambar</span>
                </div>

                @if ($bukti_foto)
                    <div class="p-3 bg-emerald-50/70 border border-emerald-300 rounded-xl flex items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ $bukti_foto->temporaryUrl() }}" alt="Preview Bukti" class="w-12 h-12 object-cover rounded-lg border border-emerald-200 shadow-2xs shrink-0" />
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-emerald-950 block truncate">{{ $bukti_foto->getClientOriginalName() }}</span>
                                <span class="text-[10px] text-emerald-700 font-semibold">{{ number_format($bukti_foto->getSize() / 1024, 1) }} KB</span>
                            </div>
                        </div>
                        <button type="button" wire:click="$set('bukti_foto', null)" class="px-2.5 py-1 text-xs font-bold text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 rounded-lg shadow-2xs transition shrink-0 cursor-pointer">
                            Hapus
                        </button>
                    </div>
                @else
                    <div class="relative border-2 border-dashed border-stone-300 hover:border-emerald-500 rounded-xl p-3.5 bg-stone-50/60 hover:bg-emerald-50/20 text-center transition group cursor-pointer">
                        <input 
                            type="file" 
                            wire:model="bukti_foto" 
                            accept="image/jpeg,image/png,image/jpg,image/webp" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                        />
                        <div class="flex items-center justify-center gap-2 pointer-events-none text-stone-600 group-hover:text-emerald-800">
                            <x-lucide-camera class="w-4 h-4 text-emerald-600" />
                            <span class="text-xs font-bold">Pilih foto struk / bukti transfer (Maks. 2MB)</span>
                        </div>
                        <div wire:loading wire:target="bukti_foto" class="absolute inset-0 bg-white/90 backdrop-blur-xs rounded-xl flex items-center justify-center z-20">
                            <span class="text-xs font-bold text-emerald-800 flex items-center gap-1.5">
                                <span class="animate-spin inline-block w-3.5 h-3.5 border-2 border-emerald-600 border-t-transparent rounded-full"></span>
                                Mengunggah foto...
                            </span>
                        </div>
                    </div>
                @endif
                @error('bukti_foto') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                <x-button type="button" variant="secondary" size="sm" wire:click="closeCreateModal">
                    Batal
                </x-button>
                <x-button type="submit" variant="danger-solid" size="sm" icon="check" loadingTarget="saveExpense">
                    Simpan Kas Keluar
                </x-button>
            </div>
        </form>
    </x-floating-card>

    <!-- Floating Card: Edit Pengeluaran & Bukti Pembayaran -->
    @if ($showEditModal)
        <x-floating-card 
            :show="true" 
            title="Edit Pengeluaran & Bukti Pembayaran" 
            subtitle="Perbarui data beban pengeluaran operasional serta lampiran foto bukti nota / struk / transfer."
            badge="EDIT PENGELUARAN"
            badgeVariant="rose"
            icon="edit"
            maxWidth="max-w-lg"
            closeAction="closeEditModal"
        >
            <form wire:submit.prevent="updateExpense" class="space-y-4 text-xs">
                <!-- Tanggal Pengeluaran -->
                <div>
                    <label class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Tanggal Transaksi</label>
                    <input type="date" wire:model="edit_tanggal" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
                    @error('edit_tanggal') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Kategori Pengeluaran -->
                <div>
                    <label class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Kategori Pengeluaran</label>
                    <select wire:model="edit_kategori_pengeluaran_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                        @foreach ($categories as $c)
                            <option value="{{ $c['id'] }}">{{ $c['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('edit_kategori_pengeluaran_id') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Nominal Pengeluaran -->
                <x-input-currency
                    id="edit_jumlah"
                    name="edit_jumlah"
                    wire:model="edit_jumlah"
                    label="Nominal Pengeluaran (Rp)"
                    placeholder="Contoh: 150.000"
                    required
                />

                <!-- Keterangan / Deskripsi Beban -->
                <div>
                    <label class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Keterangan / Uraian Belanja</label>
                    <textarea wire:model="edit_keterangan" rows="3" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs"></textarea>
                    @error('edit_keterangan') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Foto Bukti Pengeluaran Existing -->
                @if ($edit_existing_bukti)
                    <div class="space-y-1.5 p-3 bg-stone-50 border border-stone-200 rounded-xl">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-stone-700">Foto Bukti Saat Ini</span>
                            <button type="button" wire:click="deleteEditBukti" wire:confirm="Hapus foto bukti pengeluaran ini?" class="text-[11px] text-rose-600 hover:text-rose-800 font-bold inline-flex items-center gap-1 cursor-pointer">
                                <x-lucide-trash-2 class="w-3 h-3" />
                                <span>Hapus Foto</span>
                            </button>
                        </div>
                        <div class="rounded-lg overflow-hidden border border-stone-300 bg-stone-900 p-1">
                            <img src="{{ asset('storage/' . $edit_existing_bukti) }}" alt="Bukti" class="w-full max-h-48 object-contain mx-auto rounded" />
                        </div>
                    </div>
                @endif

                <!-- Upload Bukti Baru / Pengganti -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-600 uppercase tracking-wider">
                        {{ $edit_existing_bukti ? 'Ganti Foto Bukti Pengeluaran' : 'Unggah Foto Bukti Pengeluaran (Struk/TF)' }}
                    </label>

                    @if ($edit_bukti_foto)
                        <div class="p-2.5 bg-emerald-50 border border-emerald-300 rounded-xl flex items-center justify-between gap-2 shadow-2xs">
                            <div class="flex items-center gap-2 min-w-0">
                                <img src="{{ $edit_bukti_foto->temporaryUrl() }}" alt="Preview" class="w-10 h-10 object-cover rounded-lg border border-emerald-200 shadow-2xs shrink-0" />
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-emerald-950 block truncate">{{ $edit_bukti_foto->getClientOriginalName() }}</span>
                                    <span class="text-[10px] text-emerald-700 font-semibold">{{ number_format($edit_bukti_foto->getSize() / 1024, 1) }} KB</span>
                                </div>
                            </div>
                            <button type="button" wire:click="$set('edit_bukti_foto', null)" class="px-2 py-1 text-[11px] font-bold text-rose-700 bg-white border border-rose-200 rounded-lg shrink-0 cursor-pointer">
                                Batal
                            </button>
                        </div>
                    @else
                        <div class="relative border-2 border-dashed border-stone-300 hover:border-emerald-500 rounded-xl p-3 bg-stone-50 hover:bg-emerald-50/20 text-center transition group cursor-pointer">
                            <input 
                                type="file" 
                                wire:model="edit_bukti_foto" 
                                accept="image/jpeg,image/png,image/jpg,image/webp" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                            />
                            <div class="flex items-center justify-center gap-2 pointer-events-none text-stone-600 group-hover:text-emerald-800">
                                <x-lucide-camera class="w-4 h-4 text-emerald-600" />
                                <span class="text-xs font-bold">Pilih file foto baru (Maks. 2MB, JPG/PNG/WEBP)</span>
                            </div>
                        </div>
                    @endif
                    <div wire:loading wire:target="edit_bukti_foto" class="text-[11px] text-emerald-700 font-bold mt-1 flex items-center gap-1.5">
                        <span class="animate-spin inline-block w-3 h-3 border-2 border-emerald-600 border-t-transparent rounded-full"></span>
                        <span>Mengunggah foto...</span>
                    </div>
                    @error('edit_bukti_foto') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Modal Action Buttons -->
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                    <x-button type="button" variant="secondary" size="sm" wire:click="closeEditModal">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm" icon="check" loadingTarget="updateExpense">
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </x-floating-card>
    @endif

    <!-- ALPINE.JS LIGHTBOX PREVIEW MODAL (CLIENT-SIDE) -->
    <div x-show="previewOpen" 
         x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-950/80 backdrop-blur-xs"
         @click.self="previewOpen = false">
        <div class="relative max-w-3xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden border border-stone-200"
             @click.outside="previewOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-stone-200 bg-stone-50">
                <div class="flex items-center gap-2 min-w-0">
                    <x-lucide-image class="w-4 h-4 text-emerald-600 shrink-0" />
                    <h4 class="text-xs font-extrabold text-stone-900 truncate" x-text="previewTitle || 'Foto Bukti Pengeluaran'"></h4>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a :href="previewSrc" target="_blank" download class="p-1.5 bg-stone-200/70 hover:bg-emerald-100 text-stone-700 hover:text-emerald-800 rounded-lg transition cursor-pointer" title="Buka Gambar Asli">
                        <x-lucide-external-link class="w-4 h-4" />
                    </a>
                    <button type="button" @click="previewOpen = false" class="p-1.5 bg-stone-200/70 hover:bg-rose-100 text-stone-700 hover:text-rose-800 rounded-lg transition cursor-pointer" title="Tutup (ESC)">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
            </div>
            <!-- Modal Body Image -->
            <div class="p-4 bg-stone-950 flex items-center justify-center max-h-[75vh] overflow-auto">
                <img :src="previewSrc" alt="Foto Bukti Pengeluaran" class="max-w-full max-h-[70vh] rounded-lg object-contain shadow-xl" />
            </div>
            <!-- Modal Footer -->
            <div class="px-5 py-3 bg-stone-50 border-t border-stone-200 flex items-center justify-between">
                <span class="text-[11px] text-stone-500 font-medium">Tekan <kbd class="px-1.5 py-0.5 bg-stone-200 rounded text-[10px] font-mono">ESC</kbd> atau klik di luar untuk menutup</span>
                <x-button type="button" variant="secondary" size="sm" @click="previewOpen = false">
                    Tutup
                </x-button>
            </div>
        </div>
    </div>
</div>
