<!-- TABEL LENGKAP: Rekapitulasi Tagihan Siswa Per Bulan -->
<div class="space-y-4 font-sans">
    <!-- 1. Pilihan 12 Bulan Tahun Ajaran (Pill Strip) -->
    <div class="p-3 bg-stone-50 border border-stone-200 rounded-2xl space-y-2">
        <div class="flex items-center justify-between flex-wrap gap-2 px-1">
            <div class="flex items-center gap-2">
                <div class="p-1 rounded-lg bg-emerald-100/80 text-emerald-800 border border-emerald-200">
                    <x-lucide-calendar-range class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-xs font-black text-stone-900 uppercase tracking-tight">Pilih Bulan Tahun Ajaran</h3>
                    <p class="text-[11px] text-stone-600 font-medium">Klik bulan untuk memantau status pelunasan seluruh jenis tunggakan siswa pada periode tersebut.</p>
                </div>
            </div>

            <!-- Actions & Active Month Badge -->
            <div class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold text-stone-600">Bulan:</span>
                    <span class="px-2.5 py-1 bg-emerald-800 text-white rounded-lg text-xs font-black shadow-2xs">
                        {{ $selectedBulan }}
                    </span>
                </div>

                <a 
                    href="{{ route('finance.input-pembayaran') }}" 
                    wire:navigate
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-800 hover:bg-emerald-900 text-white text-xs font-bold rounded-xl transition shadow-2xs cursor-pointer"
                    title="Buka Kasir Pembayaran untuk transaksi baru"
                >
                    <x-lucide-plus class="w-3.5 h-3.5 shrink-0" />
                    <span>Buka Kasir Pembayaran</span>
                </a>
            </div>
        </div>

        <!-- 12-Month Interactive Mini Strip -->
        <div class="overflow-x-auto pb-1 pt-1">
            <div class="flex items-center gap-2 min-w-[780px]">
                @foreach ($standardMonths as $m)
                    @php
                        $mStat = $monthlyStats[$m] ?? null;
                        $isSelected = ($selectedBulan === $m);
                    @endphp
                    <button 
                        type="button" 
                        wire:click="selectBulan('{{ $m }}')" 
                        class="flex-1 min-w-[95px] p-2 rounded-xl border text-left transition select-none cursor-pointer {{ $isSelected ? 'bg-white border-emerald-600 ring-2 ring-emerald-600/30 shadow-xs' : 'bg-white/70 border-stone-200 hover:border-stone-300 hover:bg-white shadow-2xs' }}"
                        title="Tampilkan data seluruh murid bulan {{ $m }}"
                    >
                        <div class="flex items-center justify-between text-[11px] font-black uppercase {{ $isSelected ? 'text-emerald-800' : 'text-stone-800' }}">
                            <span>{{ substr($m, 0, 3) }}</span>
                            <span class="text-[9px] font-bold {{ $isSelected ? 'text-emerald-700' : 'text-stone-500' }}">{{ $mStat['persen'] ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-stone-100 h-1.5 rounded-full overflow-hidden mt-1">
                            <div class="bg-emerald-600 h-full rounded-full transition-all duration-300" style="width: {{ $mStat['persen'] ?? 0 }}%;"></div>
                        </div>
                        <div class="flex items-center justify-between text-[9px] font-bold mt-1">
                            <span class="text-emerald-700">{{ $mStat['lunas_count'] ?? 0 }} Lunas</span>
                            <span class="text-rose-700">{{ $mStat['belum_count'] ?? 0 }} Belum</span>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 2. Ringkasan & Filter Bar Bulan Terpilih -->
    @php
        $currStat = $monthlyStats[$selectedBulan] ?? null;
    @endphp
    @if ($currStat)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 bg-emerald-50/50 border border-emerald-200 rounded-2xl p-3.5 shadow-2xs">
            <!-- Stat 1: Bulan Terpilih -->
            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs">
                <span class="text-[10px] font-bold text-stone-600 uppercase tracking-wider block">Periode Dipantau</span>
                <div class="text-sm font-extrabold text-stone-900 mt-0.5">
                    Bulan {{ $selectedBulan }}
                </div>
                <span class="text-[11px] text-stone-600 font-medium">{{ $currStat['total_tagihan'] }} tagihan diterbitkan</span>
            </div>

            <!-- Stat 2: Sudah Bayar (Lunas) -->
            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Sudah Bayar</span>
                    <x-badge variant="emerald" size="xs">{{ $currStat['lunas_count'] }} Lunas</x-badge>
                </div>
                <div class="text-sm font-extrabold text-emerald-700 mt-0.5">
                    Rp {{ number_format($currStat['dibayar'], 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-stone-600 font-medium">Realisasi {{ $currStat['persen'] }}%</span>
            </div>

            <!-- Stat 3: Belum Bayar (Menunggak) -->
            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-rose-800 uppercase tracking-wider block">Belum Bayar</span>
                    <x-badge variant="rose" size="xs">{{ $currStat['belum_count'] }} Belum</x-badge>
                </div>
                <div class="text-sm font-extrabold text-rose-700 mt-0.5">
                    Rp {{ number_format($currStat['sisa'], 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-stone-600 font-medium">Sisa tagihan bulan {{ $selectedBulan }}</span>
            </div>

            <!-- Stat 4: Filter Cepat Pelunasan -->
            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs flex flex-col justify-between">
                <span class="text-[10px] font-bold text-stone-600 uppercase tracking-wider block">Filter Pelunasan Bulan Ini</span>
                <div class="flex items-center gap-1.5 mt-1">
                    <button 
                        type="button" 
                        wire:click="filterByBulanStatus('belum_bayar')" 
                        class="flex-1 py-1.5 px-2 text-[10px] font-bold rounded-lg transition border text-center cursor-pointer {{ $filterStatusBulan === 'belum_bayar' ? 'bg-rose-600 text-white border-rose-600 shadow-2xs' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}"
                    >
                        Belum Bayar ({{ $currStat['belum_count'] }})
                    </button>
                    <button 
                        type="button" 
                        wire:click="filterByBulanStatus('lunas')" 
                        class="flex-1 py-1.5 px-2 text-[10px] font-bold rounded-lg transition border text-center cursor-pointer {{ $filterStatusBulan === 'lunas' ? 'bg-emerald-600 text-white border-emerald-600 shadow-2xs' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}"
                    >
                        Lunas ({{ $currStat['lunas_count'] }})
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 3. Kontrol Pencarian, Kelas & Baris per Halaman -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <!-- Search Input -->
        <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama santri atau NIS..." />

        <!-- Filter Kelas -->
        <select wire:model.live="filterKelas" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
            <option value="">Semua Kelas</option>
            @foreach ($kelases as $k)
                <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
            @endforeach
        </select>

        <!-- Baris per Halaman -->
        <div class="flex items-center gap-2">
            <select wire:model.live="perPageLengkap" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="15">15 Siswa per Halaman</option>
                <option value="20">20 Siswa per Halaman</option>
                <option value="35">35 Siswa per Halaman</option>
                <option value="50">50 Siswa per Halaman</option>
            </select>

            @if ($filterStatusBulan !== '' || $search !== '' || $filterKelas !== null)
                <button 
                    type="button" 
                    wire:click="$set('filterStatusBulan', ''); $set('search', ''); $set('filterKelas', null);" 
                    class="p-2 text-stone-600 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 rounded-xl border border-stone-200 text-xs font-bold transition shrink-0 cursor-pointer"
                    title="Reset Filter"
                >
                    <x-lucide-rotate-ccw class="w-4 h-4" />
                </button>
            @endif
        </div>
    </div>

    <!-- 4. TABEL UTAMA: Data Tagihan Seluruh Murid -->
    <div class="overflow-x-auto rounded-2xl border border-stone-200 shadow-xs bg-white">
        <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider text-[11px] border-b border-emerald-900">
                <tr>
                    <th class="p-3 text-center w-12 border-r border-emerald-700/60 sticky left-0 z-10 bg-emerald-800">No</th>
                    <th class="p-3 min-w-[180px] border-r border-emerald-700/60 sticky left-12 z-10 bg-emerald-800">Identitas Santri</th>
                    <th class="p-3 w-24 border-r border-emerald-700/60 text-center">Kelas</th>
                    
                    <!-- Kolom Jenis-jenis Tunggakan -->
                    @foreach ($jenisTagihanList as $jt)
                        <th class="p-2.5 text-center min-w-[110px] border-r border-emerald-700/60">
                            <span class="block font-black">{{ $jt->nama }}</span>
                            <span class="text-[9px] font-normal opacity-85 block capitalize">({{ $jt->kategori }})</span>
                        </th>
                    @endforeach

                    <!-- Kolom Rangkuman Bulan Terpilih -->
                    <th class="p-3 w-32 text-right border-r border-emerald-700/60">Tagihan Bln Ini</th>
                    <th class="p-3 w-32 text-right border-r border-emerald-700/60">Sisa Tunggakan</th>
                    <th class="p-3 w-28 text-center border-r border-emerald-700/60">Status</th>
                    <th class="p-3 w-36 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($siswasLengkap as $index => $item)
                    <tr class="hover:bg-emerald-50/30 transition text-xs">
                        <!-- No -->
                        <td class="p-3 text-center font-mono font-bold text-stone-500 border-r border-stone-200 sticky left-0 bg-white">
                            {{ $siswasLengkap->firstItem() + $index }}
                        </td>

                        <!-- Identitas Santri -->
                        <td class="p-3 border-r border-stone-200 sticky left-12 bg-white">
                            <div class="font-extrabold text-stone-900 text-xs">{{ $item['nama'] }}</div>
                            <div class="text-[10px] text-stone-500 font-mono font-medium">NIS: {{ $item['nis'] }}</div>
                        </td>

                        <!-- Kelas -->
                        <td class="p-3 text-center border-r border-stone-200 font-bold text-stone-700">
                            <x-badge variant="stone" size="xs">{{ $item['kelas'] }}</x-badge>
                        </td>

                        <!-- Sel per Jenis Tunggakan -->
                        @foreach ($jenisTagihanList as $jt)
                            @php
                                $bData = $item['bills_by_jenis'][$jt->id];
                            @endphp
                            <td class="p-2 text-center border-r border-stone-200">
                                @if ($bData['has_tagihan'])
                                    @if ($bData['status'] === 'lunas')
                                        <span 
                                            class="inline-flex flex-col items-center justify-center px-2 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs w-full"
                                            title="{{ $jt->nama }} Lunas (Rp {{ number_format(!empty($bData['original_nominal']) ? $bData['original_nominal'] : $bData['nominal'], 0, ',', '.') }}) {{ !empty($bData['is_one_time_fulfilled']) ? '(1x Bayar di bulan ' . $bData['original_bulan'] . ')' : ($bData['terakhir_bayar'] ? 'Tgl ' . $bData['terakhir_bayar'] : '') }}"
                                        >
                                            <span class="inline-flex items-center gap-1">
                                                <x-lucide-check class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                                <span>Lunas</span>
                                            </span>
                                            @if (!empty($bData['is_one_time_fulfilled']))
                                                <span class="text-[9px] font-bold text-emerald-700 block mt-0.5">
                                                    1x • {{ $bData['original_bulan'] }}
                                                </span>
                                            @endif
                                        </span>
                                    @elseif ($bData['status'] === 'sebagian')
                                        <a 
                                            href="{{ route('finance.input-pembayaran', ['siswa_id' => $item['id'], 'tagihan_id' => $bData['tagihan_id']]) }}"
                                            wire:navigate
                                            class="group inline-flex flex-col items-center justify-center px-1.5 py-1 rounded-lg text-[10px] font-bold bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 hover:border-amber-300 shadow-2xs w-full transition cursor-pointer"
                                            title="Klik untuk langsung bayar tagihan {{ $jt->nama }} (Bulan {{ $bData['original_bulan'] ?? $selectedBulan }}) di Kasir"
                                        >
                                            <span class="inline-flex items-center gap-1">
                                                <x-lucide-clock class="w-3 h-3 text-amber-600 shrink-0" />
                                                <span>Cicil Rp {{ number_format($bData['sisa'], 0, ',', '.') }}</span>
                                            </span>
                                            @if (!empty($bData['is_one_time_carried']))
                                                <span class="text-[9px] font-bold text-amber-700 block mt-0.5">
                                                    Tunggakan • {{ $bData['original_bulan'] }}
                                                </span>
                                            @endif
                                        </a>
                                    @else
                                        <a 
                                            href="{{ route('finance.input-pembayaran', ['siswa_id' => $item['id'], 'tagihan_id' => $bData['tagihan_id']]) }}"
                                            wire:navigate
                                            class="group inline-flex flex-col items-center justify-center px-1.5 py-1 rounded-lg text-[10px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-200 hover:border-rose-300 shadow-2xs w-full transition cursor-pointer"
                                            title="Klik untuk langsung bayar tagihan {{ $jt->nama }} (Bulan {{ $bData['original_bulan'] ?? $selectedBulan }}) di Kasir"
                                        >
                                            <span class="inline-flex items-center gap-1">
                                                <x-lucide-alert-circle class="w-3 h-3 text-rose-600 shrink-0" />
                                                <span>Rp {{ number_format(!empty($bData['original_nominal']) ? $bData['original_nominal'] : $bData['nominal'], 0, ',', '.') }}</span>
                                            </span>
                                            @if (!empty($bData['is_one_time_carried']))
                                                <span class="text-[9px] font-bold text-rose-700 block mt-0.5">
                                                    Tunggakan • {{ $bData['original_bulan'] }}
                                                </span>
                                            @endif
                                        </a>
                                    @endif
                                @else
                                    <span class="text-stone-300 font-bold text-xs" title="Tidak ada tagihan jenis ini di bulan ini">-</span>
                                @endif
                            </td>
                        @endforeach

                        <!-- Total Tagihan Bulan Ini -->
                        <td class="p-3 text-right font-mono font-bold text-stone-800 border-r border-stone-200">
                            Rp {{ number_format($item['total_nominal_bulan'], 0, ',', '.') }}
                        </td>

                        <!-- Sisa Tunggakan Bulan Ini -->
                        <td class="p-3 text-right font-black border-r border-stone-200">
                            @if ($item['total_tunggakan_bulan'] > 0)
                                <span class="text-rose-700 font-mono font-bold">
                                    Rp {{ number_format($item['total_tunggakan_bulan'], 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-emerald-700 font-bold text-[11px]">
                                    Nihil (Lunas)
                                </span>
                            @endif
                        </td>

                        <!-- Status Global Bulan Ini -->
                        <td class="p-3 text-center border-r border-stone-200">
                            @if ($item['status_bulan'] === 'Lunas')
                                <x-badge variant="emerald" size="xs" :dot="true">Lunas</x-badge>
                            @elseif ($item['status_bulan'] === 'Ada Tunggakan')
                                <x-badge variant="rose" size="xs" :dot="true">Tunggakan</x-badge>
                            @else
                                <x-badge variant="stone" size="xs">-</x-badge>
                            @endif
                        </td>

                        <!-- Aksi: Matriks Siswa, Detail, Kasir Bayar -->
                        <td class="p-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <!-- Tombol Matriks 12 Bulan Per-Murid -->
                                <button 
                                    type="button" 
                                    wire:click="openSiswaMatrix({{ $item['id'] }})" 
                                    class="p-1.5 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-300 transition cursor-pointer shadow-2xs"
                                    title="Buka Matriks 12 Bulan untuk {{ $item['nama'] }}"
                                >
                                    <x-lucide-grid class="w-3.5 h-3.5" />
                                </button>

                                <!-- Link Detail Kartu Kendali -->
                                <x-button variant="secondary" size="xs" icon="file-text" href="{{ route('finance.tagihan.detail', $item['id']) }}" title="Buka Detail Tagihan">
                                    Detail
                                </x-button>

                                <!-- Kasir Bayar -->
                                @if ($item['total_tunggakan_bulan'] > 0)
                                    <x-button variant="primary" size="xs" icon="credit-card" href="{{ route('finance.input-pembayaran', ['siswa_id' => $item['id']]) }}" title="Buka Kasir">
                                        Bayar
                                    </x-button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 7 + count($jenisTagihanList) }}" class="p-12 text-center text-stone-500">
                            <x-table.empty 
                                title="Tidak Ada Data Santri Ditemukan" 
                                message="Sesuaikan filter pencarian, filter kelas, atau filter status pelunasan." 
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <!-- Footer Summary per Kolom Jenis Tagihan -->
            @if ($siswasLengkap->isNotEmpty())
                <tfoot class="bg-stone-50 border-t-2 border-stone-300 text-xs font-bold text-stone-800">
                    <tr>
                        <td colspan="3" class="p-3 text-right font-black uppercase text-stone-700 border-r border-stone-200">
                            Total Bulan {{ $selectedBulan }}:
                        </td>
                        @foreach ($jenisTagihanList as $jt)
                            @php
                                $fSum = $footerSummaryJenis[$jt->id] ?? null;
                            @endphp
                            <td class="p-2.5 text-center border-r border-stone-200">
                                @if ($fSum && $fSum['nominal'] > 0)
                                    <div class="text-[11px] font-black font-mono text-stone-900">
                                        Rp {{ number_format($fSum['nominal'], 0, ',', '.') }}
                                    </div>
                                    <div class="text-[9px] font-bold text-emerald-700">
                                        Lunas: {{ $fSum['lunas_count'] }}
                                    </div>
                                    @if ($fSum['sisa'] > 0)
                                        <div class="text-[9px] font-bold text-rose-700">
                                            Sisa: Rp {{ number_format($fSum['sisa'], 0, ',', '.') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-stone-400 font-bold text-xs">-</span>
                                @endif
                            </td>
                        @endforeach
                        <td colspan="4" class="p-3 text-stone-500 text-[11px] font-medium">
                            Rekapitulasi tagihan bulan {{ $selectedBulan }}
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <!-- 5. Paginasi Tabel Lengkap -->
    <div class="pt-2">
        {{ $siswasLengkap->links() }}
    </div>
</div>
