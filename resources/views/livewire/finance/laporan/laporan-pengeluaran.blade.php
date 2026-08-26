<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Laporan Pengeluaran Keuangan" 
        subtitle="Tinjau seluruh transaksi pengeluaran operasional yayasan dalam rentang tanggal tertentu."
        badge="LAPORAN PENGELUARAN"
        badgeVariant="rose"
        icon="trending-down"
    >
        <x-slot:actions>
            <x-button 
                variant="outline" 
                size="sm" 
                icon="eye" 
                wire:click="openPreviewPdf" 
                :disabled="$totalCount === 0"
                title="{{ $totalCount === 0 ? 'Tidak ada data untuk dipratinjau' : 'Buka Pratinjau Dokumen PDF' }}"
            >
                Pratinjau PDF
            </x-button>
            <x-button 
                variant="primary" 
                size="sm" 
                icon="download" 
                href="{{ route('finance.export.pengeluaran', array_filter(['filter_periode' => $filterPeriode, 'start_date' => $startDate, 'end_date' => $endDate, 'bulan' => $bulan, 'kategori_pengeluaran_id' => $kategori_pengeluaran_id, 'search' => $search])) }}" 
                target="_blank" 
                :disabled="$totalCount === 0"
                title="{{ $totalCount === 0 ? 'Tidak ada data untuk diekspor' : 'Unduh Rekap Spreadsheet Excel (.csv)' }}"
            >
                Ekspor Excel (.csv)
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Laporan Pengeluaran Keuangan"
        :steps="[
            ['title' => 'Filter Periode & Bulan', 'desc' => 'Gunakan filter preset (Hari Ini, Bulan Ini, dll.) atau Rentang Tanggal Kustom dan filter bulan spesifik.'],
            ['title' => 'Pratinjau & Cetak PDF', 'desc' => 'Pratinjau dokumen laporan pengeluaran kas resmi ber-QR Code & TTD sebelum mengunduh.'],
            ['title' => 'Kategori Operasional', 'desc' => 'Tabel merangkum beban pengadaan, gaji guru, pengajuan dana BOS, dan pemeliharaan.']
        ]"
    />

    <!-- Filters Bar -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Top Filters Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Pencarian</label>
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari deskripsi pengeluaran..." />
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Kategori Pengeluaran</label>
                <select wire:model.live="kategori_pengeluaran_id" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Filter Bulan</label>
                <select wire:model.live="bulan" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    <option value="">Semua Bulan</option>
                    @foreach ($listBulan as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Global Date Filter Component (Preset & Custom Range) -->
        <div class="border-t border-stone-100 pt-3">
            <x-date-filter 
                model="filterPeriode" 
                startDateModel="startDate" 
                endDateModel="endDate" 
                label="Filter Periode Tanggal Pengeluaran (Hari Ini, Bulan Ini, atau Rentang Kustom)" 
            />
        </div>

        <!-- Table -->
        <x-table loadingTarget="search, filterPeriode, startDate, endDate, bulan, kategori_pengeluaran_id, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-36">Tanggal</x-table.th>
                    <x-table.th class="w-48">Kategori</x-table.th>
                    <x-table.th class="min-w-[200px]">Keterangan</x-table.th>
                    <x-table.th align="center" class="w-36">Petugas</x-table.th>
                    <x-table.th align="right" class="w-44">Jumlah Pengeluaran</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($expenditures as $e)
                    <tr class="hover:bg-rose-50/40 transition">
                        <td class="p-3.5 text-xs font-bold text-stone-900 border-r border-stone-200">{{ $e->tanggal ? $e->tanggal->format('d/m/Y') : '-' }}</td>
                        <td class="p-3.5 border-r border-stone-200">
                            <x-badge variant="rose" size="xs">
                                {{ $e->kategori->nama ?? 'Umum' }}
                            </x-badge>
                        </td>
                        <td class="p-3.5 text-xs text-stone-700 font-medium border-r border-stone-200">{{ $e->keterangan ?? '-' }}</td>
                        <td class="p-3.5 text-xs font-semibold text-stone-600 text-center border-r border-stone-200">{{ $e->petugas->nama ?? '-' }}</td>
                        <td class="p-3.5 text-xs font-black text-rose-700 text-right">Rp {{ number_format($e->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <x-table.empty :colspan="5" title="Tidak ada data pengeluaran" message="Tidak ditemukan transaksi pengeluaran pada kriteria filter terpilih." />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $expenditures->links() }}
        </div>
    </div>

    <!-- PDF Interactive Preview Modal -->
    @if ($showPreviewModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/75 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 animate-fade-in" wire:keydown.escape="closePreviewPdf">
            <div class="bg-white rounded-3xl shadow-2xl border border-stone-200 w-full max-w-5xl overflow-hidden flex flex-col max-h-[92vh]" @click.away="$wire.closePreviewPdf()">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-emerald-900 text-white flex items-center justify-between border-b border-emerald-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-emerald-300">
                            <x-lucide-file-text class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-sm font-black tracking-tight text-white uppercase">Pratinjau Laporan Pengeluaran PDF</h3>
                            <p class="text-[11px] text-emerald-200 font-medium">Bulan: {{ $bulan ?: 'Semua Bulan' }} | Periode: {{ ucfirst(str_replace('_', ' ', $filterPeriode)) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a 
                            href="{{ route('finance.laporan.pengeluaran.pdf', array_filter(['filter_periode' => $filterPeriode, 'start_date' => $startDate, 'end_date' => $endDate, 'bulan' => $bulan, 'kategori_pengeluaran_id' => $kategori_pengeluaran_id, 'search' => $search, 'download' => 1])) }}" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer"
                        >
                            <x-lucide-download class="w-3.5 h-3.5" />
                            <span>Unduh PDF</span>
                        </a>
                        <a 
                            href="{{ route('finance.laporan.pengeluaran.pdf', array_filter(['filter_periode' => $filterPeriode, 'start_date' => $startDate, 'end_date' => $endDate, 'bulan' => $bulan, 'kategori_pengeluaran_id' => $kategori_pengeluaran_id, 'search' => $search])) }}" 
                            target="_blank" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition cursor-pointer"
                        >
                            <x-lucide-printer class="w-3.5 h-3.5" />
                            <span>Cetak</span>
                        </a>
                        <button 
                            type="button" 
                            wire:click="closePreviewPdf" 
                            class="p-1.5 text-white/70 hover:text-white hover:bg-white/10 rounded-xl transition cursor-pointer"
                        >
                            <x-lucide-x class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <!-- Modal Body (Embedded PDF Viewer) -->
                <div class="flex-1 bg-stone-100 p-2 sm:p-4 min-h-[520px] max-h-[72vh] overflow-hidden">
                    <iframe 
                        src="{{ route('finance.laporan.pengeluaran.pdf', array_filter(['filter_periode' => $filterPeriode, 'start_date' => $startDate, 'end_date' => $endDate, 'bulan' => $bulan, 'kategori_pengeluaran_id' => $kategori_pengeluaran_id, 'search' => $search])) }}" 
                        class="w-full h-full min-h-[500px] rounded-xl border border-stone-300 bg-white shadow-inner" 
                        title="Pratinjau PDF Laporan Pengeluaran"
                    ></iframe>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3 bg-stone-50 border-t border-stone-200 flex items-center justify-between text-xs text-stone-500">
                    <span class="font-medium">Tekan tombol ESC atau Tutup untuk kembali.</span>
                    <x-button variant="secondary" size="xs" wire:click="closePreviewPdf">
                        Tutup
                    </x-button>
                </div>
            </div>
        </div>
    @endif
</div>
