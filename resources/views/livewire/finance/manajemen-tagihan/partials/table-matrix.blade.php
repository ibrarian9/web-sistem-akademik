<!-- TABEL MATRIKS TAGIHAN & PEMBAYARAN SISWA -->
<div class="space-y-4 font-sans">
    <!-- Header Control Bar: Mode Toggle & Student Selector -->
    <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl space-y-3">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-emerald-100 text-emerald-800 rounded-xl border border-emerald-200">
                    <x-lucide-grid class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-xs font-black text-stone-900 uppercase tracking-tight">Matriks Tagihan dan Pembayaran Siswa</h3>
                    <p class="text-[11px] text-stone-600 font-medium">Pantau dan kelola rincian seluruh kategori biaya (SPP, Uang Buku, Gedung, Seragam, dll.) per bulan tahun ajaran.</p>
                </div>
            </div>

            <!-- Mode Switcher: Per Santri vs Rekapitulasi Global -->
            <div class="flex items-center gap-1.5 p-1 bg-white border border-stone-200 rounded-xl shadow-2xs">
                <button 
                    type="button" 
                    wire:click="setMatrixMode('siswa')" 
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer {{ $matrixMode === 'siswa' ? 'bg-emerald-800 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-50' }}"
                >
                    Per Santri / Siswa
                </button>
                <button 
                    type="button" 
                    wire:click="setMatrixMode('rekap')" 
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer {{ $matrixMode === 'rekap' ? 'bg-emerald-800 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-50' }}"
                >
                    Rekapitulasi Global
                </button>
            </div>
        </div>

        @if ($matrixMode === 'siswa')
            <div class="pt-2 border-t border-stone-200 flex items-center justify-between flex-wrap gap-3">
                <!-- Dropdown Pilih Siswa -->
                <div class="flex-1 min-w-[280px] max-w-md">
                    <label class="text-[10px] font-bold text-stone-600 uppercase tracking-wider block mb-1">Pilih Santri / Siswa:</label>
                    <select wire:model.live="matrixSiswaId" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        @foreach ($allStudentsForMatrix as $st)
                            <option value="{{ $st->id }}">
                                {{ $st->user->nama ?? '-' }} (NIS: {{ $st->nis }}) - Kelas {{ $st->kelas->nama_kelas ?? 'Belum Diatur' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action buttons for selected student -->
                @if ($matrixSiswaData && $matrixSiswaData['siswa'])
                    <div class="flex items-center gap-2 self-end">
                        <x-button variant="secondary" size="sm" icon="file-text" href="{{ route('finance.tagihan.detail', $matrixSiswaData['siswa']->id) }}" title="Buka Detail Tagihan">
                            Kartu Kendali Lengkap
                        </x-button>
                        @if ($matrixSiswaData['grand_tunggakan'] > 0)
                            <x-button variant="primary" size="sm" icon="credit-card" href="{{ route('finance.input-pembayaran', ['siswa_id' => $matrixSiswaData['siswa']->id]) }}" title="Bayar Sekarang di Kasir">
                                Buka Kasir Siswa Ini
                            </x-button>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Student Summary Cards (When in 'siswa' mode) -->
    @if ($matrixMode === 'siswa' && $matrixSiswaData && $matrixSiswaData['siswa'])
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-emerald-50/40 border border-emerald-200 rounded-2xl p-3.5 shadow-2xs">
            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs">
                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Identitas Santri</span>
                <div class="text-xs font-extrabold text-stone-900 mt-0.5 truncate">
                    {{ $matrixSiswaData['siswa']->user->nama ?? '-' }}
                </div>
                <span class="text-[10px] text-stone-500 font-mono">NIS: {{ $matrixSiswaData['siswa']->nis }} • Kelas {{ $matrixSiswaData['siswa']->kelas->nama_kelas ?? '-' }}</span>
            </div>

            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs">
                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Total Tagihan 1 Tahun</span>
                <div class="text-sm font-black text-stone-900 mt-0.5 font-mono">
                    Rp {{ number_format($matrixSiswaData['grand_nominal'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-stone-500">T.A. {{ $activeTA->nama ?? '-' }}</span>
            </div>

            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs">
                <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Total Telah Dibayar</span>
                <div class="text-sm font-black text-emerald-700 mt-0.5 font-mono">
                    Rp {{ number_format($matrixSiswaData['grand_dibayar'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-emerald-600 font-medium">Realisasi pembayaran</span>
            </div>

            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs">
                <span class="text-[10px] font-bold text-rose-700 uppercase tracking-wider block">Sisa Total Tunggakan</span>
                <div class="text-sm font-black text-rose-700 mt-0.5 font-mono">
                    Rp {{ number_format($matrixSiswaData['grand_tunggakan'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-rose-600 font-medium">
                    {{ $matrixSiswaData['grand_tunggakan'] > 0 ? 'Perlu Pelunasan' : 'Tertib Lunas' }}
                </span>
            </div>
        </div>
    @elseif ($matrixMode === 'rekap' && $matrixSiswaData)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-stone-50 border border-stone-200 rounded-2xl p-3.5 shadow-2xs">
            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs">
                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Total Tagihan Seluruh Siswa</span>
                <div class="text-sm font-black text-stone-900 mt-0.5 font-mono">
                    Rp {{ number_format($matrixSiswaData['grand_nominal'], 0, ',', '.') }}
                </div>
            </div>
            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs">
                <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Total Realisasi Terkumpul</span>
                <div class="text-sm font-black text-emerald-700 mt-0.5 font-mono">
                    Rp {{ number_format($matrixSiswaData['grand_dibayar'], 0, ',', '.') }}
                </div>
            </div>
            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs">
                <span class="text-[10px] font-bold text-rose-700 uppercase tracking-wider block">Total Piutang Belum Tertagih</span>
                <div class="text-sm font-black text-rose-700 mt-0.5 font-mono">
                    Rp {{ number_format($matrixSiswaData['grand_tunggakan'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    @endif

    <!-- TABEL MATRIKS -->
    <div class="overflow-x-auto rounded-2xl border border-stone-200 shadow-xs bg-white">
        <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider text-[11px] border-b border-emerald-900">
                <tr>
                    <!-- Kolom Bulan -->
                    <th class="p-3 w-32 border-r border-emerald-700/60 sticky left-0 z-10 bg-emerald-800 text-center">
                        Bulan
                    </th>

                    <!-- Kolom Kategori Tagihan -->
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
                @if ($matrixSiswaData && !empty($matrixSiswaData['months_rows']))
                    @foreach ($matrixSiswaData['months_rows'] as $mName => $rowData)
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
                                    @if ($matrixMode === 'siswa')
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
                                                    href="{{ route('finance.input-pembayaran', ['siswa_id' => $matrixSiswaData['siswa']->id, 'tagihan_id' => $cell['tagihan_id']]) }}" 
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
                                            @else
                                                <a 
                                                    href="{{ route('finance.input-pembayaran', ['siswa_id' => $matrixSiswaData['siswa']->id, 'tagihan_id' => $cell['tagihan_id']]) }}" 
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
                                                        wire:click="quickCreateTagihanForMonth({{ $jt->id }}, '{{ $mName }}', {{ $matrixSiswaData['siswa']->id }})" 
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
                                    @else
                                        <!-- Mode Rekapitulasi Global -->
                                        @if ($cell['count'] > 0)
                                            <div class="text-[11px] font-bold font-mono text-stone-900">
                                                Rp {{ number_format($cell['nominal'], 0, ',', '.') }}
                                            </div>
                                            <div class="text-[9px] font-bold text-emerald-700">
                                                {{ $cell['lunas_count'] }} Lunas
                                            </div>
                                            @if ($cell['sisa'] > 0)
                                                <div class="text-[9px] font-bold text-rose-700">
                                                    Sisa: Rp {{ number_format($cell['sisa'], 0, ',', '.') }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-stone-300 font-bold text-xs">-</span>
                                        @endif
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
                                @if ($matrixMode === 'siswa')
                                    @if ($rowData['status'] === 'Lunas')
                                        <x-badge variant="emerald" size="xs" :dot="true">Lunas</x-badge>
                                    @elseif ($rowData['status'] === 'Ada Tunggakan')
                                        <x-badge variant="rose" size="xs" :dot="true">Tunggakan</x-badge>
                                    @else
                                        <span class="text-stone-400 font-bold">-</span>
                                    @endif
                                @else
                                    @if ($rowData['sisa_tunggakan'] <= 0 && $rowData['total_nominal'] > 0)
                                        <x-badge variant="emerald" size="xs">100% Lunas</x-badge>
                                    @elseif ($rowData['total_nominal'] > 0)
                                        <x-badge variant="amber" size="xs">Ada Piutang</x-badge>
                                    @else
                                        <span class="text-stone-400 font-bold">-</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ 5 + count($jenisTagihanList) }}" class="p-12 text-center text-stone-500">
                            <x-table.empty 
                                title="Tidak Ada Data Tagihan Ditemukan" 
                                message="Pilih santri terlebih dahulu atau pastikan tagihan tahun ajaran ini telah diterbitkan." 
                            />
                        </td>
                    </tr>
                @endif
            </tbody>

            <!-- Footer: Akumulasi Per Jenis Tagihan -->
            @if ($matrixSiswaData && !empty($matrixSiswaData['footer_per_jenis']))
                <tfoot class="bg-stone-50 border-t-2 border-stone-300 text-xs font-bold text-stone-800">
                    <tr>
                        <td class="p-3 font-black uppercase text-stone-800 border-r border-stone-200 sticky left-0 bg-stone-50">
                            Total 1 Tahun:
                        </td>
                        @foreach ($jenisTagihanList as $jt)
                            @php
                                $fData = $matrixSiswaData['footer_per_jenis'][$jt->id];
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
                            Rp {{ number_format($matrixSiswaData['grand_nominal'], 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-right font-black font-mono text-emerald-700 border-r border-stone-200">
                            Rp {{ number_format($matrixSiswaData['grand_dibayar'], 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-right font-black font-mono text-rose-700 border-r border-stone-200">
                            Rp {{ number_format($matrixSiswaData['grand_tunggakan'], 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-center">
                            @if ($matrixSiswaData['grand_tunggakan'] <= 0 && $matrixSiswaData['grand_nominal'] > 0)
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
