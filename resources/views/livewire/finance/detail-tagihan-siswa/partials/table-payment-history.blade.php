<!-- Riwayat Transaksi Pembayaran Siswa -->
<div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-stone-100 pb-3">
        <div>
            <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-tight flex items-center gap-2">
                <x-lucide-receipt class="w-4 h-4 text-emerald-600" />
                <span>Riwayat Pembayaran & Kwitansi Terakhir</span>
            </h3>
            <p class="text-xs text-stone-500">Daftar transaksi pembayaran yang pernah dicatat dan divalidasi untuk siswa ini.</p>
        </div>
        <div class="flex items-center gap-2">
            @if ($searchBayar || $filterBayarBulan || $filterBayarJenis || $filterBayarMetode || $filterBayarTahunAjaran)
                <x-button variant="secondary" size="xs" wire:click="resetBayarFilters" title="Reset Semua Filter Pembayaran">
                    Reset Filter
                </x-button>
            @endif
            <x-button variant="outline" size="xs" icon="credit-card" href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswa->id]) }}">
                Catat Pembayaran Baru
            </x-button>
        </div>
    </div>

    <!-- Filter Controls Grid Riwayat Pembayaran -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
        <!-- Search -->
        <div class="col-span-1 sm:col-span-2 md:col-span-1">
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Pencarian</label>
            <x-search-input wire:model.live.debounce.300ms="searchBayar" placeholder="Cari No. Resi atau Kasir..." />
        </div>

        <!-- Filter Bulan Pembayaran -->
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Bulan Bayar</label>
            <select wire:model.live="filterBayarBulan" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Periode</option>
                @foreach ($bulanOptions as $b)
                    <option value="{{ $b }}">{{ $b }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Jenis Kategori Tagihan -->
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Kategori Tagihan</label>
            <select wire:model.live="filterBayarJenis" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Kategori</option>
                @foreach ($jenisTagihans as $jt)
                    <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Metode Pembayaran -->
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Metode Bayar</label>
            <select wire:model.live="filterBayarMetode" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Metode</option>
                <option value="Tunai">Tunai</option>
                <option value="Transfer Bank">Transfer Bank</option>
                <option value="Deposit">Deposit</option>
                <option value="Beasiswa">Beasiswa</option>
            </select>
        </div>

        <!-- Filter Tahun Ajaran -->
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Tahun Ajaran</label>
            <select wire:model.live="filterBayarTahunAjaran" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua T.A.</option>
                @foreach ($tahunAjarans as $ta)
                    <option value="{{ $ta['id'] }}">{{ $ta['nama'] }} {{ $ta['status_aktif'] ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Payment History Table with loadingTarget & table component -->
    <x-table loadingTarget="filterBayarBulan, filterBayarJenis, filterBayarMetode, filterBayarTahunAjaran, searchBayar, pembayaranPage">
        <thead class="bg-stone-800 text-white font-extrabold uppercase tracking-wider border-b border-stone-900 text-xs">
            <tr>
                <x-table.th class="w-10 text-center">No</x-table.th>
                <x-table.th class="w-32">No Resi</x-table.th>
                <x-table.th class="w-28 text-center">Tanggal Bayar</x-table.th>
                <x-table.th class="w-44">Kategori Tagihan</x-table.th>
                <x-table.th class="w-28 text-right">Nominal Bayar</x-table.th>
                <x-table.th class="w-24 text-center">Metode</x-table.th>
                <x-table.th align="center" class="w-28">Bukti Foto</x-table.th>
                <x-table.th class="w-28">Petugas Kasir</x-table.th>
                <x-table.th align="center" class="w-28">Aksi</x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-200 bg-white">
            @forelse ($recentPayments as $pIndex => $rp)
                <tr class="hover:bg-stone-50 transition">
                    <td class="p-3.5 text-center text-xs font-mono font-bold text-stone-500 border-r border-stone-200">
                        {{ $recentPayments->firstItem() + $pIndex }}
                    </td>
                    <td class="p-3.5 font-mono font-bold text-stone-900 border-r border-stone-200">
                        {{ $rp->no_resi ?: ('RES-' . str_pad($rp->id, 5, '0', STR_PAD_LEFT)) }}
                    </td>
                    <td class="p-3.5 text-center border-r border-stone-200 text-stone-700 font-semibold text-xs whitespace-nowrap">
                        {{ $rp->tanggal_bayar ? \Carbon\Carbon::parse($rp->tanggal_bayar)->locale('id')->isoFormat('D MMM YYYY') : '-' }}
                    </td>
                    <td class="p-3.5 border-r border-stone-200 text-xs">
                        <div class="font-extrabold text-stone-800">{{ $rp->tagihan->jenisTagihan->nama ?? '-' }}</div>
                        <div class="text-[10px] text-stone-400 font-medium">Periode: {{ $rp->tagihan->bulan ?? '-' }}</div>
                    </td>
                    <td class="p-3.5 text-right font-mono font-black text-emerald-800 border-r border-stone-200 text-xs">
                        Rp {{ number_format($rp->nominal_dibayar, 0, ',', '.') }}
                    </td>
                    <td class="p-3.5 text-center border-r border-stone-200">
                        <x-badge variant="stone" size="xs">{{ $rp->metode_bayar }}</x-badge>
                    </td>
                    <td class="p-3.5 text-center border-r border-stone-200">
                        @if ($rp->bukti_bayar)
                            <button 
                                type="button" 
                                wire:click="openBuktiModal({{ $rp->id }})" 
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-lg text-[11px] font-bold shadow-2xs transition cursor-pointer"
                                title="Klik untuk melihat atau mengganti foto bukti"
                            >
                                <x-lucide-image class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                <span>Bukti Ada</span>
                            </button>
                        @else
                            <button 
                                type="button" 
                                wire:click="openBuktiModal({{ $rp->id }})" 
                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-stone-50 hover:bg-stone-100 text-stone-600 hover:text-stone-900 border border-dashed border-stone-300 rounded-lg text-[10px] font-semibold transition cursor-pointer"
                                title="Klik untuk menambahkan foto bukti transfer/struk"
                            >
                                <x-lucide-plus class="w-3 h-3 text-stone-400 shrink-0" />
                                <span>+ Bukti</span>
                            </button>
                        @endif
                    </td>
                    <td class="p-3.5 border-r border-stone-200 text-stone-600 font-medium text-xs">
                        {{ $rp->petugas->nama ?? 'Kasir Finance' }}
                    </td>
                    <td class="p-3.5 text-center">
                        <div class="flex items-center justify-center gap-1.5 flex-wrap">
                            <x-button variant="outline" size="xs" icon="printer" href="{{ route('finance.pembayaran.resi', $rp->id) }}" target="_blank" title="Cetak Kuitansi Resi">
                                Resi
                            </x-button>
                            @if ($this->isFinanceOrAdmin() && !auth()->user()->isSuperAdmin2())
                                <x-button 
                                    type="button" 
                                    variant="danger" 
                                    size="xs" 
                                    icon="trash-2" 
                                    wire:click="deletePembayaran({{ $rp->id }})" 
                                    wire:confirm="{{ auth()->user()->role?->nama === 'finance' ? 'Aksi pembatalan pembayaran oleh Keuangan membutuhkan persetujuan Super Admin atau Super Admin 2. Ajukan pembatalan pembayaran ini?' : 'Yakin ingin membatalkan dan menghapus transaksi pembayaran ini? Total terbayar dan status tagihan akan disesuaikan otomatis.' }}" 
                                    title="Batalkan Transaksi Pembayaran">
                                    Hapus
                                </x-button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="py-12 text-center text-stone-400">
                        <x-table.empty title="Tidak ada riwayat pembayaran yang sesuai" subtitle="Tidak ada transaksi pembayaran ditemukan untuk filter yang dipilih." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <!-- Pagination for Payment History -->
    <div class="pt-2">
        {{ $recentPayments->links() }}
    </div>
</div>
