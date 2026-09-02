<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Laporan Pengeluaran Keuangan" 
        subtitle="Tinjau, catat manual, dan susun laporan pengeluaran kas operasional yayasan secara komprehensif."
        badge="LAPORAN PENGELUARAN"
        badgeVariant="rose"
        icon="trending-down"
    >
        <x-slot:actions>
            <x-button 
                variant="primary" 
                size="sm" 
                icon="plus" 
                wire:click="openCreateModal"
                title="Catat transaksi pengeluaran operasional kas baru secara manual"
            >
                Catat Pengeluaran
            </x-button>

            <x-button 
                variant="secondary" 
                size="sm" 
                icon="file-cog" 
                wire:click="openManualReportModal"
                title="Susun dan konfigurasi laporan keuangan manual dengan judul & catatan khusus"
            >
                Buat Laporan Kustom
            </x-button>

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
                variant="secondary" 
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
        title="Petunjuk Laporan & Pencatatan Pengeluaran"
        :steps="[
            ['title' => 'Catat Pengeluaran Manual', 'desc' => 'Gunakan tombol Catat Pengeluaran untuk mencatat belanja operasional, pemeliharaan, atau pengadaan kas.'],
            ['title' => 'Buat Laporan Kustom', 'desc' => 'Atur judul laporan, rentang tanggal spesifik, catatan pengantar/penutup, dan tanda tangan resmi pada modal Buat Laporan Kustom.'],
            ['title' => 'Filter & Ekspor Lengkap', 'desc' => 'Data dapat difilter berdasarkan bulan, kategori, atau rentang tanggal kustom dan dicetak ke PDF/Excel.']
        ]"
    />

    <!-- Summary Overview Card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 bg-white border border-stone-200 rounded-2xl shadow-2xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                <x-lucide-receipt class="w-5 h-5" />
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-stone-500 uppercase tracking-wider block">Total Transaksi</span>
                <span class="text-lg font-black text-stone-900">{{ number_format($totalCount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="p-4 bg-white border border-stone-200 rounded-2xl shadow-2xs flex items-center gap-3.5 sm:col-span-2">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                <x-lucide-wallet class="w-5 h-5" />
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-stone-500 uppercase tracking-wider block">Akumulasi Beban Pengeluaran</span>
                <span class="text-xl font-black text-rose-700">Rp {{ number_format($totalSum, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

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
                    <x-table.th class="min-w-[200px]">Keterangan & Rincian</x-table.th>
                    <x-table.th align="center" class="w-36">Petugas</x-table.th>
                    <x-table.th align="right" class="w-44">Jumlah Pengeluaran</x-table.th>
                    <x-table.th align="center" class="w-20">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($expenditures as $e)
                    <tr class="hover:bg-rose-50/40 transition">
                        <td class="p-3.5 text-xs font-bold text-stone-900 border-r border-stone-200">
                            {{ $e->tanggal ? $e->tanggal->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <x-badge variant="rose" size="xs">
                                {{ $e->kategori->nama ?? 'Umum' }}
                            </x-badge>
                        </td>
                        <td class="p-3.5 text-xs text-stone-700 font-medium border-r border-stone-200">{{ $e->keterangan ?? '-' }}</td>
                        <td class="p-3.5 text-xs font-semibold text-stone-600 text-center border-r border-stone-200">{{ $e->petugas->nama ?? '-' }}</td>
                        <td class="p-3.5 text-xs font-black text-rose-700 text-right border-r border-stone-200">Rp {{ number_format($e->jumlah, 0, ',', '.') }}</td>
                        <td class="p-3.5 text-center">
                            @if (!$e->gajiGuru)
                                <x-button 
                                    type="button" 
                                    variant="danger" 
                                    size="xs" 
                                    icon="trash-2" 
                                    wire:click="deletePengeluaran({{ $e->id }})" 
                                    data-confirm="Hapus catatan pengeluaran kas ini?" 
                                    title="Hapus Pengeluaran"
                                >
                                    Hapus
                                </x-button>
                            @else
                                <span class="text-[10px] text-stone-400 font-mono italic">Gaji Pegawai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="6" title="Tidak ada data pengeluaran" message="Tidak ditemukan transaksi pengeluaran pada kriteria filter terpilih." />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $expenditures->links() }}
        </div>
    </div>

    <!-- 1. Modal Catat Pengeluaran Kas Baru (Manual) -->
    <x-floating-card 
        :show="$showCreateModal" 
        title="Catat Pengeluaran Kas Manual" 
        subtitle="Input transaksi pengeluaran kas operasional baru secara langsung ke pembukuan yayasan."
        badge="INPUT PENGELUARAN"
        badgeVariant="rose"
        icon="wallet-cards"
        maxWidth="max-w-xl"
        closeAction="closeCreateModal"
    >
        <form wire:submit.prevent="savePengeluaran" class="space-y-4 font-sans">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Tanggal Transaksi</label>
                    <input 
                        type="date" 
                        wire:model="createTanggal" 
                        class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-rose-500 shadow-2xs" 
                    />
                    @error('createTanggal') <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Kategori Pos</label>
                    <select 
                        wire:model="createKategoriId" 
                        class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-rose-500 shadow-2xs"
                    >
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                    @error('createKategoriId') <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Amount input with formatted dot separators -->
            <div x-data="{
                val: @entangle('createJumlah'),
                fmt: '',
                format(v) {
                    if (v === null || v === undefined || v === '') return '0';
                    if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                    let s = v.toString().trim();
                    let clean = s.replace(/[^0-9]/g, '');
                    return clean ? Number(clean).toLocaleString('id-ID') : '0';
                },
                onInput(e) {
                    let c = e.target.value.replace(/[^0-9]/g, '');
                    this.val = c ? parseInt(c, 10) : 0;
                    this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                    e.target.value = this.fmt;
                },
                init() {
                    this.fmt = this.format(this.val);
                    this.$watch('val', (v) => { this.fmt = this.format(v); });
                }
            }">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Jumlah Pengeluaran (Rp)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-500 font-bold text-xs">Rp</span>
                    <input 
                        type="text" 
                        inputmode="numeric" 
                        x-model="fmt" 
                        @input="onInput($event)" 
                        placeholder="0"
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-sm font-black text-right focus:ring-2 focus:ring-rose-500 shadow-2xs" 
                    />
                </div>
                @error('createJumlah') <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Keterangan / Rincian Belanja</label>
                <textarea 
                    wire:model="createKeterangan" 
                    rows="3" 
                    placeholder="Contoh: Pembelian perlengkapan ATK kantor, konsumsi rapat bulanan, perbaikan AC gedung..." 
                    class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-rose-500 shadow-2xs"
                ></textarea>
                @error('createKeterangan') <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button type="button" variant="secondary" size="md" wire:click="closeCreateModal">Batal</x-button>
                <x-button type="submit" variant="primary" size="md" loadingTarget="savePengeluaran">
                    Simpan Pengeluaran
                </x-button>
            </div>
        </form>
    </x-floating-card>

    <!-- 2. Modal Buat Laporan Keuangan Manual / Kustom -->
    <x-floating-card 
        :show="$showManualReportModal" 
        title="Buat Laporan Keuangan Manual / Kustom" 
        subtitle="Sesuaikan judul, rentang tanggal, catatan pengantar, dan pejabat penandatangan dokumen."
        badge="LAPORAN MANUAL"
        badgeVariant="emerald"
        icon="file-signature"
        maxWidth="max-w-2xl"
        closeAction="closeManualReportModal"
    >
        <div class="space-y-4 font-sans">
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Judul Laporan Keuangan</label>
                <input 
                    type="text" 
                    wire:model="reportJudul" 
                    class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" 
                    placeholder="LAPORAN PENGELUARAN KEUANGAN YAYASAN"
                />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Tanggal Mulai</label>
                    <input 
                        type="date" 
                        wire:model="reportStartDate" 
                        class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" 
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Tanggal Selesai</label>
                    <input 
                        type="date" 
                        wire:model="reportEndDate" 
                        class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" 
                    />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Filter Kategori (Opsional)</label>
                <select 
                    wire:model="reportKategoriId" 
                    class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs"
                >
                    <option value="">Semua Kategori Pengeluaran</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Catatan / Keterangan Penutup (Opsional)</label>
                <textarea 
                    wire:model="reportCatatan" 
                    rows="2" 
                    placeholder="Contoh: Laporan ini telah diperiksa dan disetujui untuk pertanggungjawaban kas operasional yayasan bulan ini." 
                    class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs"
                ></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Nama Penandatangan</label>
                    <input 
                        type="text" 
                        wire:model="reportPenandatangan" 
                        placeholder="Nama Lengkap Penandatangan" 
                        class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" 
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Jabatan Penandatangan</label>
                    <input 
                        type="text" 
                        wire:model="reportJabatanPenandatangan" 
                        placeholder="Contoh: Bendahara Yayasan / Kepala Sekolah" 
                        class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" 
                    />
                </div>
            </div>

            <!-- Action Buttons inside Modal -->
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200 flex-wrap">
                <x-button variant="secondary" size="md" wire:click="closeManualReportModal">Batal</x-button>

                <a 
                    href="{{ route('finance.export.pengeluaran', array_filter(['start_date' => $reportStartDate, 'end_date' => $reportEndDate, 'kategori_pengeluaran_id' => $reportKategoriId])) }}" 
                    target="_blank"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 border border-emerald-300 rounded-xl text-xs font-bold transition shadow-2xs"
                >
                    <x-lucide-file-spreadsheet class="w-4 h-4 text-emerald-600" />
                    <span>Ekspor Excel (.csv)</span>
                </a>

                <a 
                    href="{{ route('finance.laporan.pengeluaran.pdf', array_filter(['start_date' => $reportStartDate, 'end_date' => $reportEndDate, 'kategori_pengeluaran_id' => $reportKategoriId, 'judul' => $reportJudul, 'catatan' => $reportCatatan, 'penandatangan' => $reportPenandatangan, 'jabatan_penandatangan' => $reportJabatanPenandatangan])) }}" 
                    target="_blank"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold transition shadow-xs"
                >
                    <x-lucide-printer class="w-4 h-4" />
                    <span>Cetak Dokumen PDF</span>
                </a>
            </div>
        </div>
    </x-floating-card>

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
