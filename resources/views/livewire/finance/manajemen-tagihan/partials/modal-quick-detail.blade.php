<!-- FLOATING CARD / MODAL: RINCIAN CEPAT SELURUH TAGIHAN 1 SISWA -->
<x-floating-card 
    :show="$showQuickDetailModal" 
    title="Rincian Seluruh Tagihan Siswa" 
    :subtitle="($quickDetailSiswa->user->nama ?? 'Siswa') . ' (NIS: ' . ($quickDetailSiswa->nis ?? '-') . ' - Kelas: ' . ($quickDetailSiswa->kelas->nama_kelas ?? '-') . ')'"
    badge="SEMUA TAGIHAN"
    badgeVariant="emerald"
    icon="file-text"
    maxWidth="max-w-2xl"
    closeAction="closeQuickDetailModal"
>
    @if ($quickDetailSiswa)
        @php
            $qTotalTagihan = $quickDetailSiswa->tagihans->sum('nominal');
            $qTotalDibayar = $quickDetailSiswa->tagihans->sum('total_dibayar');
            $qSisaPiutang = max(0, $qTotalTagihan - $qTotalDibayar);
            $qCountTagihan = $quickDetailSiswa->tagihans->count();
        @endphp
        <div class="space-y-4">
            <!-- Stat Cards -->
            <div class="grid grid-cols-3 gap-2.5 text-center">
                <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl">
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Total Tagihan</span>
                    <span class="text-sm font-black text-stone-900 block mt-0.5">Rp {{ number_format($qTotalTagihan, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-stone-400 font-medium">{{ $qCountTagihan }} Tagihan</span>
                </div>
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                    <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Total Dibayar</span>
                    <span class="text-sm font-black text-emerald-800 block mt-0.5">Rp {{ number_format($qTotalDibayar, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-emerald-600 font-medium">{{ $quickDetailSiswa->tagihans->where('status', 'lunas')->count() }} Lunas</span>
                </div>
                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl">
                    <span class="text-[10px] font-bold text-rose-700 uppercase tracking-wider block">Sisa Piutang</span>
                    <span class="text-sm font-black text-rose-800 block mt-0.5">Rp {{ number_format($qSisaPiutang, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-rose-600 font-medium">{{ $quickDetailSiswa->tagihans->where('status', '!=', 'lunas')->count() }} Belum Lunas</span>
                </div>
            </div>

            <!-- Tagihan List (SPP & Non-SPP) -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-stone-800 uppercase tracking-wider">Daftar Tagihan (SPP & Non-SPP)</span>
                    <span class="text-[11px] text-stone-500 font-medium">Diurutkan kronologis</span>
                </div>

                <div class="max-h-72 overflow-y-auto border border-stone-200 rounded-xl bg-white divide-y divide-stone-100 shadow-2xs">
                    @forelse ($quickDetailSiswa->tagihans as $qt)
                        @php
                            $qtSisa = max(0, $qt->nominal - $qt->total_dibayar);
                            $isSpp = str_contains(strtoupper($qt->jenisTagihan->nama ?? ''), 'SPP');
                        @endphp
                        <div class="p-3 flex items-center justify-between gap-3 hover:bg-stone-50 transition">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-extrabold text-stone-900">{{ $qt->jenisTagihan->nama ?? '-' }}</span>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-md {{ $isSpp ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                        {{ $qt->bulan ?: '-' }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-stone-500 mt-0.5 flex items-center gap-2">
                                    <span>Jatuh Tempo: {{ $qt->jatuh_tempo ? date('d M Y', strtotime($qt->jatuh_tempo)) : '-' }}</span>
                                    <span>•</span>
                                    <span>T.A. {{ $qt->tahunAjaran->nama ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <div class="text-xs font-black text-stone-900">
                                    Rp {{ number_format($qt->nominal, 0, ',', '.') }}
                                </div>
                                <div class="mt-0.5">
                                    @if ($qt->status === 'lunas')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Lunas</span>
                                    @elseif ($qt->status === 'sebagian')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Sisa: Rp {{ number_format($qtSisa, 0, ',', '.') }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Belum Bayar</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-stone-400">
                            Siswa ini belum memiliki tagihan.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex items-center justify-between pt-3 border-t border-stone-200 gap-2">
                <x-button variant="secondary" size="sm" wire:click="closeQuickDetailModal">
                    Tutup
                </x-button>
                <div class="flex items-center gap-2">
                    <x-button variant="outline" size="sm" icon="external-link" href="{{ route('finance.tagihan.detail', $quickDetailSiswa->id) }}">
                        Halaman Detail Lengkap
                    </x-button>
                    @if ($qSisaPiutang > 0)
                        <x-button variant="primary" size="sm" icon="credit-card" href="{{ route('finance.input-pembayaran', ['siswa_id' => $quickDetailSiswa->id]) }}">
                            Bayar di Kasir
                        </x-button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</x-floating-card>
