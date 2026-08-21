<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Gabungan Arus Kas Masuk" 
        subtitle="Pusat analitik &amp; rekapitulasi seluruh penerimaan: Pembayaran SPP/Tagihan Siswa, Infaq &amp; Donasi Yayasan, serta Setoran Tabungan."
        badge="MONITORING &amp; KAS MASUK"
        badgeVariant="emerald"
        icon="trending-up"
    >
        <x-slot:actions>
            <x-button variant="outline" size="sm" icon="file-text" wire:click="exportPdf">
                Ekspor PDF
            </x-button>
            <x-button variant="primary" size="sm" icon="plus" wire:click="openCreateModal">
                Catat Kas Masuk Yayasan
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Monitoring & Gabungan Arus Kas Masuk"
        :steps="[
            ['title' => 'Grafik Tren Bulanan', 'desc' => 'Visualisasi tren arus kas masuk 6 bulan terakhir merangkum perbandingan setoran SPP siswa, kas infaq/donasi, dan tabungan.'],
            ['title' => 'Filter Multi-Stream', 'desc' => 'Pilih tab stream (SPP & Tagihan, Kas Yayasan, Tabungan) atau periode (Hari Ini, Kemarin, Minggu Ini, Bulan Ini) untuk penelusuran cepat.'],
            ['title' => 'Pencatatan Pemasukan', 'desc' => 'Klik Catat Kas Masuk Yayasan untuk mendokumentasikan penerimaan infaq, donasi, sedekah subuh, atau sponsor yayasan.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- 4-Stat Metric Cards Row (Non-BOS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card 
            title="Total Seluruh Kas Masuk" 
            :value="'Rp ' . number_format($totalInflowAll, 0, ',', '.')" 
            subtitle="Akumulasi seluruh penerimaan kas masuk terpilih"
            icon="trending-up" 
            variant="emerald" 
        />
        <x-stat-card 
            title="Setoran SPP &amp; Tagihan Siswa" 
            :value="'Rp ' . number_format($totalTagihanSpp, 0, ',', '.')" 
            subtitle="SPP bulanan, gedung, tahunan, seragam, dsb."
            icon="credit-card" 
            variant="white" 
        />
        <x-stat-card 
            title="Kas Masuk Yayasan (Infaq &amp; Donasi)" 
            :value="'Rp ' . number_format($totalKasYayasan, 0, ',', '.')" 
            subtitle="Infaq, sedekah subuh, donatur, sponsor"
            icon="heart-handshake" 
            variant="white" 
        />
        <x-stat-card 
            title="Setoran Tabungan Siswa" 
            :value="'Rp ' . number_format($totalTabunganSetor, 0, ',', '.')" 
            subtitle="Dana tabungan masuk kas sekolah"
            icon="wallet" 
            variant="white" 
        />
    </div>

    <!-- VISUAL ANALYTICS & CHARTS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 1. Monthly Inflow Trend Chart (2 Cols) -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-stone-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                        <x-lucide-bar-chart-3 class="w-4 h-4 text-emerald-700" />
                        <span>Tren Arus Kas Masuk (6 Bulan Terakhir)</span>
                    </h3>
                    <p class="text-[11px] text-stone-500 font-medium mt-0.5">Komposisi setoran SPP siswa, penerimaan infaq/donasi yayasan, dan setoran tabungan.</p>
                </div>
                <!-- Chart Legend -->
                <div class="flex items-center gap-3 text-[11px] font-bold flex-wrap">
                    <span class="flex items-center gap-1.5 text-stone-700">
                        <span class="w-2.5 h-2.5 rounded-sm bg-emerald-500 inline-block"></span> SPP &amp; Tagihan
                    </span>
                    <span class="flex items-center gap-1.5 text-stone-700">
                        <span class="w-2.5 h-2.5 rounded-sm bg-amber-500 inline-block"></span> Kas Yayasan (Infaq)
                    </span>
                    <span class="flex items-center gap-1.5 text-stone-700">
                        <span class="w-2.5 h-2.5 rounded-sm bg-purple-500 inline-block"></span> Tabungan
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
                                <div class="text-emerald-300 font-black">Rp {{ number_format($mData['total'], 0, ',', '.') }}</div>
                            </div>

                            <!-- Stacked Bar Column -->
                            <div class="w-full max-w-[48px] bg-stone-100 rounded-t-xl overflow-hidden flex flex-col-reverse transition-all duration-300 group-hover:scale-105 shadow-2xs" style="height: {{ max(10, $mData['height_percentage']) }}%;">
                                <!-- SPP segment -->
                                @if ($mData['spp'] > 0)
                                    <div class="bg-emerald-500 w-full" style="height: {{ $mData['spp_pct'] }}%;" title="SPP & Tagihan: Rp {{ number_format($mData['spp'], 0, ',', '.') }}"></div>
                                @endif
                                <!-- Kas Yayasan segment -->
                                @if ($mData['kas_yayasan'] > 0)
                                    <div class="bg-amber-500 w-full" style="height: {{ $mData['kas_pct'] }}%;" title="Infaq/Donasi: Rp {{ number_format($mData['kas_yayasan'], 0, ',', '.') }}"></div>
                                @endif
                                <!-- Tabungan segment -->
                                @if ($mData['tabungan'] > 0)
                                    <div class="bg-purple-500 w-full" style="height: {{ $mData['tab_pct'] }}%;" title="Tabungan: Rp {{ number_format($mData['tabungan'], 0, ',', '.') }}"></div>
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
                <span>* Angka dalam grafik terupdate otomatis dari transaksi kas masuk sekolah</span>
                <span class="font-bold text-stone-700">Puncak Pemasukan: Rp {{ number_format($maxMonthTotal, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- 2. Stream Breakdown & Top Inflow Categories Card (1 Col) -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2 border-b border-stone-100 pb-3">
                <x-lucide-pie-chart class="w-4 h-4 text-emerald-700" />
                <span>Proporsi Alokasi Kas Masuk</span>
            </h3>

            @php
                $sppShare = $totalInflowAll > 0 ? round(($totalTagihanSpp / $totalInflowAll) * 100, 1) : 0;
                $kasShare = $totalInflowAll > 0 ? round(($totalKasYayasan / $totalInflowAll) * 100, 1) : 0;
                $tabShare = $totalInflowAll > 0 ? round(($totalTabunganSetor / $totalInflowAll) * 100, 1) : 0;
            @endphp

            <!-- Stream Allocation Bars -->
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-xs font-bold text-stone-700 mb-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>SPP &amp; Tagihan Siswa</span>
                        </span>
                        <span>{{ $sppShare }}% (Rp {{ number_format($totalTagihanSpp, 0, ',', '.') }})</span>
                    </div>
                    <div class="w-full h-2 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $sppShare }}%;"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-bold text-stone-700 mb-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>Kas Masuk Yayasan (Infaq)</span>
                        </span>
                        <span>{{ $kasShare }}% (Rp {{ number_format($totalKasYayasan, 0, ',', '.') }})</span>
                    </div>
                    <div class="w-full h-2 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-500 rounded-full" style="width: {{ $kasShare }}%;"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-bold text-stone-700 mb-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            <span>Setoran Tabungan</span>
                        </span>
                        <span>{{ $tabShare }}% (Rp {{ number_format($totalTabunganSetor, 0, ',', '.') }})</span>
                    </div>
                    <div class="w-full h-2 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-500 rounded-full" style="width: {{ $tabShare }}%;"></div>
                    </div>
                </div>
            </div>

            <!-- Top Inflow Categories List -->
            <div class="pt-3 border-t border-stone-100 space-y-2">
                <h4 class="text-xs font-bold text-stone-600 uppercase tracking-wider">Kategori Pemasukan Yayasan Terbesar:</h4>
                <div class="space-y-1.5">
                    @forelse ($categoryBreakdown as $cItem)
                        <div class="flex items-center justify-between text-xs p-2 bg-stone-50 rounded-xl border border-stone-200">
                            <span class="font-bold text-stone-800">{{ $cItem['nama'] }}</span>
                            <div class="text-right">
                                <span class="font-black text-emerald-800 block">Rp {{ number_format($cItem['nominal'], 0, ',', '.') }}</span>
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
                    wire:click="selectStream('pembayaran_spp')" 
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-1.5 {{ $stream === 'pembayaran_spp' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                    <span>SPP &amp; Tagihan Siswa</span>
                </button>
                <button type="button" 
                    wire:click="selectStream('kas_yayasan')" 
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-1.5 {{ $stream === 'kas_yayasan' ? 'bg-amber-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                    <span>Kas Yayasan (Infaq)</span>
                </button>
                <button type="button" 
                    wire:click="selectStream('tabungan')" 
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-1.5 {{ $stream === 'tabungan' ? 'bg-purple-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-300"></span>
                    <span>Setoran Tabungan</span>
                </button>
            </div>

            <!-- Search Bar & Category Filter -->
            <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap w-full lg:max-w-xl">
                <div class="w-full flex-1">
                    <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari siswa, no. resi, atau sumber..." />
                </div>

                @if ($stream === 'semua' || $stream === 'kas_yayasan')
                    <select wire:model.live="filterKategori" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs shrink-0">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoriOptions as $kat)
                            <option value="{{ $kat }}">{{ $kat }}</option>
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
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                        {{ count($selectedIds) }} transaksi dipilih
                    </span>
                    <x-button variant="danger-solid" size="xs" icon="trash-2" wire:click="bulkDelete" data-confirm="Hapus seluruh transaksi yang dipilih?">
                        Hapus Terpilih
                    </x-button>
                </div>
            @endif
        </div>

        <!-- Unified Inflow Table -->
        <x-table loadingTarget="search, filterKategori, filterPeriode, startDate, endDate, stream, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    @if ($stream === 'kas_yayasan')
                        <th class="w-12 p-3.5 text-center border-r border-emerald-700/60">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                        </th>
                    @endif
                    <x-table.th class="w-36">Tanggal</x-table.th>
                    <x-table.th class="w-48">Stream / Sumber</x-table.th>
                    <x-table.th class="w-44">Kategori / Tagihan</x-table.th>
                    <x-table.th align="right" class="w-44">Nominal Masuk</x-table.th>
                    <x-table.th class="min-w-[200px]">Keterangan / Siswa</x-table.th>
                    <x-table.th align="center" class="w-36">Metode / Resi</x-table.th>
                    <x-table.th align="center" class="w-36">Petugas</x-table.th>
                    <x-table.th align="center" class="w-24">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($paginatedInflows as $item)
                    <tr class="hover:bg-emerald-50/40 transition {{ in_array($item->raw_id, $selectedIds) ? 'bg-emerald-50/60 font-semibold' : '' }}">
                        @if ($stream === 'kas_yayasan')
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
                        <td class="p-3.5 text-right font-black text-emerald-800 text-xs border-r border-stone-200">
                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-xs text-stone-700 font-medium border-r border-stone-200">
                            {{ $item->keterangan }}
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            <span class="text-xs text-stone-700 font-bold block">{{ $item->metode }}</span>
                            @if ($item->no_resi)
                                <span class="text-[10px] font-mono text-stone-400 block">{{ $item->no_resi }}</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center text-xs text-stone-600 font-semibold border-r border-stone-200">
                            {{ $item->petugas }}
                        </td>
                        <td class="p-3.5 text-center">
                            @if ($item->can_delete)
                                <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteIncome({{ $item->raw_id }})" data-confirm="Apakah Anda yakin ingin menghapus catatan penerimaan kas ini?" title="Hapus Penerimaan">
                                    Hapus
                                </x-button>
                            @elseif ($item->stream === 'pembayaran_spp' && $item->raw_id)
                                <a href="{{ route('finance.cetak-resi', $item->raw_id) }}" target="_blank" class="p-1.5 bg-stone-100 hover:bg-emerald-100 text-stone-700 hover:text-emerald-900 rounded-lg inline-flex items-center justify-center border border-stone-300 transition" title="Cetak Resi">
                                    <x-lucide-printer class="w-3.5 h-3.5" />
                                </a>
                            @else
                                <span class="text-[10px] text-stone-400 font-mono italic">Tercatat</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="$stream === 'kas_yayasan' ? 9 : 8" title="Belum ada transaksi kas masuk" message="Tidak ada catatan penerimaan yang sesuai dengan filter yang dipilih." />
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $paginatedInflows->links() }}
        </div>
    </div>

    <!-- Floating Card: Catat Kas Masuk Yayasan Baru -->
    <x-floating-card 
        :show="$showCreateModal" 
        title="Catat Kas Masuk Yayasan" 
        subtitle="Dokumentasikan penerimaan kas non-SPP (Infaq, Sedekah Subuh, Maghrib Mengaji, Donasi, dsb.)."
        badge="KAS MASUK YAYASAN"
        badgeVariant="emerald"
        icon="arrow-down-left"
        maxWidth="max-w-lg"
        closeAction="closeCreateModal"
    >
        <form wire:submit.prevent="saveIncome" class="space-y-4">
            <!-- Tanggal Penerimaan -->
            <div>
                <label for="income_tanggal" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Tanggal Penerimaan</label>
                <input type="date" id="income_tanggal" wire:model="tanggal" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
                @error('tanggal') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Kategori Penerimaan -->
            <div>
                <label for="income_kategori" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Kategori Penerimaan</label>
                <select id="income_kategori" wire:model="kategori" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    @foreach ($kategoriOptions as $kat)
                        <option value="{{ $kat }}">{{ $kat }}</option>
                    @endforeach
                </select>
                @error('kategori') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Nominal Penerimaan -->
            <div>
                <label for="income_jumlah" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Nominal Penerimaan (Rp)</label>
                <input type="number" id="income_jumlah" wire:model="jumlah" min="1000" step="1000" placeholder="Contoh: 500000" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
                @error('jumlah') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Keterangan / Sumber Donatur -->
            <div>
                <label for="income_keterangan" class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">Keterangan / Nama Donatur / Sumber</label>
                <textarea id="income_keterangan" wire:model="keterangan" rows="3" placeholder="Tulis nama donatur, acara, atau keterangan infaq..." class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs"></textarea>
                @error('keterangan') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                <x-button type="button" variant="secondary" size="sm" wire:click="closeCreateModal">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="sm" icon="check">
                    Simpan Kas Masuk
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
