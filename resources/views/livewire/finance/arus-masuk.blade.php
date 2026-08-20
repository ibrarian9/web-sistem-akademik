<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Buku Rekapitulasi Arus Masuk" 
        subtitle="Rekapitulasi seluruh pemasukan dana sekolah: SPP, Uang Tahunan, Pembangunan, Dana BOS, &amp; Infaq Kas Yayasan."
        badge="BUKU KAS MASUK"
        badgeVariant="emerald"
        icon="book-open"
    />

    <!-- SUMMARY CARDS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <x-stat-card 
            title="Uang SPP" 
            :value="'Rp ' . number_format($sppTotal, 0, ',', '.')" 
            icon="calendar-check" 
            variant="white" 
        />
        <x-stat-card 
            title="Uang Tahunan" 
            :value="'Rp ' . number_format($tahunanTotal, 0, ',', '.')" 
            icon="refresh-cw" 
            variant="white" 
        />
        <x-stat-card 
            title="Pembangunan" 
            :value="'Rp ' . number_format($pembangunanTotal, 0, ',', '.')" 
            icon="building" 
            variant="white" 
        />
        <x-stat-card 
            title="Dana BOS" 
            :value="'Rp ' . number_format($bosTotal, 0, ',', '.')" 
            icon="landmark" 
            variant="white" 
        />
        <x-stat-card 
            title="Total Arus Masuk" 
            :value="'Rp ' . number_format($grandTotalInflow, 0, ',', '.')" 
            icon="trending-up" 
            variant="emerald" 
        />
    </div>

    <!-- TRANSACTIONS TABLE (Full Width) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama siswa..." />
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-stone-600 uppercase tracking-wider shrink-0">Kategori:</span>
                <select wire:model.live="filterJenis" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="semua">Semua Pemasukan</option>
                    <option value="spp">Uang SPP Bulanan</option>
                    <option value="tahunan">Uang Tahunan / Registrasi</option>
                    <option value="pembangunan">Uang Pembangunan / Gedung</option>
                    <option value="lainnya">Pemasukan Lainnya</option>
                </select>
            </div>
        </div>

        <x-table loadingTarget="search, filterJenis, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-36">No. Resi / Tanggal</x-table.th>
                    <x-table.th class="min-w-[180px]">Siswa / Sumber</x-table.th>
                    <x-table.th class="w-44">Kategori Pemasukan</x-table.th>
                    <x-table.th align="center" class="w-32">Metode</x-table.th>
                    <x-table.th align="right" class="w-44">Nominal Setoran</x-table.th>
                    <x-table.th align="center" class="w-28">Aksi / Resi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($pembayarans as $p)
                    <tr class="hover:bg-emerald-50/40 transition">
                        <td class="p-3.5 border-r border-stone-200">
                            <span class="font-mono font-bold text-stone-900 block text-xs">{{ $p->no_resi ?? '-' }}</span>
                            <span class="text-stone-400 font-mono text-[10px]">{{ date('d/m/Y', strtotime($p->tanggal_bayar)) }}</span>
                        </td>
                        <td class="p-3.5 font-extrabold text-stone-900 text-xs border-r border-stone-200">
                            {{ $p->tagihan->siswa->user->nama ?? '-' }}
                            <span class="text-[10px] text-stone-400 block font-mono">NIS: {{ $p->tagihan->siswa->nis ?? '-' }}</span>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <x-badge variant="stone" size="xs">
                                {{ $p->tagihan->jenisTagihan->nama ?? '-' }}
                            </x-badge>
                        </td>
                        <td class="p-3.5 text-center text-xs font-semibold text-stone-700 border-r border-stone-200">
                            {{ $p->metode_bayar }}
                        </td>
                        <td class="p-3.5 text-right font-black text-emerald-800 text-sm border-r border-stone-200">
                            Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}
                            @if ($p->kelebihan_bayar > 0)
                                <span class="block text-[10px] text-emerald-600 font-normal">+Deposit: Rp {{ number_format($p->kelebihan_bayar, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            @if ($p->is_void)
                                <x-badge variant="rose" size="xs">VOID</x-badge>
                            @else
                                <x-button variant="outline" size="xs" icon="printer" href="{{ route('finance.pembayaran.resi', $p->id) }}" target="_blank">
                                    Resi
                                </x-button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="6" title="Belum ada riwayat transaksi pemasukan" message="Pemasukan dari kasir dan pembayaran siswa akan tercatat di sini." />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $pembayarans->links() }}
        </div>
    </div>
</div>
