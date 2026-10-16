<div class="space-y-6 font-sans">
    <!-- Navigation Tabs Menu (Top of Page) -->
    <x-finance.tagihan-nav-tabs active="kasir" />

    <!-- Header Title Bar -->
    <x-page-header 
        title="Input Pembayaran Siswa" 
        subtitle="Pilih dari daftar siswa yang memiliki tagihan aktif di bawah untuk memproses transaksi setoran kasir."
        icon="plus-circle"
    >
        <x-slot:actions>
            <x-button variant="outline" size="sm" icon="file-spreadsheet" wire:click="exportExcel" :disabled="$activeTunggakan->total() === 0" title="{{ $activeTunggakan->total() === 0 ? 'Tidak ada data tagihan untuk diekspor' : 'Ekspor Daftar Tagihan ke Excel (CSV)' }}">
                Ekspor Excel
            </x-button>
            <x-button variant="outline" size="sm" icon="printer" wire:click="exportPdf" :disabled="$activeTunggakan->total() === 0" title="{{ $activeTunggakan->total() === 0 ? 'Tidak ada data tagihan untuk dicetak' : 'Cetak Dokumen PDF Daftar Tagihan' }}">
                Cetak PDF
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Kasir & Pencatatan Pembayaran Siswa"
        :steps="[
            [
                'title' => '1. Cari & Pilih Siswa', 
                'desc' => 'Gunakan kolom pencarian nama/NIS atau filter kelas pada tabel di bawah, kemudian klik tombol Bayar Sekarang pada baris siswa yang bersangkutan.'
            ],
            [
                'title' => '2. Pilih Tagihan yang Dibayar', 
                'desc' => 'Centang tagihan (SPP bulanan, Uang Gedung, Seragam, dll.) yang ingin dilunasi. Anda dapat mencentang satu atau beberapa tagihan sekaligus.'
            ],
            [
                'title' => '3. Pilih Metode Pembayaran', 
                'desc' => 'Pilih metode bayar: Tunai, Transfer Bank, E-Wallet, Saldo Deposit Tabungan, atau Beasiswa. Masukkan nominal uang yang disetorkan.'
            ],
            [
                'title' => '4. Alokasi Kelebihan Setoran (Deposit)', 
                'desc' => 'Jika Siswa membayar lebih dari jumlah tagihan yang dicentang, selisih uang otomatis disimpan menjadi Saldo Deposit Tabungan untuk tagihan berikutnya.'
            ],
            [
                'title' => '5. Cetak Kuitansi & Resi Sah', 
                'desc' => 'Setelah transaksi berhasil disimpan, tombol Cetak Resi akan muncul untuk mencetak kuitansi resmi lengkap dengan rincian pelunasan, barcode/QR, dan stempel.'
            ],
            [
                'title' => '6. Pembatalan Transaksi Pembayaran', 
                'desc' => 'Jika terjadi salah input, transaksi pembayaran dapat dibatalkan atau dihapus melalui menu Rincian Tagihan Siswa di tabel Riwayat Pembayaran.'
            ]
        ]"
        notes="Pembayaran dengan metode Saldo Deposit akan langsung memotong saldo tabungan siswa. Pastikan saldo deposit siswa mencukupi sebelum memproses transaksi."
    />

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl space-y-3 shadow-2xs">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-600 text-white rounded-xl shrink-0">
                        <x-lucide-check-circle class="w-5 h-5" />
                    </div>
                    <div>
                        <span class="text-xs font-bold text-emerald-900 block">{{ session('message') }}</span>
                        <span class="text-[11px] text-emerald-700">Setoran pembayaran telah berhasil dicatat ke dalam database keuangan.</span>
                    </div>
                </div>

                @if (!empty($createdPembayaranList) && count($createdPembayaranList) === 1)
                    <x-button variant="primary" size="sm" icon="printer" href="{{ route('finance.cetak-resi', $createdPembayaranList[0]['id']) }}" target="_blank">
                        Cetak Resi Bukti Bayar
                    </x-button>
                @elseif ($lastPembayaranId && empty($createdPembayaranList))
                    <x-button variant="primary" size="sm" icon="printer" href="{{ route('finance.cetak-resi', $lastPembayaranId) }}" target="_blank">
                        Cetak Resi Bukti Bayar
                    </x-button>
                @endif
            </div>

            @if (!empty($createdPembayaranList) && count($createdPembayaranList) > 1)
                <div class="pt-2 border-t border-emerald-200/80 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-emerald-900">Cetak Kuitansi / Resi:</span>
                    @foreach ($createdPembayaranList as $item)
                        <a href="{{ route('finance.cetak-resi', $item['id']) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-2xs transition">
                            <x-lucide-printer class="w-3.5 h-3.5" />
                            <span>{{ $item['jenis'] }} ({{ $item['no_resi'] }})</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- MAIN TABLE: DAFTAR TUNGGAKAN SISWA -->
    <div class="bg-white border border-stone-200 rounded-2xl p-3.5 sm:p-6 shadow-xs space-y-4">
        <!-- FILTER CONTROLS (PREMIUM, INTUITIVE & STREAMLINED) -->
        <div class="space-y-3 pb-3 border-b border-stone-100">
            <!-- Row 1: Quick Waktu / Status Segmented Tabs & Summary -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <!-- Waktu / Status Pills -->
                <div class="flex items-center gap-1.5 p-1 bg-stone-100/80 rounded-xl overflow-x-auto select-none">
                    <button type="button" 
                            wire:click="$set('filterWaktu', '')" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer whitespace-nowrap {{ $filterWaktu === '' ? 'bg-white text-stone-900 shadow-xs font-extrabold' : 'text-stone-600 hover:text-stone-900' }}">
                        Semua Tunggakan
                    </button>
                    <button type="button" 
                            wire:click="$set('filterWaktu', 'lewat')" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 {{ $filterWaktu === 'lewat' ? 'bg-rose-600 text-white shadow-xs font-extrabold' : 'text-rose-700 hover:bg-rose-50' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $filterWaktu === 'lewat' ? 'bg-white' : 'bg-rose-500' }}"></span>
                        <span>Lewat Jatuh Tempo</span>
                    </button>
                    <button type="button" 
                            wire:click="$set('filterWaktu', 'bulan_ini')" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 {{ $filterWaktu === 'bulan_ini' ? 'bg-emerald-600 text-white shadow-xs font-extrabold' : 'text-emerald-700 hover:bg-emerald-50' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $filterWaktu === 'bulan_ini' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                        <span>Bulan Ini</span>
                    </button>
                    <button type="button" 
                            wire:click="$set('filterWaktu', 'mendatang')" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 {{ $filterWaktu === 'mendatang' ? 'bg-amber-600 text-white shadow-xs font-extrabold' : 'text-amber-700 hover:bg-amber-50' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $filterWaktu === 'mendatang' ? 'bg-white' : 'bg-amber-500' }}"></span>
                        <span>Mendatang (Pra-Bayar)</span>
                    </button>
                    <button type="button" 
                            wire:click="$set('filterWaktu', 'semua')" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer whitespace-nowrap {{ $filterWaktu === 'semua' ? 'bg-white text-stone-900 shadow-xs font-extrabold' : 'text-stone-600 hover:text-stone-900' }}">
                        Semua Waktu
                    </button>
                </div>

                <!-- Tagihan Counter & Quick Export Actions -->
                <div class="flex flex-wrap items-center gap-2.5 self-end sm:self-auto shrink-0">
                    <span class="text-xs text-stone-500 font-medium">
                        Menampilkan <strong class="text-stone-900 font-extrabold">{{ $activeTunggakan->total() }}</strong> tagihan
                    </span>

                    <div class="h-4 w-px bg-stone-200 hidden sm:block"></div>

                    <div class="flex items-center gap-1.5">
                        <button type="button" 
                                wire:click="exportExcel" 
                                wire:loading.attr="disabled"
                                @if($activeTunggakan->total() === 0) disabled @endif
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200/80 rounded-lg text-xs font-bold transition shadow-2xs cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed" 
                                title="Ekspor Daftar Tagihan ke Excel (CSV)">
                            <x-lucide-file-spreadsheet class="w-3.5 h-3.5 text-emerald-600" />
                            <span>Excel</span>
                        </button>
                        <button type="button" 
                                wire:click="exportPdf" 
                                wire:loading.attr="disabled"
                                @if($activeTunggakan->total() === 0) disabled @endif
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200/80 rounded-lg text-xs font-bold transition shadow-2xs cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed" 
                                title="Cetak / Unduh Dokumen PDF">
                            <x-lucide-printer class="w-3.5 h-3.5 text-rose-600" />
                            <span>PDF</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Row 2: Search, Kelas, Jenis Tagihan, Periode Dropdowns with icons -->
            <div class="flex flex-col md:flex-row flex-wrap items-stretch md:items-center gap-2.5">
                <!-- Search Input with flex-1 -->
                <div class="flex-1 min-w-[240px]">
                    <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama siswa atau NIS..." />
                </div>

                <!-- Filter Kelas -->
                <div class="w-full sm:w-auto min-w-[150px]">
                    <div class="relative">
                        <select wire:model.live="filterKelas" class="w-full pl-3.5 pr-8 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-800 text-xs font-semibold focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 transition shadow-2xs appearance-none cursor-pointer">
                            <option value="">Semua Kelas</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                            @endforeach
                        </select>
                        <x-lucide-chevron-down class="w-4 h-4 text-stone-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    </div>
                </div>

                <!-- Filter Jenis Tagihan -->
                <div class="w-full sm:w-auto min-w-[170px]">
                    <div class="relative">
                        <select wire:model.live="filterJenisTagihan" class="w-full pl-3.5 pr-8 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-800 text-xs font-semibold focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 transition shadow-2xs appearance-none cursor-pointer">
                            <option value="">Semua Jenis Tagihan</option>
                            @foreach ($jenisTagihanOptions as $jt)
                                <option value="{{ $jt->id }}">{{ $jt->nama }}</option>
                            @endforeach
                        </select>
                        <x-lucide-chevron-down class="w-4 h-4 text-stone-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    </div>
                </div>

                <!-- Filter Periode (Bulan) -->
                <div class="w-full sm:w-auto min-w-[150px]">
                    <div class="relative">
                        <select wire:model.live="filterPeriode" class="w-full pl-3.5 pr-8 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-800 text-xs font-semibold focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 transition shadow-2xs appearance-none cursor-pointer">
                            <option value="">Semua Periode</option>
                            @foreach ($standardMonths as $bln)
                                <option value="{{ $bln }}">Bulan {{ $bln }}</option>
                            @endforeach
                            <option value="non_bulanan">Non-Bulanan (Bebas)</option>
                        </select>
                        <x-lucide-calendar class="w-4 h-4 text-stone-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    </div>
                </div>

                <!-- Reset Filter Button -->
                @if ($this->activeFilterCount > 0)
                    <button type="button" wire:click="resetTableFilters" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-stone-300 bg-stone-50 hover:bg-stone-100 text-stone-700 text-xs font-bold transition shadow-2xs cursor-pointer shrink-0" title="Reset Semua Filter">
                        <x-lucide-rotate-ccw class="w-3.5 h-3.5 text-stone-500" />
                        <span>Reset ({{ $this->activeFilterCount }})</span>
                    </button>
                @endif
            </div>
        </div>

        <x-table loadingTarget="search, filterKelas, filterJenisTagihan, filterPeriode, filterWaktu, page">
            <thead class="bg-stone-100/90 text-stone-700 font-extrabold uppercase tracking-wider text-[11px] border-b border-stone-200">
                <tr>
                    <x-table.th class="min-w-[180px]">Siswa & Kelas</x-table.th>
                    <x-table.th class="w-44">Jenis Tagihan</x-table.th>
                    <x-table.th align="center" class="w-40">Periode & Jatuh Tempo</x-table.th>
                    <x-table.th align="right" class="w-36">Nominal</x-table.th>
                    <x-table.th align="right" class="w-36 text-rose-700">Sisa Tagihan</x-table.th>
                    <x-table.th align="center" class="w-32">Status</x-table.th>
                    <x-table.th align="center" class="w-36">Aksi Kasir</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100 bg-white">
                @forelse ($activeTunggakan as $t)
                    @php
                        $sisa = floatval($t->nominal - $t->total_dibayar);
                        $isSelected = $selectedInvoiceInfo && $selectedInvoiceInfo['id'] === $t->id;
                        $isOverdue = $t->jatuh_tempo && $t->jatuh_tempo->format('Y-m-d') < date('Y-m-d');
                    @endphp
                    <tr class="hover:bg-emerald-50/40 transition-colors {{ $isSelected ? 'bg-emerald-50/80 font-bold' : '' }}">
                        <td class="p-3.5">
                            <span class="font-extrabold text-stone-900 text-xs block">{{ $t->siswa->user->nama ?? '-' }}</span>
                            <span class="text-[10px] text-stone-500 font-mono">NIS: {{ $t->siswa->nis ?? '-' }} | Kelas {{ $t->siswa->kelas->nama_kelas ?? '-' }}</span>
                        </td>
                        <td class="p-3.5 text-xs font-bold text-stone-900">
                            {{ $t->jenisTagihan->nama ?? '-' }}
                        </td>
                        <td class="p-3.5 text-center">
                            <span class="text-xs text-stone-800 font-bold block">{{ $t->bulan ?: '-' }}</span>
                            @if ($t->jatuh_tempo)
                                <div class="inline-flex items-center gap-1 text-[10px] mt-0.5 {{ $isOverdue ? 'text-rose-600 font-bold' : 'text-stone-400 font-mono' }}">
                                    @if ($isOverdue)
                                        <x-lucide-alert-circle class="w-3 h-3 text-rose-500" />
                                    @endif
                                    <span>JT: {{ $t->jatuh_tempo->format('d/m/Y') }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="p-3.5 text-right font-bold text-xs text-stone-700">
                            Rp {{ number_format($t->nominal, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-right font-black text-rose-700 text-xs">
                            Rp {{ number_format($sisa, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-center">
                            @if ($t->status === 'belum_bayar')
                                <x-badge variant="rose" size="xs" :dot="true">Belum Bayar</x-badge>
                            @else
                                <x-badge variant="amber" size="xs" :dot="true">Sebagian</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            @if(!auth()->user()->isSuperAdmin2())
                            <x-button variant="primary" size="xs" icon="plus" wire:click="pilihSiswaAndTagihan({{ $t->siswa_id }}, {{ $t->id }})">
                                Bayar Sekarang
                            </x-button>
                            @else
                                <span class="text-[10px] text-stone-400 font-mono italic">Lihat Saja</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="7" title="Tidak ada tagihan tertunggak" message="Seluruh siswa telah melunasi tagihan yang dirilis." />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $activeTunggakan->links() }}
        </div>
    </div>

    <!-- FLOATING CARD INPUT MODAL: KASIR PEMBAYARAN -->
    @if ($selectedInvoiceInfo)
        <x-floating-card 
            :show="true" 
            title="Form Setoran Kasir Pembayaran" 
            :subtitle="$selectedInvoiceInfo['siswa_nama'] . ' (NIS: ' . $selectedInvoiceInfo['siswa_nis'] . ' : Kelas ' . $selectedInvoiceInfo['siswa_kelas'] . ')'"
            badge="KASIR SETORAN"
            badgeVariant="emerald"
            icon="plus-circle"
            maxWidth="max-w-xl"
            closeAction="resetSelection"
        >
            <!-- Invoice Summary Metrics in Card -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                <div class="p-2.5 bg-stone-50 border border-stone-200 rounded-xl text-center">
                    <span class="text-[9px] font-bold text-stone-400 uppercase tracking-wider block">Tagihan Terpilih</span>
                    <span class="text-xs font-bold text-stone-800 block mt-0.5 truncate" title="{{ $selectedInvoiceInfo['jenis'] }}">{{ $selectedInvoiceInfo['jenis'] }}</span>
                </div>
                <div class="p-2.5 bg-stone-50 border border-stone-200 rounded-xl text-center">
                    <span class="text-[9px] font-bold text-stone-400 uppercase tracking-wider block">Total Tagihan</span>
                    <span class="text-xs font-bold text-stone-800 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['nominal'], 0, ',', '.') }}</span>
                </div>
                <div class="p-2.5 bg-stone-50 border border-stone-200 rounded-xl text-center">
                    <span class="text-[9px] font-bold text-stone-400 uppercase tracking-wider block">Telah Dibayar</span>
                    <span class="text-xs font-bold text-emerald-700 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['total_dibayar'], 0, ',', '.') }}</span>
                </div>
                <div class="p-2.5 bg-rose-50 border border-rose-200 rounded-xl text-center">
                    <span class="text-[9px] font-bold text-rose-600 uppercase tracking-wider block">Sisa Tunggakan</span>
                    <span class="text-xs font-black text-rose-800 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['sisa'], 0, ',', '.') }}</span>
                </div>
            </div>

            @if ($siswaDeposit > 0)
                <div class="px-3.5 py-2 bg-emerald-50 border border-emerald-300 rounded-xl flex items-center justify-between text-xs shadow-2xs">
                    <div class="flex items-center gap-2">
                        <x-lucide-wallet class="w-4 h-4 text-emerald-600" />
                        <span class="text-stone-700 font-bold">Saldo Deposit Tersedia:</span>
                    </div>
                    <span class="font-black text-emerald-800">Rp {{ number_format($siswaDeposit, 0, ',', '.') }}</span>
                </div>
            @endif

            @if (!empty($siswaUnpaidInvoices) && count($siswaUnpaidInvoices) > 1)
                <!-- Selector Seluruh Tagihan Belum Lunas Siswa Ini (Multi-Pilih) -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                            Pilih Tagihan yang Ingin Dibayar:
                        </label>
                        <div class="flex items-center gap-1.5">
                            <button 
                                type="button" 
                                wire:click="selectAllTagihans" 
                                class="text-[10px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2 py-0.5 rounded border border-emerald-200 transition cursor-pointer"
                            >
                                Pilih Semua ({{ count($siswaUnpaidInvoices) }})
                            </button>
                            <button 
                                type="button" 
                                wire:click="deselectAllTagihans" 
                                class="text-[10px] font-bold text-stone-600 bg-stone-100 hover:bg-stone-200 px-2 py-0.5 rounded border border-stone-200 transition cursor-pointer"
                            >
                                Kosongkan
                            </button>
                        </div>
                    </div>

                    <div class="max-h-48 overflow-y-auto space-y-1.5 p-1.5 bg-stone-50 border border-stone-200 rounded-xl">
                        @foreach ($siswaUnpaidInvoices as $ui)
                            @php $isChecked = in_array($ui['id'], $selected_tagihan_ids); @endphp
                            <div 
                                wire:click="toggleTagihan({{ $ui['id'] }})"
                                class="w-full text-left p-2.5 rounded-lg border transition flex items-center justify-between gap-3 cursor-pointer select-none {{ $isChecked ? 'bg-emerald-50/90 border-emerald-500 ring-2 ring-emerald-500/20 shadow-2xs font-bold text-emerald-950' : 'bg-white border-stone-200 hover:bg-stone-100/80 text-stone-700' }}"
                            >
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-4 h-4 rounded border flex items-center justify-center shrink-0 transition {{ $isChecked ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-stone-300 bg-white' }}">
                                        @if ($isChecked)
                                            <x-lucide-check class="w-3 h-3 text-white stroke-[3]" />
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-xs font-extrabold truncate text-stone-900">{{ $ui['jenis'] }}</span>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded font-semibold {{ $ui['is_spp'] ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">{{ $ui['bulan'] }}</span>
                                        </div>
                                        <span class="text-[10px] text-stone-400 block mt-0.5">Jatuh Tempo: {{ $ui['jatuh_tempo'] }}</span>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-xs font-black text-rose-700 block">Rp {{ number_format($ui['sisa'], 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-stone-400 block">dari Rp {{ number_format($ui['nominal'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if (count($selected_tagihan_ids) > 1)
                        <div class="p-2 bg-indigo-50/70 border border-indigo-200 rounded-lg flex items-center justify-between text-xs">
                            <span class="text-indigo-900 font-bold flex items-center gap-1.5">
                                <x-lucide-check-circle-2 class="w-4 h-4 text-indigo-600" />
                                {{ count($selected_tagihan_ids) }} Tagihan Terpilih Sekaligus
                            </span>
                            <span class="font-black text-indigo-950">Total: Rp {{ number_format($selectedInvoiceInfo['sisa'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @error('selected_tagihan_ids') <span class="text-rose-600 text-[11px] font-bold block">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- Form Inputs -->
            <form wire:submit.prevent="savePayment" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nominal Bayar -->
                    <x-input-currency 
                        label="Nominal Bayar (Rp)" 
                        name="nominal_dibayar" 
                        wire:model="nominal_dibayar" 
                        placeholder="Contoh: 350.000" 
                        hint="Kelebihan nominal akan otomatis masuk ke Deposit Siswa."
                        required 
                    />

                    <!-- Tanggal Bayar -->
                    <x-input 
                        type="date" 
                        label="Tanggal Bayar" 
                        name="tanggal_bayar" 
                        wire:model="tanggal_bayar" 
                        required 
                    />
                </div>

                <!-- Metode Bayar -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        @foreach (['Tunai', 'Transfer Bank', 'E-Wallet', 'Deposit'] as $method)
                            <button type="button" 
                                wire:click="setMetodeBayar('{{ $method }}')"
                                class="flex items-center justify-center gap-1.5 p-2.5 border rounded-xl text-xs select-none transition duration-150
                                {{ $metode_bayar === $method ? 'border-emerald-500 text-emerald-900 bg-emerald-50 font-black shadow-2xs ring-2 ring-emerald-500/20' : 'border-stone-300 text-stone-600 bg-white hover:bg-stone-50 hover:border-stone-400 font-bold' }}
                            ">
                                @if($method === 'Tunai') <x-lucide-banknote class="w-3.5 h-3.5 text-emerald-600" />
                                @elseif($method === 'Transfer Bank') <x-lucide-building-2 class="w-3.5 h-3.5 text-blue-600" />
                                @elseif($method === 'E-Wallet') <x-lucide-smartphone class="w-3.5 h-3.5 text-purple-600" />
                                @elseif($method === 'Deposit') <x-lucide-wallet class="w-3.5 h-3.5 text-amber-600" />
                                @endif
                                <span>{{ $method }}</span>
                            </button>
                        @endforeach
                    </div>
                    @error('metode_bayar') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Upload Bukti Pembayaran (Foto Struk / Bukti Transfer) -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                            Bukti Pembayaran (Struk / Transfer)
                        </label>
                        <span class="text-[10px] text-stone-400 font-semibold">Opsional • Maks. 2MB • Foto/Gambar</span>
                    </div>

                    @if ($bukti_foto)
                        <div class="p-3 bg-emerald-50/70 border border-emerald-300 rounded-xl flex items-center justify-between gap-3 shadow-2xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ $bukti_foto->temporaryUrl() }}" alt="Preview Bukti" class="w-14 h-14 object-cover rounded-lg border border-emerald-200 shadow-2xs shrink-0" />
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-emerald-950 block truncate">{{ $bukti_foto->getClientOriginalName() }}</span>
                                    <span class="text-[11px] text-emerald-700 font-semibold">{{ number_format($bukti_foto->getSize() / 1024, 1) }} KB • Siap Disimpan</span>
                                </div>
                            </div>
                            <button type="button" wire:click="removeBuktiFoto" class="px-2.5 py-1.5 text-xs font-bold text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 rounded-lg shadow-2xs transition shrink-0 cursor-pointer">
                                Ganti / Hapus
                            </button>
                        </div>
                    @else
                        <div class="relative border-2 border-dashed border-stone-300 hover:border-emerald-500 rounded-xl p-4 bg-stone-50/60 hover:bg-emerald-50/20 text-center transition group cursor-pointer">
                            <input 
                                type="file" 
                                wire:model="bukti_foto" 
                                accept="image/jpeg,image/png,image/jpg,image/webp" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                            />
                            <div class="flex flex-col items-center justify-center gap-1.5 pointer-events-none">
                                <div class="w-9 h-9 rounded-xl bg-white border border-stone-200 group-hover:border-emerald-300 text-stone-500 group-hover:text-emerald-600 flex items-center justify-center shadow-2xs transition">
                                    <x-lucide-camera class="w-5 h-5" />
                                </div>
                                <div class="text-xs font-bold text-stone-700 group-hover:text-emerald-900">
                                    Klik atau seret foto bukti transfer / struk kasir
                                </div>
                                <div class="text-[10px] text-stone-400 font-medium">
                                    Hanya file gambar (JPG, JPEG, PNG, WEBP) maks 2MB
                                </div>
                            </div>

                            <div wire:loading wire:target="bukti_foto" class="absolute inset-0 bg-white/90 backdrop-blur-xs rounded-xl flex items-center justify-center z-20">
                                <div class="flex items-center gap-2 text-xs font-bold text-emerald-800">
                                    <svg class="animate-spin h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Mengunggah foto...</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    @error('bukti_foto') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-stone-200">
                    <x-button variant="secondary" size="md" wire:click="resetSelection">
                        Batal
                    </x-button>
                    <x-button variant="primary" size="md" type="submit" loadingTarget="savePayment">
                        Simpan Setoran Kasir
                    </x-button>
                </div>
            </form>
        </x-floating-card>
    @endif
</div>
