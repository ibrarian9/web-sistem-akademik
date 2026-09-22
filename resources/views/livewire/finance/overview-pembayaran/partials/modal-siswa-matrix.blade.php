<!-- MODAL / FLOATING CARD: MATRIKS TAGIHAN 12 BULAN PER-MURID (Y = Bulan, X = Jenis Tagihan) -->
<div 
    class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
    x-data 
    x-on:keydown.escape.window="$wire.closeSiswaMatrix()"
>
    <!-- Modal Card Box -->
    <div class="bg-white rounded-2xl shadow-2xl border border-stone-200 w-full max-w-5xl overflow-hidden flex flex-col max-h-[92vh] animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Modal Header -->
        <div class="px-5 py-4 bg-emerald-800 text-white flex items-center justify-between border-b border-emerald-900">
            <div class="flex items-center gap-2.5">
                <div class="p-2 rounded-xl bg-white/10 text-white border border-white/20">
                    <x-lucide-calendar class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-black uppercase tracking-tight">
                        Matriks Tagihan Tahunan Siswa
                    </h3>
                    <p class="text-xs text-emerald-100 font-medium mt-0.5">
                        {{ $siswaMatrixData['siswa']->user->nama ?? '-' }} (NIS: {{ $siswaMatrixData['siswa']->nis }}) : Kelas {{ $siswaMatrixData['siswa']->kelas->nama_kelas ?? 'Belum Diatur' }}
                    </p>
                </div>
            </div>

            <!-- Close Button -->
            <button 
                type="button" 
                wire:click="closeSiswaMatrix" 
                class="p-2 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer"
                title="Tutup Jendela"
            >
                <x-lucide-x class="w-5 h-5" />
            </button>
        </div>

        <!-- Student Summary Highlight Banner -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 bg-stone-50 border-b border-stone-200">
            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs">
                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Total Tagihan 1 Tahun</span>
                <div class="text-sm font-black text-stone-900 mt-0.5 font-mono">
                    Rp {{ number_format($siswaMatrixData['grand_nominal'], 0, ',', '.') }}
                </div>
            </div>
            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs">
                <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Total Telah Dibayar</span>
                <div class="text-sm font-black text-emerald-700 mt-0.5 font-mono">
                    Rp {{ number_format($siswaMatrixData['grand_dibayar'], 0, ',', '.') }}
                </div>
            </div>
            <div class="bg-white border border-stone-200 rounded-xl p-3 shadow-2xs">
                <span class="text-[10px] font-bold text-rose-700 uppercase tracking-wider block">Sisa Total Tunggakan</span>
                <div class="text-sm font-black text-rose-700 mt-0.5 font-mono">
                    Rp {{ number_format($siswaMatrixData['grand_tunggakan'], 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Table Scroll Container: Y = 12 Bulan, X = Jenis Tagihan -->
        <div class="overflow-auto flex-1 p-4">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-stone-100 text-stone-800 font-extrabold uppercase tracking-wider text-[11px] border-b border-stone-200 sticky top-0 z-10">
                    <tr>
                        <!-- Kolom Bulan Tagihan -->
                        <th class="p-3 w-32 border-r border-stone-200 sticky left-0 z-20 bg-stone-100">Bulan (Periode)</th>
                        
                        <!-- Kolom Jenis Tunggakan -->
                        @foreach ($jenisTagihanList as $jt)
                            <th class="p-2.5 text-center min-w-[110px] border-r border-stone-200">
                                <span class="block font-black">{{ $jt->nama }}</span>
                                <span class="text-[9px] font-normal text-stone-500 block capitalize">({{ $jt->kategori }})</span>
                            </th>
                        @endforeach

                        <th class="p-3 w-28 text-right border-r border-stone-200">Total Bln Ini</th>
                        <th class="p-3 w-28 text-right border-r border-stone-200">Dibayar</th>
                        <th class="p-3 w-28 text-right border-r border-stone-200">Sisa</th>
                        <th class="p-3 w-28 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @foreach ($siswaMatrixData['months_rows'] as $mName => $rowData)
                        <tr class="hover:bg-emerald-50/30 transition text-xs">
                            <!-- Bulan (Y) -->
                            <td class="p-3 font-extrabold text-stone-900 border-r border-stone-200 sticky left-0 bg-white">
                                <span class="px-2 py-0.5 rounded-md bg-stone-100 border border-stone-200 font-bold">
                                    {{ $mName }}
                                </span>
                            </td>

                            <!-- Kolom Tagihan Siswa -->
                            @foreach ($jenisTagihanList as $jt)
                                @php
                                    $cell = $rowData['bills'][$jt->id];
                                @endphp
                                <td class="p-2 text-center border-r border-stone-200">
                                    @if ($cell['has_tagihan'])
                                        @if ($cell['status'] === 'lunas')
                                            <span 
                                                class="inline-flex items-center justify-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs"
                                                title="Lunas Rp {{ number_format($cell['nominal'], 0, ',', '.') }} {{ $cell['terakhir_bayar'] ? 'Tgl ' . $cell['terakhir_bayar'] : '' }}"
                                            >
                                                <x-lucide-check class="w-3 h-3 text-emerald-600 shrink-0 mr-1" />
                                                <span>Lunas</span>
                                            </span>
                                        @elseif ($cell['status'] === 'sebagian')
                                            <a 
                                                href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswaMatrixData['siswa']->id, 'tagihan_id' => $cell['tagihan_id']]) }}"
                                                wire:navigate
                                                class="group inline-flex items-center justify-center px-1.5 py-0.5 rounded-lg text-[10px] font-bold bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 hover:border-amber-300 shadow-2xs transition cursor-pointer"
                                                title="Klik untuk langsung bayar tagihan {{ $jt->nama }} di Kasir"
                                            >
                                                <x-lucide-clock class="w-3 h-3 text-amber-600 shrink-0 mr-1" />
                                                <span>Cicil</span>
                                            </a>
                                        @else
                                            <a 
                                                href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswaMatrixData['siswa']->id, 'tagihan_id' => $cell['tagihan_id']]) }}"
                                                wire:navigate
                                                class="group inline-flex items-center justify-center px-1.5 py-0.5 rounded-lg text-[10px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-200 hover:border-rose-300 shadow-2xs transition cursor-pointer"
                                                title="Klik untuk langsung bayar tagihan {{ $jt->nama }} di Kasir"
                                            >
                                                <x-lucide-credit-card class="w-3 h-3 text-rose-600 shrink-0 mr-1" />
                                                <span>Bayar</span>
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-stone-300 font-bold text-xs">-</span>
                                    @endif
                                </td>
                            @endforeach

                            <!-- Row Totals -->
                            <td class="p-3 text-right font-mono font-bold text-stone-800 border-r border-stone-200">
                                Rp {{ number_format($rowData['total_nominal'], 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-right font-mono font-bold text-emerald-700 border-r border-stone-200">
                                Rp {{ number_format($rowData['total_dibayar'], 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-right font-mono font-black border-r border-stone-200">
                                @if ($rowData['sisa_tunggakan'] > 0)
                                    <span class="text-rose-700">
                                        Rp {{ number_format($rowData['sisa_tunggakan'], 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-emerald-700 text-[11px] font-bold">Lunas</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                @if ($rowData['status'] === 'Lunas')
                                    <x-badge variant="emerald" size="xs">Lunas</x-badge>
                                @elseif ($rowData['status'] === 'Ada Tunggakan')
                                    <x-badge variant="rose" size="xs">Tunggakan</x-badge>
                                @else
                                    <span class="text-stone-400 font-bold">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <!-- Footer: Akumulasi Per Jenis Tagihan -->
                <tfoot class="bg-stone-100 border-t-2 border-stone-300 text-xs font-bold text-stone-900 sticky bottom-0 z-10">
                    <tr>
                        <td class="p-3 font-black uppercase text-stone-800 border-r border-stone-200 sticky left-0 bg-stone-100">
                            Total 1 Tahun:
                        </td>
                        @foreach ($jenisTagihanList as $jt)
                            @php
                                $fData = $siswaMatrixData['footer_per_jenis'][$jt->id];
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
                            Rp {{ number_format($siswaMatrixData['grand_nominal'], 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-right font-black font-mono text-emerald-700 border-r border-stone-200">
                            Rp {{ number_format($siswaMatrixData['grand_dibayar'], 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-right font-black font-mono text-rose-700 border-r border-stone-200">
                            Rp {{ number_format($siswaMatrixData['grand_tunggakan'], 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-center">
                            @if ($siswaMatrixData['grand_tunggakan'] <= 0 && $siswaMatrixData['grand_nominal'] > 0)
                                <x-badge variant="emerald" size="xs">Lunas 100%</x-badge>
                            @else
                                <x-badge variant="rose" size="xs">Belum Lunas</x-badge>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Modal Footer Actions -->
        <div class="px-5 py-3.5 bg-stone-50 border-t border-stone-200 flex items-center justify-between flex-wrap gap-2">
            <div class="text-xs text-stone-600 font-medium">
                Siswa: <span class="font-bold text-stone-900">{{ $siswaMatrixData['siswa']->user->nama ?? '-' }}</span>
            </div>

            <div class="flex items-center gap-2">
                <x-button variant="secondary" size="sm" icon="file-text" href="{{ route('finance.tagihan.detail', $siswaMatrixData['siswa']->id) }}" title="Buka Detail Tagihan">
                    Kartu Kendali Lengkap
                </x-button>

                @if ($siswaMatrixData['grand_tunggakan'] > 0)
                    <x-button variant="primary" size="sm" icon="credit-card" href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswaMatrixData['siswa']->id]) }}" title="Buka Kasir">
                        Bayar di Kasir
                    </x-button>
                @endif

                <x-button variant="stone" size="sm" wire:click="closeSiswaMatrix">
                    Tutup
                </x-button>
            </div>
        </div>
    </div>
</div>
