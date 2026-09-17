<!-- Students Table (1 Row per Student) -->
<x-table loadingTarget="search, filterBulan, filterKelas, filterJenis, filterStatus, filterPeriode, startDate, endDate, page">
    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900 text-xs">
        <tr>
            @if (!auth()->user()->isSuperAdmin2())
                <th class="w-12 p-3.5 text-center border-r border-emerald-700/60">
                    <input type="checkbox" wire:model.live="selectAll" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                </th>
            @endif
            <x-table.th class="min-w-[200px]">Identitas Siswa</x-table.th>
            <x-table.th class="w-32">Kelas</x-table.th>
            <x-table.th align="center" class="w-32">Jml Tagihan</x-table.th>
            <x-table.th align="right" class="w-36">Total Tagihan</x-table.th>
            <x-table.th align="right" class="w-36">Total Dibayar</x-table.th>
            <x-table.th align="right" class="w-36">Sisa Piutang</x-table.th>
            <x-table.th align="center" class="w-32">Status Global</x-table.th>
            <x-table.th align="center" class="w-28">Aksi</x-table.th>
        </tr>
    </thead>
    <tbody class="divide-y divide-stone-200 bg-white">
        @forelse ($students as $siswa)
            @php
                $cutoffDate = now()->endOfMonth()->toDateString();
                $totalTagihan = $siswa->tagihans->sum('nominal');
                $totalDibayar = $siswa->tagihans->sum('total_dibayar');
                
                // Pisahkan tagihan jatuh tempo s/d bulan ini vs mendatang
                $dueTagihans = $siswa->tagihans->filter(fn($t) => ($t->jatuh_tempo ? $t->jatuh_tempo->format('Y-m-d') <= $cutoffDate : true));
                $upcomingTagihans = $siswa->tagihans->filter(fn($t) => ($t->jatuh_tempo && $t->jatuh_tempo->format('Y-m-d') > $cutoffDate));
                
                $dueTagihan = $dueTagihans->sum('nominal');
                $dueDibayar = $dueTagihans->sum('total_dibayar');
                $sisaTunggakan = max(0, $dueTagihan - $dueDibayar);
                
                $upcomingTagihan = $upcomingTagihans->sum('nominal');
                $upcomingDibayar = $upcomingTagihans->sum('total_dibayar');
                $sisaMendatang = max(0, $upcomingTagihan - $upcomingDibayar);
                $sisaPiutang = max(0, $totalTagihan - $totalDibayar);

                $countTagihan = $siswa->tagihans->count();
                
                // Cek apakah ada tagihan siswa ini yang sedang dalam proses pengajuan hapus
                $pendingTagihanCount = $siswa->tagihans->whereIn('id', $pendingApprovalTagihanIds ?? [])->count();
                $hasPendingDeletion = $pendingTagihanCount > 0;
                
                $globalStatus = 'lunas';
                if ($hasPendingDeletion) {
                    $globalStatus = 'pengajuan_hapus';
                } elseif ($sisaTunggakan > 0 && $dueDibayar > 0) {
                    $globalStatus = 'sebagian';
                } elseif ($sisaTunggakan > 0 && $dueDibayar == 0) {
                    $globalStatus = 'belum_bayar';
                } elseif ($sisaTunggakan == 0 && $sisaMendatang > 0) {
                    $globalStatus = 'tertib_berjalan';
                }
            @endphp
            <tr class="{{ $hasPendingDeletion ? 'bg-amber-50/75 hover:bg-amber-100/75 border-l-4 border-l-amber-500' : 'hover:bg-stone-50' }} transition duration-150 text-xs">
                @if (!auth()->user()->isSuperAdmin2())
                    <td class="p-3.5 text-center border-r border-stone-200">
                        <input type="checkbox" wire:model.live="selectedIds" value="{{ $siswa->id }}" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                    </td>
                @endif

                <!-- Student Identity -->
                <td class="p-3.5 border-r border-stone-200">
                    <div class="font-extrabold text-stone-900 text-xs">
                        {{ $siswa->user->nama ?? '-' }}
                    </div>
                    <div class="text-[11px] text-stone-500 font-mono font-bold mt-0.5">
                        NIS: {{ $siswa->nis }}
                    </div>
                </td>

                <!-- Class -->
                <td class="p-3.5 border-r border-stone-200 font-bold text-stone-700">
                    {{ $siswa->kelas->nama_kelas ?? '-' }}
                </td>

                <!-- Number of Bills -->
                <td class="p-3.5 text-center border-r border-stone-200">
                    <span class="px-2.5 py-1 bg-stone-100 border border-stone-200 rounded-lg font-black text-stone-800 text-[11px]">
                        {{ $countTagihan }} Tagihan
                    </span>
                </td>

                <!-- Total Tagihan -->
                <td class="p-3.5 text-right font-black text-stone-900 border-r border-stone-200">
                    Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                </td>

                <!-- Total Dibayar -->
                <td class="p-3.5 text-right font-black text-emerald-700 border-r border-stone-200">
                    Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                </td>

                <!-- Sisa Piutang / Tunggakan -->
                <td class="p-3.5 text-right font-black border-r border-stone-200">
                    @if ($sisaTunggakan > 0)
                        <div class="text-xs text-rose-700 font-black">
                            Rp {{ number_format($sisaTunggakan, 0, ',', '.') }}
                        </div>
                        <span class="text-[9px] font-extrabold text-rose-600 block">Jatuh Tempo</span>
                    @else
                        <div class="text-xs text-emerald-700 font-black">
                            Lunas
                        </div>
                        <span class="text-[9px] font-bold text-stone-400 block">Bulan Ini</span>
                    @endif
                    @if ($sisaMendatang > 0)
                        <div class="text-[10px] text-stone-400 font-medium mt-0.5" title="Tagihan belum jatuh tempo (bulan depan)">
                            + Rp {{ number_format($sisaMendatang, 0, ',', '.') }} (Bln Depan)
                        </div>
                    @endif
                </td>

                <!-- Status Global -->
                <td class="p-3.5 text-center border-r border-stone-200">
                    @if ($globalStatus === 'pengajuan_hapus')
                        <x-badge variant="amber" size="xs" :dot="true">Pengajuan Hapus</x-badge>
                        @if ($pendingTagihanCount < $countTagihan)
                            <span class="text-[9px] text-amber-700 font-bold block mt-0.5">{{ $pendingTagihanCount }} diajukan</span>
                        @endif
                    @elseif ($globalStatus === 'lunas')
                        <x-badge variant="emerald" size="xs">Semua Lunas</x-badge>
                    @elseif ($globalStatus === 'tertib_berjalan')
                        <x-badge variant="emerald" size="xs">Tertib (Bulan Ini)</x-badge>
                    @elseif ($globalStatus === 'sebagian')
                        <x-badge variant="amber" size="xs">Ada Tunggakan</x-badge>
                    @else
                        <x-badge variant="rose" size="xs">Belum Lunas</x-badge>
                    @endif
                </td>

                <!-- Actions -->
                <td class="p-3.5 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        <x-button type="button" variant="outline" size="xs" icon="eye" wire:click="openQuickDetail({{ $siswa->id }})" title="Lihat Rincian Seluruh Tagihan Siswa Ini">
                            Rincian
                        </x-button>
                        <x-button variant="secondary" size="xs" icon="file-text" href="{{ route('finance.tagihan.detail', $siswa->id) }}" title="Buka Halaman Rincian Tagihan Siswa">
                            Detail
                        </x-button>
                        @if ($sisaPiutang > 0)
                            <x-button variant="primary" size="xs" icon="credit-card" href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswa->id]) }}" title="Bayar Sekarang di Kasir">
                                Bayar
                            </x-button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ !auth()->user()->isSuperAdmin2() ? 9 : 8 }}" class="py-12 text-center text-stone-400">
                    <x-table.empty title="Tidak ada data tagihan ditemukan" subtitle="Gunakan filter pencarian atau buat rilis tagihan baru." />
                </td>
            </tr>
        @endforelse
    </tbody>
</x-table>

<!-- Pagination -->
<div class="pt-2">
    {{ $students->links() }}
</div>
