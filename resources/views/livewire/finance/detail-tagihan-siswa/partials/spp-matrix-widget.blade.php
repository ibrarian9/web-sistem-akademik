<!-- WIDGET MATRIKS TAGIHAN SISWA -->
<div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-4 font-sans">
    <!-- Header Control Bar: Title, Period Switcher & View Switcher -->
    <div class="flex items-center justify-between flex-wrap gap-4 border-b border-stone-100 pb-4">
        <div class="flex items-center gap-2.5">
            <div class="p-2.5 bg-emerald-100 text-emerald-800 rounded-xl border border-emerald-200 shadow-2xs">
                <x-lucide-grid class="w-4 h-4" />
            </div>
            <div>
                <h3 class="text-xs font-black text-stone-900 uppercase tracking-tight">Matriks Tagihan dan Status SPP Siswa</h3>
                <p class="text-[11px] text-stone-600 font-medium">T.A. {{ $activeTAName }} : Matriks Status SPP 12 Bulan dan rincian seluruh kategori pembayaran per bulan dalam satu tabel komprehensif.</p>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <!-- Period Switcher SPP 6 Bulan -->
            <div class="flex items-center gap-1.5 p-1 bg-stone-100 border border-stone-200 rounded-xl shadow-2xs">
                <button 
                    type="button" 
                    wire:click="setSppPeriode('ganjil')" 
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer {{ $sppPeriode === 'ganjil' ? 'bg-white text-emerald-800 shadow-xs border border-stone-200/80 font-black' : 'text-stone-600 hover:text-stone-900 hover:bg-white/60' }}"
                    title="Tampilkan 6 bulan Semester Ganjil (Juli : Des)"
                >
                    Semester Ganjil (Juli : Des)
                </button>
                <button 
                    type="button" 
                    wire:click="setSppPeriode('genap')" 
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer {{ $sppPeriode === 'genap' ? 'bg-white text-emerald-800 shadow-xs border border-stone-200/80 font-black' : 'text-stone-600 hover:text-stone-900 hover:bg-white/60' }}"
                    title="Tampilkan 6 bulan Semester Genap (Jan : Jun)"
                >
                    Semester Genap (Jan : Jun)
                </button>
                <button 
                    type="button" 
                    wire:click="setSppPeriode('terakhir')" 
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer {{ $sppPeriode === 'terakhir' ? 'bg-white text-emerald-800 shadow-xs border border-stone-200/80 font-black' : 'text-stone-600 hover:text-stone-900 hover:bg-white/60' }}"
                    title="Tampilkan 6 bulan berjalan"
                >
                    6 Bln Berjalan
                </button>
            </div>

            <!-- View Mode Switcher -->
            <div class="flex items-center gap-1.5 p-1 bg-stone-100 border border-stone-200 rounded-xl shadow-2xs">
                <button 
                    type="button" 
                    wire:click="setMatrixViewStyle('table')" 
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer flex items-center gap-1.5 {{ $matrixViewStyle === 'table' ? 'bg-white text-emerald-800 shadow-xs border border-stone-200/80 font-black' : 'text-stone-600 hover:text-stone-900 hover:bg-white/60' }}"
                >
                    <x-lucide-table class="w-3.5 h-3.5" />
                    <span>Tabel Matriks Lengkap</span>
                </button>
                <button 
                    type="button" 
                    wire:click="setMatrixViewStyle('cards')" 
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer flex items-center gap-1.5 {{ $matrixViewStyle === 'cards' ? 'bg-white text-emerald-800 shadow-xs border border-stone-200/80 font-black' : 'text-stone-600 hover:text-stone-900 hover:bg-white/60' }}"
                >
                    <x-lucide-layout-grid class="w-3.5 h-3.5" />
                    <span>Kartu Ringkas SPP</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Status Legend -->
    <div class="flex items-center gap-3 text-[11px] font-bold flex-wrap bg-stone-50 p-2.5 rounded-xl border border-stone-200">
        <span class="text-stone-500 uppercase text-[10px] tracking-wider mr-1">Keterangan:</span>
        <span class="inline-flex items-center gap-1 text-emerald-700"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Lunas</span>
        <span class="inline-flex items-center gap-1 text-amber-700"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Dicicil</span>
        <span class="inline-flex items-center gap-1 text-rose-700"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Tunggakan</span>
        <span class="inline-flex items-center gap-1 text-sky-700"><span class="w-2 h-2 rounded-full bg-sky-400"></span> Mendatang</span>
        <span class="inline-flex items-center gap-1 text-stone-400"><span class="w-2 h-2 rounded-full bg-stone-300"></span> Belum Ada Tagihan</span>
    </div>

    @if ($matrixViewStyle === 'table')
        <!-- TABEL MATRIKS TERPADU (SUMBU X: SPP 6 BULAN + KATEGORI NON-SPP) -->
        <div class="overflow-x-auto rounded-2xl border border-stone-200 shadow-xs bg-white">
            <table class="w-full text-left border-separate border-spacing-0 min-w-[1000px]">
                <!-- Header Bertingkat (Two-Tier Unified Emerald Header) -->
                <thead>
                    <!-- Tier 1: Group Kategori -->
                    <tr class="text-[11px] uppercase font-black tracking-wider text-white">
                        <!-- Metrik Keuangan (Rowspan 2, Sticky Left 0px) -->
                        <th rowspan="2" class="p-3 w-[200px] min-w-[200px] max-w-[200px] border-b border-r-2 border-emerald-800 sticky left-0 z-30 bg-emerald-950 text-white shadow-[4px_0_6px_-2px_rgba(0,0,0,0.15)]">
                            <span class="block text-white font-black text-xs">Metrik Keuangan</span>
                            <span class="text-[9px] font-medium text-emerald-300 block">Rincian Tagihan Santri</span>
                        </th>

                        <!-- SPP Rutin (Colspan 6 Bulan) -->
                        <th colspan="{{ count($sppMatrixMonths) }}" class="bg-emerald-900 p-2.5 text-center border-b border-r border-emerald-800 text-white">
                            <div class="flex items-center justify-center gap-1.5 text-xs font-black">
                                <x-lucide-calendar class="w-3.5 h-3.5 text-emerald-300" />
                                <span>SPP Rutin (Terbatas 6 Bulan)</span>
                            </div>
                        </th>

                        <!-- Kategori Tunggakan Lainnya (Colspan Non-SPP) -->
                        @if ($nonSppJenisList->count() > 0)
                            <th colspan="{{ $nonSppJenisList->count() }}" class="bg-emerald-900 p-2.5 text-center border-b border-r border-emerald-800 text-white">
                                <div class="flex items-center justify-center gap-1.5 text-xs font-black">
                                    <x-lucide-layers class="w-3.5 h-3.5 text-emerald-300" />
                                    <span>Kategori Tagihan Lainnya (Non-SPP)</span>
                                </div>
                            </th>
                        @endif

                        <!-- Total SPP (Rowspan 2) -->
                        <th rowspan="2" class="p-2.5 w-32 text-right border-b border-r border-emerald-800 bg-emerald-950 text-white">
                            <span class="block font-black text-xs text-emerald-300">Total SPP</span>
                            <span class="text-[9px] font-normal text-emerald-400 block">(6 Bulan)</span>
                        </th>

                        <!-- Total Non-SPP (Rowspan 2) -->
                        <th rowspan="2" class="p-2.5 w-32 text-right border-b border-r border-emerald-800 bg-emerald-950 text-white">
                            <span class="block font-black text-xs text-emerald-300">Total Non-SPP</span>
                            <span class="text-[9px] font-normal text-emerald-400 block">(Kategori Lain)</span>
                        </th>

                        <!-- Grand Total (Rowspan 2) -->
                        <th rowspan="2" class="p-2.5 w-36 text-right border-b border-emerald-800 bg-emerald-950 text-white">
                            <span class="block font-black text-xs text-amber-300">Grand Total</span>
                            <span class="text-[9px] font-normal text-emerald-300 block">Semua Tagihan</span>
                        </th>
                    </tr>

                    <!-- Tier 2: Nama Kolom Detail SPP & Non-SPP -->
                    <tr class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-100">
                        <!-- 6 Kolom SPP -->
                        @foreach ($sppMatrixMonths as $m)
                            <th class="p-2 text-center min-w-[125px] border-b border-r border-emerald-800/80 bg-emerald-800 text-emerald-50">
                                <span class="block font-black text-white">SPP {{ $m }}</span>
                                <span class="text-[9px] font-normal text-emerald-200 block">Rutin Bulanan</span>
                            </th>
                        @endforeach

                        <!-- Kolom per Kategori Non-SPP (Uang Gedung, Seragam, dll) -->
                        @foreach ($nonSppJenisList as $jt)
                            <th class="p-2 text-center min-w-[135px] border-b border-r border-emerald-800/80 bg-emerald-800 text-emerald-50">
                                <span class="block font-black text-white truncate" title="{{ $jt->nama }}">{{ $jt->nama }}</span>
                                <span class="text-[9px] font-normal text-emerald-200 block capitalize">({{ $jt->kategori }})</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <!-- Body: 5 Baris Metrik Keuangan Santri -->
                <tbody class="bg-white text-xs">
                    <!-- BARIS 1: STATUS REALISASI DAN AKSI KASIR -->
                    <tr class="hover:bg-emerald-50/30 transition">
                        <!-- Left Sticky Header -->
                        <td class="p-3 border-b border-r-2 border-stone-300 sticky left-0 z-20 bg-stone-100 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.06)]">
                            <div class="flex items-center gap-2">
                                <x-lucide-activity class="w-4 h-4 text-emerald-700 shrink-0" />
                                <div>
                                    <span class="block text-xs font-black text-stone-900">Status dan Aksi Kasir</span>
                                    <span class="text-[10px] text-stone-500 font-medium">Realisasi & Pembayaran</span>
                                </div>
                            </div>
                        </td>

                        <!-- 6 Kolom SPP: Status & Aksi -->
                        @foreach ($sppMatrixMonths as $m)
                            @php
                                $cell = $spp6MonthsData[$m];
                            @endphp
                            <td class="p-2 text-center border-b border-r border-stone-200">
                                @if ($cell['has_tagihan'])
                                    @if ($cell['status'] === 'lunas')
                                        <div 
                                            class="inline-flex flex-col items-center justify-center p-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs w-full"
                                            title="SPP {{ $m }} Lunas (Rp {{ number_format($cell['nominal'], 0, ',', '.') }}) {{ $cell['terakhir_bayar'] ? 'Tgl ' . $cell['terakhir_bayar'] : '' }}"
                                        >
                                            <span class="inline-flex items-center gap-1 text-[10px] font-black text-emerald-700">
                                                <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                                <span>Lunas</span>
                                            </span>
                                            <span class="text-[9px] font-mono font-bold text-emerald-800 mt-0.5">
                                                Rp {{ number_format($cell['nominal'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @elseif ($cell['status'] === 'sebagian')
                                        <a 
                                            href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswa->id, 'tagihan_id' => $cell['id']]) }}" 
                                            wire:navigate
                                            class="group block w-full p-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 hover:border-amber-300 shadow-2xs transition text-center cursor-pointer"
                                            title="Klik untuk pembayaran SPP {{ $m }}"
                                        >
                                            <div class="text-center leading-tight">
                                                <span class="text-[9px] font-bold text-amber-800 block">Dicicil</span>
                                                <span class="text-[10px] font-black text-amber-900 font-mono">Sisa Rp {{ number_format($cell['sisa'], 0, ',', '.') }}</span>
                                            </div>
                                            <span class="mt-1.5 inline-flex items-center justify-center gap-1 w-full px-2 py-1 rounded-lg bg-emerald-700 group-hover:bg-emerald-800 text-white text-[10px] font-black shadow-2xs transition">
                                                <x-lucide-credit-card class="w-2.5 h-2.5 shrink-0" />
                                                <span>Bayar Tagihan</span>
                                            </span>
                                        </a>
                                    @elseif ($cell['is_mendatang'])
                                        <a 
                                            href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswa->id, 'tagihan_id' => $cell['id']]) }}" 
                                            wire:navigate
                                            class="group block w-full p-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-900 border border-sky-200 hover:border-sky-300 shadow-2xs transition text-center cursor-pointer"
                                            title="Klik untuk bayar tagihan mendatang SPP {{ $m }}"
                                        >
                                            <div class="text-center leading-tight">
                                                <span class="text-[10px] font-black text-sky-800 font-mono">
                                                    Rp {{ number_format($cell['nominal'], 0, ',', '.') }}
                                                </span>
                                                <span class="text-[9px] font-bold text-sky-700 block mt-0.5">Mendatang</span>
                                            </div>
                                            <span class="mt-1.5 inline-flex items-center justify-center gap-1 w-full px-2 py-1 rounded-lg bg-sky-700 group-hover:bg-sky-800 text-white text-[10px] font-black shadow-2xs transition">
                                                <x-lucide-credit-card class="w-2.5 h-2.5 shrink-0" />
                                                <span>Bayar Tagihan</span>
                                            </span>
                                        </a>
                                    @else
                                        <a 
                                            href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswa->id, 'tagihan_id' => $cell['id']]) }}" 
                                            wire:navigate
                                            class="group block w-full p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-200 hover:border-rose-300 shadow-2xs transition text-center cursor-pointer"
                                            title="Klik untuk pembayaran SPP {{ $m }}"
                                        >
                                            <div class="text-center leading-tight">
                                                <span class="text-[10px] font-black text-rose-700 font-mono">
                                                    Rp {{ number_format($cell['nominal'], 0, ',', '.') }}
                                                </span>
                                                <span class="text-[9px] font-bold text-rose-600 block mt-0.5">Belum Bayar</span>
                                            </div>
                                            <span class="mt-1.5 inline-flex items-center justify-center gap-1 w-full px-2 py-1 rounded-lg bg-emerald-700 group-hover:bg-emerald-800 text-white text-[10px] font-black shadow-2xs transition">
                                                <x-lucide-credit-card class="w-2.5 h-2.5 shrink-0" />
                                                <span>Bayar Tagihan</span>
                                            </span>
                                        </a>
                                    @endif
                                @else
                                    <div class="flex flex-col items-center justify-center py-1">
                                        @if (!auth()->user()->isSuperAdmin2())
                                            <button 
                                                type="button" 
                                                wire:click="quickCreateTagihanForMonth({{ $sppJenis->id ?? 1 }}, '{{ $m }}')" 
                                                class="w-full flex flex-col items-center justify-center py-2 px-1.5 rounded-xl border border-dashed border-stone-300 hover:border-emerald-500 hover:bg-emerald-50/70 text-stone-500 hover:text-emerald-800 transition cursor-pointer group shadow-2xs"
                                                title="Tambah Tagihan SPP Bulan {{ $m }}"
                                            >
                                                <span class="inline-flex items-center gap-1 text-[10px] font-black text-stone-600 group-hover:text-emerald-700">
                                                    <x-lucide-plus class="w-3.5 h-3.5 text-stone-400 group-hover:text-emerald-600 shrink-0" />
                                                    <span>+ Tambah</span>
                                                </span>
                                                <span class="text-[9px] text-stone-400 group-hover:text-emerald-600 font-medium">{{ $m }}</span>
                                            </button>
                                        @else
                                            <span class="text-stone-300 font-bold text-xs">-</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        @endforeach

                        <!-- Kolom Non-SPP: Status & Aksi -->
                        @foreach ($nonSppJenisList as $jt)
                            @php
                                $cell = $nonSppBillsData[$jt->id];
                            @endphp
                            <td class="p-2 text-center border-b border-r border-stone-200">
                                @if ($cell['has_tagihan'])
                                    @if ($cell['status'] === 'lunas')
                                        <div 
                                            class="inline-flex flex-col items-center justify-center p-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs w-full"
                                            title="{{ $jt->nama }} Lunas (Rp {{ number_format($cell['original_nominal'] ?: $cell['nominal'], 0, ',', '.') }}) {{ $cell['original_bulan'] ? '(1x Bayar • ' . $cell['original_bulan'] . ')' : '' }}"
                                        >
                                            <span class="inline-flex items-center gap-1 text-[10px] font-black text-emerald-700">
                                                <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                                <span>Lunas</span>
                                            </span>
                                            @if (!empty($cell['original_bulan']))
                                                <span class="text-[9px] font-bold text-emerald-700 mt-0.5">
                                                    1x Bayar • {{ $cell['original_bulan'] }}
                                                </span>
                                            @else
                                                <span class="text-[9px] font-mono font-bold text-emerald-800 mt-0.5">
                                                    Rp {{ number_format($cell['nominal'], 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                    @elseif ($cell['status'] === 'sebagian')
                                        <a 
                                            href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswa->id, 'tagihan_id' => $cell['id']]) }}" 
                                            wire:navigate
                                            class="group block w-full p-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 hover:border-amber-300 shadow-2xs transition text-center cursor-pointer"
                                            title="Klik untuk bayar tagihan {{ $jt->nama }}"
                                        >
                                            <div class="text-center leading-tight">
                                                <span class="text-[9px] font-bold text-amber-800 block">
                                                    Dicicil • {{ $cell['original_bulan'] ?? 'Tahunan' }}
                                                </span>
                                                <span class="text-[10px] font-black text-amber-900 font-mono">Sisa Rp {{ number_format($cell['sisa'], 0, ',', '.') }}</span>
                                            </div>
                                            <span class="mt-1.5 inline-flex items-center justify-center gap-1 w-full px-2 py-1 rounded-lg bg-emerald-700 group-hover:bg-emerald-800 text-white text-[10px] font-black shadow-2xs transition">
                                                <x-lucide-credit-card class="w-2.5 h-2.5 shrink-0" />
                                                <span>Bayar Tagihan</span>
                                            </span>
                                        </a>
                                    @else
                                        <a 
                                            href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswa->id, 'tagihan_id' => $cell['id']]) }}" 
                                            wire:navigate
                                            class="group block w-full p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-200 hover:border-rose-300 shadow-2xs transition text-center cursor-pointer"
                                            title="Klik untuk bayar tagihan {{ $jt->nama }}"
                                        >
                                            <div class="text-center leading-tight">
                                                <span class="text-[10px] font-black text-rose-700 font-mono">
                                                    Rp {{ number_format($cell['original_nominal'] ?: $cell['nominal'], 0, ',', '.') }}
                                                </span>
                                                <span class="text-[9px] font-bold text-rose-600 block mt-0.5">
                                                    Belum Bayar • {{ $cell['original_bulan'] ?? 'Tahunan' }}
                                                </span>
                                            </div>
                                            <span class="mt-1.5 inline-flex items-center justify-center gap-1 w-full px-2 py-1 rounded-lg bg-emerald-700 group-hover:bg-emerald-800 text-white text-[10px] font-black shadow-2xs transition">
                                                <x-lucide-credit-card class="w-2.5 h-2.5 shrink-0" />
                                                <span>Bayar Tagihan</span>
                                            </span>
                                        </a>
                                    @endif
                                @else
                                    <div class="flex flex-col items-center justify-center py-1">
                                        @if (!auth()->user()->isSuperAdmin2())
                                            <button 
                                                type="button" 
                                                wire:click="quickCreateTagihanForMonth({{ $jt->id }}, 'Tahunan')" 
                                                class="w-full flex flex-col items-center justify-center py-2 px-1.5 rounded-xl border border-dashed border-stone-300 hover:border-emerald-500 hover:bg-emerald-50/70 text-stone-500 hover:text-emerald-800 transition cursor-pointer group shadow-2xs"
                                                title="Tambah Tagihan {{ $jt->nama }}"
                                            >
                                                <span class="inline-flex items-center gap-1 text-[10px] font-black text-stone-600 group-hover:text-emerald-700">
                                                    <x-lucide-plus class="w-3.5 h-3.5 text-stone-400 group-hover:text-emerald-600 shrink-0" />
                                                    <span>+ Tambah</span>
                                                </span>
                                                <span class="text-[9px] text-stone-400 group-hover:text-emerald-600 font-medium">Kategori ini</span>
                                            </button>
                                        @else
                                            <span class="text-stone-300 font-bold text-xs">-</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        @endforeach

                        <!-- Total SPP Status -->
                        <td class="p-3 text-center border-b border-r border-stone-200">
                            @if ($matrix6BulanSummary['spp']['sisa'] <= 0 && $matrix6BulanSummary['spp']['nominal'] > 0)
                                <x-badge variant="emerald" size="xs" :dot="true">Lunas (6 Bln)</x-badge>
                            @elseif ($matrix6BulanSummary['spp']['sisa'] > 0)
                                <x-badge variant="rose" size="xs" :dot="true">Tunggakan</x-badge>
                            @else
                                <span class="text-stone-400 font-bold">-</span>
                            @endif
                        </td>

                        <!-- Total Non-SPP Status -->
                        <td class="p-3 text-center border-b border-r border-stone-200">
                            @if ($matrix6BulanSummary['non_spp']['sisa'] <= 0 && $matrix6BulanSummary['non_spp']['nominal'] > 0)
                                <x-badge variant="emerald" size="xs" :dot="true">Lunas</x-badge>
                            @elseif ($matrix6BulanSummary['non_spp']['sisa'] > 0)
                                <x-badge variant="rose" size="xs" :dot="true">Tunggakan</x-badge>
                            @else
                                <span class="text-stone-400 font-bold">-</span>
                            @endif
                        </td>

                        <!-- Grand Total Status -->
                        <td class="p-3 text-center border-b border-stone-200">
                            @if ($matrix6BulanSummary['grand_sisa'] <= 0 && $matrix6BulanSummary['grand_nominal'] > 0)
                                <x-badge variant="emerald" size="xs">Lunas 100%</x-badge>
                            @else
                                <x-badge variant="rose" size="xs">Belum Lunas</x-badge>
                            @endif
                        </td>
                    </tr>

                    <!-- BARIS 2: KEWAJIBAN TAGIHAN (NOMINAL) -->
                    <tr class="hover:bg-emerald-50/30 transition">
                        <td class="p-3 border-b border-r-2 border-stone-300 sticky left-0 z-20 bg-stone-100 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.06)]">
                            <div class="flex items-center gap-2">
                                <x-lucide-receipt class="w-4 h-4 text-stone-700 shrink-0" />
                                <div>
                                    <span class="block text-xs font-black text-stone-900">Kewajiban Tagihan (Nominal)</span>
                                    <span class="text-[10px] text-stone-500 font-medium">Beban Biaya Siswa</span>
                                </div>
                            </div>
                        </td>
                        @foreach ($sppMatrixMonths as $m)
                            <td class="p-2.5 text-center font-mono font-bold text-stone-800 border-b border-r border-stone-200">
                                Rp {{ number_format($spp6MonthsData[$m]['nominal'], 0, ',', '.') }}
                            </td>
                        @endforeach
                        @foreach ($nonSppJenisList as $jt)
                            <td class="p-2.5 text-center font-mono font-bold text-stone-800 border-b border-r border-stone-200">
                                Rp {{ number_format($nonSppBillsData[$jt->id]['nominal'], 0, ',', '.') }}
                            </td>
                        @endforeach
                        <td class="p-2.5 text-right font-mono font-bold text-stone-800 border-b border-r border-stone-200">
                            Rp {{ number_format($matrix6BulanSummary['spp']['nominal'], 0, ',', '.') }}
                        </td>
                        <td class="p-2.5 text-right font-mono font-bold text-stone-800 border-b border-r border-stone-200">
                            Rp {{ number_format($matrix6BulanSummary['non_spp']['nominal'], 0, ',', '.') }}
                        </td>
                        <td class="p-2.5 text-right font-mono font-black text-stone-900 border-b border-stone-200">
                            Rp {{ number_format($matrix6BulanSummary['grand_nominal'], 0, ',', '.') }}
                        </td>
                    </tr>

                    <!-- BARIS 3: TOTAL TELAH DIBAYAR -->
                    <tr class="hover:bg-emerald-50/30 transition">
                        <td class="p-3 border-b border-r-2 border-stone-300 sticky left-0 z-20 bg-stone-100 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.06)]">
                            <div class="flex items-center gap-2">
                                <x-lucide-check-check class="w-4 h-4 text-emerald-700 shrink-0" />
                                <div>
                                    <span class="block text-xs font-black text-stone-900">Total Telah Dibayar</span>
                                    <span class="text-[10px] text-emerald-700 font-medium">Realisasi Penerimaan</span>
                                </div>
                            </div>
                        </td>
                        @foreach ($sppMatrixMonths as $m)
                            <td class="p-2.5 text-center font-mono font-bold text-emerald-700 border-b border-r border-stone-200">
                                Rp {{ number_format($spp6MonthsData[$m]['total_dibayar'], 0, ',', '.') }}
                            </td>
                        @endforeach
                        @foreach ($nonSppJenisList as $jt)
                            <td class="p-2.5 text-center font-mono font-bold text-emerald-700 border-b border-r border-stone-200">
                                Rp {{ number_format($nonSppBillsData[$jt->id]['total_dibayar'], 0, ',', '.') }}
                            </td>
                        @endforeach
                        <td class="p-2.5 text-right font-mono font-bold text-emerald-700 border-b border-r border-stone-200">
                            Rp {{ number_format($matrix6BulanSummary['spp']['dibayar'], 0, ',', '.') }}
                        </td>
                        <td class="p-2.5 text-right font-mono font-bold text-emerald-700 border-b border-r border-stone-200">
                            Rp {{ number_format($matrix6BulanSummary['non_spp']['dibayar'], 0, ',', '.') }}
                        </td>
                        <td class="p-2.5 text-right font-mono font-black text-emerald-800 border-b border-stone-200">
                            Rp {{ number_format($matrix6BulanSummary['grand_dibayar'], 0, ',', '.') }}
                        </td>
                    </tr>

                    <!-- BARIS 4: SISA TUNGGAKAN (PIUTANG) -->
                    <tr class="hover:bg-emerald-50/30 transition">
                        <td class="p-3 border-b border-r-2 border-stone-300 sticky left-0 z-20 bg-stone-100 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.06)]">
                            <div class="flex items-center gap-2">
                                <x-lucide-alert-circle class="w-4 h-4 text-rose-600 shrink-0" />
                                <div>
                                    <span class="block text-xs font-black text-stone-900">Sisa Tunggakan (Piutang)</span>
                                    <span class="text-[10px] text-rose-600 font-medium">Kewajiban Belum Terbayar</span>
                                </div>
                            </div>
                        </td>
                        @foreach ($sppMatrixMonths as $m)
                            @php $cell = $spp6MonthsData[$m]; @endphp
                            <td class="p-2.5 text-center font-mono font-bold border-b border-r border-stone-200">
                                @if ($cell['sisa'] > 0)
                                    <span class="text-rose-700 font-black">Rp {{ number_format($cell['sisa'], 0, ',', '.') }}</span>
                                @elseif ($cell['nominal'] > 0)
                                    <span class="text-emerald-700 font-bold">Lunas</span>
                                @else
                                    <span class="text-stone-400 font-bold">-</span>
                                @endif
                            </td>
                        @endforeach
                        @foreach ($nonSppJenisList as $jt)
                            @php $cell = $nonSppBillsData[$jt->id]; @endphp
                            <td class="p-2.5 text-center font-mono font-bold border-b border-r border-stone-200">
                                @if ($cell['sisa'] > 0)
                                    <span class="text-rose-700 font-black">Rp {{ number_format($cell['sisa'], 0, ',', '.') }}</span>
                                @elseif ($cell['nominal'] > 0)
                                    <span class="text-emerald-700 font-bold">Lunas</span>
                                @else
                                    <span class="text-stone-400 font-bold">-</span>
                                @endif
                            </td>
                        @endforeach
                        <td class="p-2.5 text-right font-mono font-bold border-b border-r border-stone-200">
                            @if ($matrix6BulanSummary['spp']['sisa'] > 0)
                                <span class="text-rose-700 font-black">Rp {{ number_format($matrix6BulanSummary['spp']['sisa'], 0, ',', '.') }}</span>
                            @else
                                <span class="text-emerald-700 font-bold">Lunas</span>
                            @endif
                        </td>
                        <td class="p-2.5 text-right font-mono font-bold border-b border-r border-stone-200">
                            @if ($matrix6BulanSummary['non_spp']['sisa'] > 0)
                                <span class="text-rose-700 font-black">Rp {{ number_format($matrix6BulanSummary['non_spp']['sisa'], 0, ',', '.') }}</span>
                            @else
                                <span class="text-emerald-700 font-bold">Lunas</span>
                            @endif
                        </td>
                        <td class="p-2.5 text-right font-mono font-black border-b border-stone-200">
                            @if ($matrix6BulanSummary['grand_sisa'] > 0)
                                <span class="text-rose-700 font-black">Rp {{ number_format($matrix6BulanSummary['grand_sisa'], 0, ',', '.') }}</span>
                            @else
                                <span class="text-emerald-700 font-bold">Lunas</span>
                            @endif
                        </td>
                    </tr>

                    <!-- BARIS 5: HISTORI PEMBAYARAN TERAKHIR -->
                    <tr class="hover:bg-emerald-50/30 transition">
                        <td class="p-3 border-r-2 border-stone-300 sticky left-0 z-20 bg-stone-100 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.06)]">
                            <div class="flex items-center gap-2">
                                <x-lucide-clock class="w-4 h-4 text-stone-600 shrink-0" />
                                <div>
                                    <span class="block text-xs font-black text-stone-900">Histori Pembayaran Terakhir</span>
                                    <span class="text-[10px] text-stone-500 font-medium">Tanggal Transaksi Terakhir</span>
                                </div>
                            </div>
                        </td>
                        @foreach ($sppMatrixMonths as $m)
                            <td class="p-2.5 text-center text-[11px] font-mono text-stone-600 border-r border-stone-200">
                                {{ $spp6MonthsData[$m]['terakhir_bayar'] ?? '-' }}
                            </td>
                        @endforeach
                        @foreach ($nonSppJenisList as $jt)
                            <td class="p-2.5 text-center text-[11px] font-mono text-stone-600 border-r border-stone-200">
                                {{ $nonSppBillsData[$jt->id]['terakhir_bayar'] ?? '-' }}
                            </td>
                        @endforeach
                        <td class="p-2.5 text-right text-[11px] font-mono text-stone-400 border-r border-stone-200">
                            -
                        </td>
                        <td class="p-2.5 text-right text-[11px] font-mono text-stone-400 border-r border-stone-200">
                            -
                        </td>
                        <td class="p-2.5 text-right text-[11px] font-mono text-stone-400">
                            -
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ACCORDION / EXPANDABLE SECTION: PEMBUKUAN KALENDER LENGKAP 12 BULAN -->
        <div class="mt-4 pt-3 border-t border-stone-200">
            <details class="group rounded-2xl border border-stone-200 bg-stone-50/50 overflow-hidden shadow-2xs">
                <summary class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-stone-100/70 transition font-bold text-xs text-stone-800 select-none">
                    <div class="flex items-center gap-2">
                        <x-lucide-calendar-days class="w-4 h-4 text-emerald-700 shrink-0" />
                        <span class="font-extrabold text-stone-900">Pembukuan Riwayat Lengkap 12 Bulan (Kalender TA {{ $activeTAName }})</span>
                        <span class="text-[10px] text-stone-500 font-normal hidden sm:inline">Rincian tabel per bulan Juli s/d Juni & Tahunan</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-emerald-700 font-bold text-xs">
                        <span>Rincian Kalender</span>
                        <x-lucide-chevron-down class="w-4 h-4 group-open:rotate-180 transition-transform duration-200" />
                    </div>
                </summary>

                <div class="p-4 border-t border-stone-200 bg-white">
                    <div class="overflow-x-auto rounded-xl border border-stone-200 shadow-2xs bg-white">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider text-[11px] border-b border-emerald-900">
                                <tr>
                                    <!-- Kolom Bulan -->
                                    <th class="p-3 w-32 border-r border-emerald-700/60 sticky left-0 z-10 bg-emerald-800 text-center">
                                        Bulan
                                    </th>

                                    <!-- Kolom Jenis Tagihan -->
                                    @foreach ($jenisTagihanList as $jt)
                                        <th class="p-2.5 text-center min-w-[125px] border-r border-emerald-700/60">
                                            <span class="block font-black">{{ $jt->nama }}</span>
                                            <span class="text-[9px] font-normal opacity-85 block capitalize">({{ $jt->kategori }})</span>
                                        </th>
                                    @endforeach

                                    <!-- Rangkuman Baris per Bulan -->
                                    <th class="p-3 w-28 text-right border-r border-emerald-700/60">Total Bln Ini</th>
                                    <th class="p-3 w-28 text-right border-r border-emerald-700/60">Dibayar</th>
                                    <th class="p-3 w-28 text-right border-r border-emerald-700/60">Sisa Tunggakan</th>
                                    <th class="p-3 w-24 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200 bg-white">
                                @if (!empty($detailMatrixData['months_rows']))
                                    @foreach ($detailMatrixData['months_rows'] as $mName => $rowData)
                                        <tr class="hover:bg-emerald-50/30 transition text-xs">
                                            <!-- Bulan -->
                                            <td class="p-3 font-extrabold text-stone-900 border-r border-stone-200 sticky left-0 bg-white">
                                                <span class="px-2.5 py-1 rounded-lg bg-stone-100 border border-stone-200 font-bold text-stone-800 block text-center">
                                                    {{ $mName }}
                                                </span>
                                            </td>

                                            <!-- Kolom Tagihan -->
                                            @foreach ($jenisTagihanList as $jt)
                                                @php
                                                    $cell = $rowData['bills'][$jt->id];
                                                @endphp
                                                <td class="p-2 text-center border-r border-stone-200">
                                                    @if ($cell['has_tagihan'])
                                                        @if ($cell['status'] === 'lunas')
                                                            <div 
                                                                class="inline-flex flex-col items-center justify-center p-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs w-full"
                                                                title="{{ $jt->nama }} Lunas (Rp {{ number_format(!empty($cell['original_nominal']) ? $cell['original_nominal'] : $cell['nominal'], 0, ',', '.') }}) {{ !empty($cell['is_one_time_fulfilled']) ? '(1x Bayar di bulan ' . $cell['original_bulan'] . ')' : ($cell['terakhir_bayar'] ? 'Tgl ' . $cell['terakhir_bayar'] : '') }}"
                                                            >
                                                                <span class="inline-flex items-center gap-1 text-[10px] font-black text-emerald-700">
                                                                    <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                                                    <span>Lunas</span>
                                                                </span>
                                                                @if (!empty($cell['is_one_time_fulfilled']))
                                                                    <span class="text-[9px] font-bold text-emerald-700 mt-0.5">
                                                                        1x Bayar • {{ $cell['original_bulan'] }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-[9px] font-mono font-bold text-emerald-800 mt-0.5">
                                                                        Rp {{ number_format($cell['nominal'], 0, ',', '.') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @elseif ($cell['status'] === 'sebagian')
                                                            <a 
                                                                href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswa->id, 'tagihan_id' => $cell['id']]) }}" 
                                                                wire:navigate
                                                                class="group block w-full p-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 hover:border-amber-300 shadow-2xs transition text-center cursor-pointer"
                                                                title="Klik untuk langsung ke Pembayaran Tagihan {{ $jt->nama }} (Bulan {{ $cell['original_bulan'] ?? $mName }})"
                                                            >
                                                                <div class="text-center leading-tight">
                                                                    <span class="text-[9px] font-bold text-amber-800 block">
                                                                        @if (!empty($cell['is_one_time_carried']))
                                                                            Dicicil • {{ $cell['original_bulan'] }}
                                                                        @else
                                                                            Dicicil
                                                                        @endif
                                                                    </span>
                                                                    <span class="text-[10px] font-black text-amber-900 font-mono">Sisa Rp {{ number_format($cell['sisa'], 0, ',', '.') }}</span>
                                                                </div>
                                                                <span class="mt-1.5 inline-flex items-center justify-center gap-1 w-full px-2 py-1 rounded-lg bg-emerald-700 group-hover:bg-emerald-800 text-white text-[10px] font-black shadow-2xs transition">
                                                                    <x-lucide-credit-card class="w-2.5 h-2.5 shrink-0" />
                                                                    <span>Bayar Tagihan</span>
                                                                </span>
                                                            </a>
                                                        @elseif ($cell['is_mendatang'])
                                                            <a 
                                                                href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswa->id, 'tagihan_id' => $cell['id']]) }}" 
                                                                wire:navigate
                                                                class="group block w-full p-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-900 border border-sky-200 hover:border-sky-300 shadow-2xs transition text-center cursor-pointer"
                                                                title="Klik untuk bayar tagihan mendatang {{ $jt->nama }} (Bulan {{ $mName }})"
                                                            >
                                                                <div class="text-center leading-tight">
                                                                    <span class="text-[10px] font-black text-sky-800 font-mono">
                                                                        Rp {{ number_format($cell['nominal'], 0, ',', '.') }}
                                                                    </span>
                                                                    <span class="text-[9px] font-bold text-sky-700 block mt-0.5">Mendatang</span>
                                                                </div>
                                                                <span class="mt-1.5 inline-flex items-center justify-center gap-1 w-full px-2 py-1 rounded-lg bg-sky-700 group-hover:bg-sky-800 text-white text-[10px] font-black shadow-2xs transition">
                                                                    <x-lucide-credit-card class="w-2.5 h-2.5 shrink-0" />
                                                                    <span>Bayar Tagihan</span>
                                                                </span>
                                                            </a>
                                                        @else
                                                            <a 
                                                                href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswa->id, 'tagihan_id' => $cell['id']]) }}" 
                                                                wire:navigate
                                                                class="group block w-full p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-200 hover:border-rose-300 shadow-2xs transition text-center cursor-pointer"
                                                                title="Klik untuk langsung ke Pembayaran Tagihan {{ $jt->nama }} (Bulan {{ $cell['original_bulan'] ?? $mName }})"
                                                            >
                                                                <div class="text-center leading-tight">
                                                                    <span class="text-[10px] font-black text-rose-700 font-mono">
                                                                        Rp {{ number_format(!empty($cell['original_nominal']) ? $cell['original_nominal'] : $cell['nominal'], 0, ',', '.') }}
                                                                    </span>
                                                                    <span class="text-[9px] font-bold text-rose-600 block mt-0.5">
                                                                        @if (!empty($cell['is_one_time_carried']))
                                                                            Tunggakan • {{ $cell['original_bulan'] }}
                                                                        @else
                                                                            Belum Bayar
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                                <span class="mt-1.5 inline-flex items-center justify-center gap-1 w-full px-2 py-1 rounded-lg bg-emerald-700 group-hover:bg-emerald-800 text-white text-[10px] font-black shadow-2xs transition">
                                                                    <x-lucide-credit-card class="w-2.5 h-2.5 shrink-0" />
                                                                    <span>Bayar Tagihan</span>
                                                                </span>
                                                            </a>
                                                        @endif
                                                    @else
                                                        <!-- Tombol Tambah Tagihan Sesuai Kolom & Baris -->
                                                        <div class="flex flex-col items-center justify-center py-1">
                                                            @if (!auth()->user()->isSuperAdmin2())
                                                                <button 
                                                                    type="button" 
                                                                    wire:click="quickCreateTagihanForMonth({{ $jt->id }}, '{{ $mName }}')" 
                                                                    class="w-full flex flex-col items-center justify-center py-2 px-1.5 rounded-xl border border-dashed border-stone-300 hover:border-emerald-500 hover:bg-emerald-50/70 text-stone-500 hover:text-emerald-800 transition cursor-pointer group shadow-2xs"
                                                                    title="Tambah Tagihan {{ $jt->nama }} Bulan {{ $mName }}"
                                                                >
                                                                    <span class="inline-flex items-center gap-1 text-[10px] font-black text-stone-600 group-hover:text-emerald-700">
                                                                        <x-lucide-plus class="w-3.5 h-3.5 text-stone-400 group-hover:text-emerald-600 shrink-0" />
                                                                        <span>+ Tambah</span>
                                                                    </span>
                                                                    <span class="text-[9px] text-stone-400 group-hover:text-emerald-600 font-medium">Isi kolom ini</span>
                                                                </button>
                                                            @else
                                                                <span class="text-stone-300 font-bold text-xs">-</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                            @endforeach

                                            <!-- Total Bln Ini -->
                                            <td class="p-3 text-right font-mono font-bold text-stone-800 border-r border-stone-200">
                                                Rp {{ number_format($rowData['total_nominal'], 0, ',', '.') }}
                                            </td>

                                            <!-- Dibayar -->
                                            <td class="p-3 text-right font-mono font-bold text-emerald-700 border-r border-stone-200">
                                                Rp {{ number_format($rowData['total_dibayar'], 0, ',', '.') }}
                                            </td>

                                            <!-- Sisa Tunggakan -->
                                            <td class="p-3 text-right font-mono font-black border-r border-stone-200">
                                                @if ($rowData['sisa_tunggakan'] > 0)
                                                    <span class="text-rose-700">
                                                        Rp {{ number_format($rowData['sisa_tunggakan'], 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="text-emerald-700 text-[11px] font-bold">Lunas</span>
                                                @endif
                                            </td>

                                            <!-- Status -->
                                            <td class="p-3 text-center">
                                                @if ($rowData['status'] === 'Lunas')
                                                    <x-badge variant="emerald" size="xs" :dot="true">Lunas</x-badge>
                                                @elseif ($rowData['status'] === 'Ada Tunggakan')
                                                    <x-badge variant="rose" size="xs" :dot="true">Tunggakan</x-badge>
                                                @else
                                                    <span class="text-stone-400 font-bold">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ 5 + count($jenisTagihanList) }}" class="p-8 text-center text-stone-500">
                                            Belum ada tagihan yang diterbitkan untuk santri ini pada Tahun Ajaran {{ $activeTAName }}.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>

                            <!-- Footer: Akumulasi Per Jenis Tagihan -->
                            @if (!empty($detailMatrixData['footer_per_jenis']))
                                <tfoot class="bg-stone-50 border-t-2 border-stone-300 text-xs font-bold text-stone-800">
                                    <tr>
                                        <td class="p-3 font-black uppercase text-stone-800 border-r border-stone-200 sticky left-0 bg-stone-50">
                                            Total 1 Tahun:
                                        </td>
                                        @foreach ($jenisTagihanList as $jt)
                                            @php
                                                $fData = $detailMatrixData['footer_per_jenis'][$jt->id];
                                            @endphp
                                            <td class="p-2.5 text-center border-r border-stone-200">
                                                @if ($fData['nominal'] > 0)
                                                    <div class="text-[11px] font-black font-mono text-stone-900">
                                                        Rp {{ number_format($fData['nominal'], 0, ',', '.') }}
                                                    </div>
                                                    @if ($fData['sisa'] > 0)
                                                        <div class="text-[9px] font-bold text-rose-700">
                                                            Sisa: Rp {{ number_format($fData['sisa'], 0, ',', '.') }}
                                                        </div>
                                                    @else
                                                        <div class="text-[9px] font-bold text-emerald-700">Lunas</div>
                                                    @endif
                                                @else
                                                    <span class="text-stone-400 font-bold text-xs">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="p-3 text-right font-black font-mono border-r border-stone-200">
                                            Rp {{ number_format($detailMatrixData['grand_nominal'], 0, ',', '.') }}
                                        </td>
                                        <td class="p-3 text-right font-black font-mono text-emerald-700 border-r border-stone-200">
                                            Rp {{ number_format($detailMatrixData['grand_dibayar'], 0, ',', '.') }}
                                        </td>
                                        <td class="p-3 text-right font-black font-mono text-rose-700 border-r border-stone-200">
                                            Rp {{ number_format($detailMatrixData['grand_tunggakan'], 0, ',', '.') }}
                                        </td>
                                        <td class="p-3 text-center">
                                            @if ($detailMatrixData['grand_tunggakan'] <= 0 && $detailMatrixData['grand_nominal'] > 0)
                                                <x-badge variant="emerald" size="xs">Lunas 100%</x-badge>
                                            @else
                                                <x-badge variant="rose" size="xs">Belum Lunas</x-badge>
                                            @endif
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </details>
        </div>
    @else
        <!-- KARTU RINGKAS SPP 12 BULAN -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2.5">
            @foreach ($sppMatrix as $mKey => $mVal)
                @php
                    $cardClasses = match($mVal['status']) {
                        'lunas' => 'bg-emerald-50/60 border-emerald-200 text-emerald-950',
                        'sebagian' => 'bg-amber-50/60 border-amber-200 text-amber-950',
                        'belum_bayar' => 'bg-rose-50/60 border-rose-200 text-rose-950',
                        'mendatang' => 'bg-sky-50/60 border-sky-200 text-sky-950',
                        default => 'bg-stone-50 border-stone-200 text-stone-400',
                    };
                @endphp
                <div class="p-3 rounded-xl border transition {{ $cardClasses }}">
                    <div class="flex items-center justify-between gap-1 mb-1">
                        <span class="text-xs font-black uppercase">{{ $mKey }}</span>
                        @if ($mVal['status'] === 'lunas')
                            <x-lucide-check-circle class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                        @elseif ($mVal['status'] === 'sebagian')
                            <x-lucide-clock class="w-3.5 h-3.5 text-amber-600 shrink-0" />
                        @elseif ($mVal['status'] === 'belum_bayar')
                            <x-lucide-alert-circle class="w-3.5 h-3.5 text-rose-600 shrink-0" />
                        @elseif ($mVal['status'] === 'mendatang')
                            <x-lucide-calendar class="w-3.5 h-3.5 text-sky-600 shrink-0" />
                        @else
                            <x-lucide-minus-circle class="w-3.5 h-3.5 text-stone-300 shrink-0" />
                        @endif
                    </div>

                    @if ($mVal['has_bill'])
                        <div class="text-xs font-black">
                            Rp {{ number_format($mVal['nominal'], 0, ',', '.') }}
                        </div>
                        <div class="text-[10px] font-bold mt-1">
                            @if ($mVal['status'] === 'lunas')
                                <span class="text-emerald-700">Lunas</span>
                            @elseif ($mVal['status'] === 'sebagian')
                                <span class="text-amber-700">Sisa: Rp {{ number_format($mVal['sisa'], 0, ',', '.') }}</span>
                            @elseif ($mVal['status'] === 'mendatang')
                                <span class="text-sky-700 font-extrabold">Mendatang</span>
                            @else
                                <span class="text-rose-700">Belum Bayar</span>
                            @endif
                        </div>
                    @else
                        <div class="text-[11px] font-medium text-stone-400 italic">
                            Belum Diterbitkan
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
