<!-- TABEL MATRIKS PEMBAYARAN TERPADU (SPP 6 BULAN + KATEGORI TUNGGAKAN LAINNYA PADA SUMBU X) -->
<div class="space-y-4 font-sans">
    <!-- 1. Kontrol Filter & Pencarian (Layout Bersih, Luas & Rapi) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 sm:p-5 shadow-2xs space-y-4">
        <!-- Baris 1: Pencarian, Pilihan Kelas & Switcher Periode SPP -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Search Input & Dropdown Kelas -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
                <div class="w-full sm:w-80">
                    <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama santri atau NIS..." />
                </div>
                <div class="w-full sm:w-48">
                    <select wire:model.live="filterKelas" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelases as $k)
                            <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Periode SPP 6 Bulan Switcher -->
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-bold text-stone-600 hidden sm:inline">Periode SPP:</span>
                <div class="inline-flex items-center gap-1 p-1 bg-stone-100 rounded-xl border border-stone-200 text-xs">
                    <button 
                        type="button" 
                        wire:click="setSppPeriode('ganjil')" 
                        class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer {{ $sppPeriode === 'ganjil' ? 'bg-white text-emerald-800 shadow-2xs font-black' : 'text-stone-600 hover:text-stone-900' }}"
                        title="Tampilkan 6 bulan Semester Ganjil (Juli s/d Desember)"
                    >
                        Semester Ganjil (Juli : Des)
                    </button>
                    <button 
                        type="button" 
                        wire:click="setSppPeriode('genap')" 
                        class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer {{ $sppPeriode === 'genap' ? 'bg-white text-emerald-800 shadow-2xs font-black' : 'text-stone-600 hover:text-stone-900' }}"
                        title="Tampilkan 6 bulan Semester Genap (Januari s/d Juni)"
                    >
                        Semester Genap (Jan : Jun)
                    </button>
                    <button 
                        type="button" 
                        wire:click="setSppPeriode('terakhir')" 
                        class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer {{ $sppPeriode === 'terakhir' ? 'bg-white text-emerald-800 shadow-2xs font-black' : 'text-stone-600 hover:text-stone-900' }}"
                        title="Tampilkan 6 bulan berjalan"
                    >
                        6 Bln Berjalan
                    </button>
                </div>
            </div>
        </div>

        <!-- Baris 2: Filter Status Pelunasan, Metrik & Per-Page -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-stone-100">
            <!-- Filter Status Pelunasan -->
            <div class="inline-flex items-center gap-1.5 p-1 bg-stone-100 rounded-xl border border-stone-200 text-xs">
                <button 
                    type="button" 
                    wire:click="filterByStatusSpp('')" 
                    class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer {{ $filterStatusSpp === '' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}"
                >
                    Semua Santri ({{ $sppStatsSummary['total_siswa'] }})
                </button>
                <button 
                    type="button" 
                    wire:click="filterByStatusSpp('menunggak')" 
                    class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer {{ $filterStatusSpp === 'menunggak' ? 'bg-rose-600 text-white shadow-2xs' : 'text-rose-700 hover:bg-rose-100/60' }}"
                >
                    Ada Tunggakan ({{ $sppStatsSummary['menunggak_count'] }})
                </button>
                <button 
                    type="button" 
                    wire:click="filterByStatusSpp('lunas')" 
                    class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer {{ $filterStatusSpp === 'lunas' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-emerald-700 hover:bg-emerald-100/60' }}"
                >
                    Lunas ({{ $sppStatsSummary['lunas_count'] }})
                </button>
            </div>

            <!-- Metrik & Baris per Halaman -->
            <div class="flex items-center gap-4 text-xs justify-between sm:justify-end">
                <div class="flex items-center gap-2 text-[11px] font-mono">
                    <span class="text-emerald-700 font-bold">Terbayar: Rp {{ number_format($sppStatsSummary['total_dibayar_nominal'], 0, ',', '.') }}</span>
                    <span class="text-stone-300">|</span>
                    <span class="text-rose-700 font-bold">Tunggakan: Rp {{ number_format($sppStatsSummary['total_tunggakan_nominal'], 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-1.5 text-stone-600 font-bold">
                    <span>Tampil:</span>
                    <select wire:model.live="perPageSppMatrix" class="px-2 py-1 bg-white border border-stone-300 rounded-lg text-xs font-bold text-stone-900 focus:ring-1 focus:ring-emerald-600">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Tabel Utama Matriks (Sumbu X = SPP 6 Bulan + Kategori Non-SPP) -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto max-w-full">
            <table class="w-full text-left border-separate border-spacing-0 min-w-[1100px]">
                <!-- Header Bertingkat (Two-Tier Group Header) -->
                <thead>
                    <!-- Tier 1: Group Kategori & Rowspan Columns -->
                    <tr class="text-[11px] uppercase font-black tracking-wider text-white">
                        <!-- No (Rowspan 2, Sticky Left 0px) -->
                        <th rowspan="2" class="p-2.5 w-[48px] min-w-[48px] max-w-[48px] text-center border-b border-r border-emerald-800 sticky left-0 z-30 bg-emerald-950 text-white font-black select-none">
                            No
                        </th>

                        <!-- Identitas Santri (Rowspan 2, Sticky Left 48px) -->
                        <th rowspan="2" class="p-2.5 w-[240px] min-w-[240px] max-w-[240px] border-b border-r-2 border-emerald-800 sticky left-[48px] z-30 bg-emerald-950 text-white shadow-[4px_0_6px_-2px_rgba(0,0,0,0.15)]">
                            <span class="block text-white font-black text-xs">Nama & NIS</span>
                            <span class="text-[9px] font-medium text-emerald-300 block">Serta Kelas / Rombel</span>
                        </th>

                        <!-- SPP Rutin (Colspan 6) -->
                        <th colspan="{{ count($sppMatrixMonths) }}" class="bg-emerald-900 p-2.5 text-center border-b border-r border-emerald-800 text-white">
                            <div class="flex items-center justify-center gap-1.5 text-xs font-black">
                                <x-lucide-calendar class="w-3.5 h-3.5 text-emerald-300" />
                                <span>SPP Rutin (Terbatas 6 Bulan)</span>
                            </div>
                        </th>

                        <!-- Kategori Tunggakan Lainnya (Colspan sesuai jumlah non-SPP) -->
                        @if ($nonSppJenisList->count() > 0)
                            <th colspan="{{ $nonSppJenisList->count() }}" class="bg-emerald-900 p-2.5 text-center border-b border-r border-emerald-800 text-white">
                                <div class="flex items-center justify-center gap-1.5 text-xs font-black">
                                    <x-lucide-layers class="w-3.5 h-3.5 text-emerald-300" />
                                    <span>Kategori Tunggakan Lainnya (Non-SPP)</span>
                                </div>
                            </th>
                        @endif

                        <!-- Total Tunggakan (Rowspan 2) -->
                        <th rowspan="2" class="p-2.5 w-36 text-right border-b border-r border-emerald-800 bg-emerald-950 text-white">
                            <span class="block font-black text-xs text-rose-300">Total Tunggakan</span>
                            <span class="text-[9px] font-normal text-emerald-300 block">Semua Tagihan</span>
                        </th>

                        <!-- Aksi (Rowspan 2) -->
                        <th rowspan="2" class="p-2.5 w-24 text-center border-b border-emerald-800 bg-emerald-950 text-white text-xs font-black">
                            Aksi
                        </th>
                    </tr>

                    <!-- Tier 2: Nama Kolom Detail untuk SPP dan Kategori Non-SPP -->
                    <tr class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-100">
                        <!-- 6 Kolom SPP -->
                        @foreach ($sppMatrixMonths as $m)
                            <th class="p-2 text-center min-w-[110px] border-b border-r border-emerald-800/80 bg-emerald-800 text-emerald-50">
                                <span class="block font-black text-white">SPP {{ $m }}</span>
                                @if (isset($footerSppMatrix[$m]))
                                    <span class="text-[9px] font-normal text-emerald-200 block">
                                        {{ $footerSppMatrix[$m]['lunas_count'] }} Lunas
                                    </span>
                                @endif
                            </th>
                        @endforeach

                        <!-- Kolom per Kategori Non-SPP (Uang Gedung, Seragam, Buku, dll) -->
                        @foreach ($nonSppJenisList as $jt)
                            <th class="p-2 text-center min-w-[125px] border-b border-r border-emerald-800/80 bg-emerald-800 text-emerald-50">
                                <span class="block font-black text-white truncate" title="{{ $jt->nama }}">{{ $jt->nama }}</span>
                                @if (isset($footerNonSppMatrix[$jt->id]))
                                    <span class="text-[9px] font-normal text-emerald-200 block">
                                        {{ $footerNonSppMatrix[$jt->id]['lunas_count'] }} Lunas
                                    </span>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <!-- Body Tabel -->
                <tbody class="bg-white">
                    @forelse ($siswasSppMatrix as $index => $item)
                        <tr class="group hover:bg-emerald-50/40 transition text-xs {{ $index % 2 === 1 ? 'bg-stone-50/50' : 'bg-white' }}">
                            <!-- No (Sticky Left 0px) -->
                            <td class="p-2.5 text-center font-mono font-bold text-stone-600 border-b border-r border-stone-200 sticky left-0 z-20 w-[48px] min-w-[48px] max-w-[48px] {{ $index % 2 === 1 ? 'bg-stone-50' : 'bg-white' }} group-hover:bg-emerald-50">
                                {{ $siswasSppMatrix->firstItem() + $index }}
                            </td>

                            <!-- Identitas Santri (Sticky Left 48px) -->
                            <td class="p-2.5 border-b border-r-2 border-stone-300 sticky left-[48px] z-20 w-[240px] min-w-[240px] max-w-[240px] shadow-[4px_0_6px_-2px_rgba(0,0,0,0.06)] {{ $index % 2 === 1 ? 'bg-stone-50' : 'bg-white' }} group-hover:bg-emerald-50">
                                <div class="font-extrabold text-stone-900 text-xs truncate" title="{{ $item['nama'] }}">{{ $item['nama'] }}</div>
                                <div class="flex items-center gap-1.5 mt-0.5 text-[10px]">
                                    <span class="text-stone-600 font-mono font-medium">NIS: {{ $item['nis'] }}</span>
                                    <span class="text-stone-300">•</span>
                                    <span class="font-bold text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/70">{{ $item['kelas'] }}</span>
                                </div>
                            </td>

                            <!-- 1. Sel Data 6 Bulan SPP -->
                            @foreach ($sppMatrixMonths as $m)
                                @php
                                    $cell = $item['spp_months'][$m];
                                @endphp
                                <td class="p-2 text-center border-b border-r border-stone-200">
                                    @if ($cell['has_tagihan'])
                                        @if ($cell['status'] === 'lunas')
                                            <span 
                                                class="inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-md text-emerald-700 font-semibold text-[11px] bg-emerald-50/70 border border-emerald-200/60"
                                                title="SPP {{ $m }} Lunas Rp {{ number_format($cell['nominal'], 0, ',', '.') }} {{ $cell['terakhir_bayar'] ? 'Tgl ' . $cell['terakhir_bayar'] : '' }}"
                                            >
                                                <x-lucide-check class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                                <span>Lunas</span>
                                            </span>
                                        @elseif ($cell['status'] === 'sebagian')
                                            <a 
                                                href="{{ route('finance.input-pembayaran', ['siswa_id' => $item['id'], 'tagihan_id' => $cell['tagihan_id']]) }}" 
                                                wire:navigate
                                                class="group/cell inline-flex flex-col items-center justify-center px-2 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 hover:border-amber-300 shadow-2xs transition cursor-pointer"
                                                title="Klik untuk bayar cicilan SPP {{ $m }}"
                                            >
                                                <span class="text-[10px] font-black font-mono text-amber-900">Sisa Rp {{ number_format($cell['sisa'], 0, ',', '.') }}</span>
                                                <span class="text-[9px] font-bold text-amber-700 flex items-center gap-0.5 mt-0.5">
                                                    <x-lucide-credit-card class="w-2.5 h-2.5 text-amber-600" />
                                                    <span>Cicil</span>
                                                </span>
                                            </a>
                                        @elseif ($cell['is_mendatang'])
                                            <span class="inline-flex flex-col items-center justify-center px-1.5 py-0.5 rounded text-[10px] text-stone-600 bg-stone-100/80 border border-stone-200/60 font-mono">
                                                <span>Rp {{ number_format($cell['nominal'], 0, ',', '.') }}</span>
                                                <span class="text-[8px] uppercase tracking-wider text-stone-500 font-bold font-sans">Mendatang</span>
                                            </span>
                                        @else
                                            <a 
                                                href="{{ route('finance.input-pembayaran', ['siswa_id' => $item['id'], 'tagihan_id' => $cell['tagihan_id']]) }}" 
                                                wire:navigate
                                                class="group/cell inline-flex flex-col items-center justify-center px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-200 hover:border-rose-300 shadow-2xs transition cursor-pointer"
                                                title="Klik untuk langsung bayar SPP {{ $m }} di Kasir"
                                            >
                                                <span class="text-[10px] font-black font-mono text-rose-700">Rp {{ number_format($cell['nominal'], 0, ',', '.') }}</span>
                                                <span class="text-[9px] font-bold text-rose-600 group-hover/cell:text-rose-800 flex items-center gap-0.5 mt-0.5">
                                                    <x-lucide-credit-card class="w-2.5 h-2.5 text-rose-600" />
                                                    <span>Bayar</span>
                                                </span>
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-stone-400 font-mono font-bold text-xs select-none">-</span>
                                    @endif
                                </td>
                            @endforeach

                            <!-- 2. Sel Data Kategori Non-SPP (Uang Gedung, Seragam, Buku, dll) -->
                            @foreach ($nonSppJenisList as $jt)
                                @php
                                    $nonSppCell = $item['non_spp_by_jenis'][$jt->id] ?? null;
                                @endphp
                                <td class="p-2 text-center border-b border-r border-stone-200">
                                    @if ($nonSppCell && $nonSppCell['has_tagihan'])
                                        @if ($nonSppCell['status'] === 'lunas')
                                            <span 
                                                class="inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-md text-emerald-700 font-semibold text-[11px] bg-emerald-50/70 border border-emerald-200/60"
                                                title="{{ $jt->nama }} Lunas Rp {{ number_format($nonSppCell['nominal'], 0, ',', '.') }} {{ $nonSppCell['terakhir_bayar'] ? 'Tgl ' . $nonSppCell['terakhir_bayar'] : '' }}"
                                            >
                                                <x-lucide-check class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                                <span>Lunas</span>
                                            </span>
                                        @elseif ($nonSppCell['status'] === 'sebagian')
                                            <a 
                                                href="{{ route('finance.input-pembayaran', ['siswa_id' => $item['id'], 'tagihan_id' => $nonSppCell['tagihan_id']]) }}" 
                                                wire:navigate
                                                class="group/cell inline-flex flex-col items-center justify-center px-2 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 hover:border-amber-300 shadow-2xs transition cursor-pointer"
                                                title="Klik untuk bayar cicilan {{ $jt->nama }}"
                                            >
                                                <span class="text-[10px] font-black font-mono text-amber-900">Sisa Rp {{ number_format($nonSppCell['sisa'], 0, ',', '.') }}</span>
                                                <span class="text-[9px] font-bold text-amber-700 flex items-center gap-0.5 mt-0.5">
                                                    <x-lucide-credit-card class="w-2.5 h-2.5 text-amber-600" />
                                                    <span>Cicil</span>
                                                </span>
                                            </a>
                                        @else
                                            <a 
                                                href="{{ route('finance.input-pembayaran', ['siswa_id' => $item['id'], 'tagihan_id' => $nonSppCell['tagihan_id']]) }}" 
                                                wire:navigate
                                                class="group/cell inline-flex flex-col items-center justify-center px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-200 hover:border-rose-300 shadow-2xs transition cursor-pointer"
                                                title="Klik untuk langsung bayar {{ $jt->nama }} di Kasir"
                                            >
                                                <span class="text-[10px] font-black font-mono text-rose-700">Rp {{ number_format($nonSppCell['nominal'], 0, ',', '.') }}</span>
                                                <span class="text-[9px] font-bold text-rose-600 group-hover/cell:text-rose-800 flex items-center gap-0.5 mt-0.5">
                                                    <x-lucide-credit-card class="w-2.5 h-2.5 text-rose-600" />
                                                    <span>Bayar</span>
                                                </span>
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-stone-400 font-mono font-bold text-xs select-none" title="Tidak ada tagihan">-</span>
                                    @endif
                                </td>
                            @endforeach

                            <!-- Total Tunggakan Keseluruhan -->
                            <td class="p-2.5 text-right border-b border-r border-stone-200 font-mono font-black">
                                @if ($item['total_tunggakan'] > 0)
                                    <span class="text-rose-700 text-xs block font-mono font-bold">
                                        Rp {{ number_format($item['total_tunggakan'], 0, ',', '.') }}
                                    </span>
                                    <span class="text-[9px] text-rose-600 font-bold font-sans block mt-0.5">Ada Tunggakan</span>
                                @else
                                    <span class="text-emerald-700 text-xs font-bold font-sans inline-flex items-center gap-1">
                                        <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                        <span>Lunas</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="p-2 text-center border-b border-stone-200">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a 
                                        href="{{ route('finance.input-pembayaran', ['siswa_id' => $item['id']]) }}" 
                                        wire:navigate 
                                        class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-2xs transition cursor-pointer min-h-[38px] min-w-[38px] sm:min-h-0 sm:min-w-0 inline-flex items-center justify-center"
                                        title="Buka Kasir Pembayaran untuk {{ $item['nama'] }}"
                                    >
                                        <x-lucide-credit-card class="w-4 h-4 text-emerald-700" />
                                    </a>
                                    <a 
                                        href="{{ route('finance.tagihan.detail', ['siswaId' => $item['id']]) }}" 
                                        wire:navigate 
                                        class="p-1.5 rounded-lg bg-stone-50 hover:bg-stone-100 text-stone-700 border border-stone-200 shadow-2xs transition cursor-pointer min-h-[38px] min-w-[38px] sm:min-h-0 sm:min-w-0 inline-flex items-center justify-center"
                                        title="Buka Detail Tagihan {{ $item['nama'] }}"
                                    >
                                        <x-lucide-file-text class="w-4 h-4 text-stone-600" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($sppMatrixMonths) + $nonSppJenisList->count() + 2 }}" class="p-8 text-center text-stone-600 border-b border-stone-200">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <x-lucide-inbox class="w-8 h-8 text-stone-400" />
                                    <p class="font-bold text-sm text-stone-800">Tidak ada santri yang sesuai kriteria pencarian.</p>
                                    <p class="text-xs text-stone-600">Coba ubah kata kunci pencarian atau bersihkan filter kelas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <!-- Footer Rekapitulasi Realisasi per Kolom -->
                @if ($siswasSppMatrix->count() > 0)
                    <tfoot class="bg-stone-100 font-extrabold text-xs select-none">
                        <tr>
                            <td colspan="2" class="p-2.5 text-stone-900 border-b border-r-2 border-stone-300 sticky left-0 z-20 bg-stone-100 uppercase tracking-wider text-right font-mono font-bold w-[288px] min-w-[288px] max-w-[288px] shadow-[4px_0_6px_-2px_rgba(0,0,0,0.06)]">
                                Total Realisasi:
                            </td>

                            <!-- 1. Rekapitulasi SPP 6 Bulan -->
                            @foreach ($sppMatrixMonths as $m)
                                @php
                                    $col = $footerSppMatrix[$m] ?? null;
                                @endphp
                                <td class="p-2 text-center border-b border-r border-stone-300 font-mono text-[11px] bg-emerald-50/50">
                                    @if ($col && $col['nominal'] > 0)
                                        <div class="text-emerald-800 font-black">
                                            Rp {{ number_format($col['dibayar'], 0, ',', '.') }}
                                        </div>
                                        @if ($col['sisa'] > 0)
                                            <div class="text-rose-700 font-bold text-[10px] mt-0.5">
                                                Sisa Rp {{ number_format($col['sisa'], 0, ',', '.') }}
                                            </div>
                                        @endif
                                        <div class="text-[9px] text-stone-600 font-medium font-sans mt-0.5">
                                            {{ $col['lunas_count'] }} lunas / {{ $col['belum_count'] }} belum
                                        </div>
                                    @else
                                        <span class="text-stone-400 font-normal">-</span>
                                    @endif
                                </td>
                            @endforeach

                            <!-- 2. Rekapitulasi Kategori Non-SPP -->
                            @foreach ($nonSppJenisList as $jt)
                                @php
                                    $col = $footerNonSppMatrix[$jt->id] ?? null;
                                @endphp
                                <td class="p-2 text-center border-b border-r border-stone-300 font-mono text-[11px] bg-emerald-50/30">
                                    @if ($col && $col['nominal'] > 0)
                                        <div class="text-emerald-800 font-black">
                                            Rp {{ number_format($col['dibayar'], 0, ',', '.') }}
                                        </div>
                                        @if ($col['sisa'] > 0)
                                            <div class="text-rose-700 font-bold text-[10px] mt-0.5">
                                                Sisa Rp {{ number_format($col['sisa'], 0, ',', '.') }}
                                            </div>
                                        @endif
                                        <div class="text-[9px] text-stone-600 font-medium font-sans mt-0.5">
                                            {{ $col['lunas_count'] }} lunas / {{ $col['belum_count'] }} belum
                                        </div>
                                    @else
                                        <span class="text-stone-400 font-normal">-</span>
                                    @endif
                                </td>
                            @endforeach

                            <!-- Grand Total Tunggakan -->
                            <td class="p-2.5 text-right font-mono font-black text-rose-700 text-xs border-b border-r border-stone-300 bg-rose-50/60">
                                Rp {{ number_format($sppStatsSummary['total_tunggakan_nominal'], 0, ',', '.') }}
                            </td>

                            <!-- Aksi Status -->
                            <td class="p-2 text-center text-[10px] font-bold text-stone-600 bg-stone-100 border-b border-stone-300">
                                Terpadu
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <!-- Pagination Links -->
        @if ($siswasSppMatrix->hasPages())
            <div class="p-4 border-t border-stone-200 bg-stone-50/50">
                {{ $siswasSppMatrix->links() }}
            </div>
        @endif
    </div>
</div>
