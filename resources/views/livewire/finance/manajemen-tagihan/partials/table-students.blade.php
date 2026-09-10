<!-- Students Table (1 Row per Student) -->
<x-table loadingTarget="search, filterBulan, filterKelas, filterJenis, filterStatus, filterPeriode, startDate, endDate, page">
    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900 text-xs">
        <tr>
            @if ($isFounder)
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
                $totalTagihan = $siswa->tagihans->sum('nominal');
                $totalDibayar = $siswa->tagihans->sum('total_dibayar');
                $sisaPiutang = max(0, $totalTagihan - $totalDibayar);
                $countTagihan = $siswa->tagihans->count();
                
                $globalStatus = 'lunas';
                if ($sisaPiutang > 0 && $totalDibayar > 0) {
                    $globalStatus = 'sebagian';
                } elseif ($sisaPiutang > 0 && $totalDibayar == 0) {
                    $globalStatus = 'belum_bayar';
                }
            @endphp
            <tr class="hover:bg-stone-50 transition duration-150 text-xs">
                @if ($isFounder)
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

                <!-- Sisa Piutang -->
                <td class="p-3.5 text-right font-black text-rose-700 border-r border-stone-200">
                    Rp {{ number_format($sisaPiutang, 0, ',', '.') }}
                </td>

                <!-- Status Global -->
                <td class="p-3.5 text-center border-r border-stone-200">
                    @if ($globalStatus === 'lunas')
                        <x-badge variant="emerald" size="xs">Semua Lunas</x-badge>
                    @elseif ($globalStatus === 'sebagian')
                        <x-badge variant="amber" size="xs">Ada Sebagian</x-badge>
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
                <td colspan="{{ $isFounder ? 9 : 8 }}" class="py-12 text-center text-stone-400">
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
