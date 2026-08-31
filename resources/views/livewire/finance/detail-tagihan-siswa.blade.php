<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <x-page-header 
        :title="'Tagihan: ' . ($siswa->user->nama ?? 'Siswa')" 
        :subtitle="'NIS: ' . ($siswa->nis ?? '-') . ' • Kelas: ' . ($siswa->kelas->nama_kelas ?? '-') . ' • Wali: ' . ($siswa->nama_wali ?: '-') . ' • Kontak: ' . ($siswa->no_hp_wali ?: ($siswa->user->no_hp ?? '-'))"
        icon="file-text"
        :breadcrumbs="[
            ['label' => 'Manajemen Tagihan', 'url' => route('finance.tagihan')],
            ['label' => 'Rincian: ' . ($siswa->user->nama ?? 'Siswa')]
        ]"
    >
        <x-slot:actions>
            <div class="flex items-center gap-2 flex-wrap">
                <x-button variant="secondary" size="md" icon="arrow-left" href="{{ route('finance.tagihan') }}">
                    Kembali
                </x-button>
                <x-button variant="primary" size="md" icon="credit-card" href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswa->id]) }}">
                    Buka Kasir Siswa Ini
                </x-button>
                <x-button variant="primary" size="md" icon="plus-circle" wire:click="openCreateModal">
                    + Tambah Tagihan
                </x-button>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Alerts Notification -->
    @if (session()->has('success'))
        <x-alert-banner type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="danger" :message="session('error')" />
    @endif

    <!-- Financial Stat Cards for This Student -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Tagihan</span>
                <div class="p-2 bg-blue-50 text-blue-700 rounded-xl border border-blue-200">
                    <x-lucide-file-text class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">
                Rp {{ number_format($totalNominal, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-stone-500 font-medium">Akumulasi seluruh tagihan</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Terbayar</span>
                <div class="p-2 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200">
                    <x-lucide-check-circle class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-800">
                Rp {{ number_format($totalTerbayar, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-emerald-700 font-medium">{{ $countLunas }} tagihan lunas</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Sisa Tunggakan</span>
                <div class="p-2 bg-rose-50 text-rose-700 rounded-xl border border-rose-200">
                    <x-lucide-alert-circle class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-rose-800">
                Rp {{ number_format($totalSisa, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-rose-700 font-medium">{{ $countBelumLunas }} tagihan belum lunas</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Status Pembayaran</span>
                <div class="p-2 bg-purple-50 text-purple-700 rounded-xl border border-purple-200">
                    <x-lucide-pie-chart class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black {{ $totalSisa == 0 && $totalNominal > 0 ? 'text-emerald-800' : 'text-amber-800' }}">
                @if ($totalNominal == 0)
                    0%
                @else
                    {{ round(($totalTerbayar / max(1, $totalNominal)) * 100) }}%
                @endif
            </div>
            <div class="text-[11px] text-stone-500 font-medium">Persentase pelunasan</div>
        </div>
    </div>

    <!-- Main Invoices Table Card with Multi-Filter -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-5">
        <!-- Title & Filter Bar -->
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between flex-wrap gap-2 border-b border-stone-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-tight">Daftar Tagihan & SPP</h3>
                    <p class="text-xs text-stone-500">Filter berdasarkan bulan, kategori, atau status pelunasan.</p>
                </div>
                @if ($filterBulan || $filterJenis || $filterStatus || $filterTahunAjaran || $search)
                    <x-button type="button" variant="ghost" size="xs" icon="x" wire:click="resetFilters">
                        Reset Filter
                    </x-button>
                @endif
            </div>

            <!-- Filter Controls Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                <!-- Search -->
                <div class="col-span-1 sm:col-span-2 md:col-span-1">
                    <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Pencarian</label>
                    <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari tagihan..." />
                </div>

                <!-- Filter Bulan -->
                <div>
                    <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Filter Bulan</label>
                    <select wire:model.live="filterBulan" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">Semua Bulan / Periode</option>
                        @foreach ($bulanOptions as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Jenis Kategori Tagihan -->
                <div>
                    <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Kategori Tagihan</label>
                    <select wire:model.live="filterJenis" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">Semua Kategori</option>
                        @foreach ($jenisTagihans as $jt)
                            <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Status Bayar</label>
                    <select wire:model.live="filterStatus" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">Semua Status</option>
                        <option value="belum_bayar">Belum Bayar</option>
                        <option value="sebagian">Sebagian (Cicil)</option>
                        <option value="lunas">Lunas</option>
                    </select>
                </div>

                <!-- Filter Tahun Ajaran -->
                <div>
                    <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Tahun Ajaran</label>
                    <select wire:model.live="filterTahunAjaran" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">Semua T.A.</option>
                        @foreach ($tahunAjarans as $ta)
                            <option value="{{ $ta['id'] }}">{{ $ta['nama'] }} {{ $ta['status_aktif'] ? '(Aktif)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <x-table loadingTarget="filterBulan, filterJenis, filterStatus, filterTahunAjaran, search">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-10 text-center">No</x-table.th>
                    <x-table.th class="w-44">Kategori Tagihan</x-table.th>
                    <x-table.th class="w-28">Bulan / Periode</x-table.th>
                    <x-table.th class="w-28 text-center">Jatuh Tempo</x-table.th>
                    <x-table.th class="w-28 text-right">Nominal</x-table.th>
                    <x-table.th class="w-28 text-right">Dibayar</x-table.th>
                    <x-table.th class="w-28 text-right">Sisa</x-table.th>
                    <x-table.th class="w-24 text-center">Status</x-table.th>
                    <x-table.th align="center" class="w-36">Aksi & Resi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($tagihans as $index => $item)
                    @php
                        $sisa = max(0, $item->nominal - $item->total_dibayar);
                    @endphp
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3.5 text-center text-xs font-mono font-bold text-stone-500 border-r border-stone-200">
                            {{ $tagihans->firstItem() + $index }}
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ $item->jenisTagihan->nama ?? '-' }}</div>
                            <div class="text-[10px] text-stone-400 font-medium">T.A. {{ $item->tahunAjaran->nama ?? '-' }}</div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200 text-xs font-bold text-stone-800">
                            {{ $item->bulan ?: '-' }}
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200 text-xs font-semibold text-stone-700 whitespace-nowrap">
                            @if ($item->jatuh_tempo)
                                <span>{{ \Carbon\Carbon::parse($item->jatuh_tempo)->locale('id')->isoFormat('D MMM YYYY') }}</span>
                            @else
                                <span class="text-stone-400">-</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-right border-r border-stone-200 text-xs font-mono font-black text-stone-900">
                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-right border-r border-stone-200 text-xs font-mono font-black text-emerald-800">
                            Rp {{ number_format($item->total_dibayar, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-right border-r border-stone-200 text-xs font-mono font-black {{ $sisa > 0 ? 'text-rose-700' : 'text-stone-400' }}">
                            Rp {{ number_format($sisa, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($item->status === 'lunas')
                                <x-badge variant="emerald" size="xs">Lunas</x-badge>
                            @elseif ($item->status === 'sebagian')
                                <x-badge variant="amber" size="xs">Sebagian</x-badge>
                            @else
                                <x-badge variant="rose" size="xs">Belum Bayar</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <!-- Edit Button -->
                                <x-button 
                                    type="button" 
                                    variant="secondary" 
                                    size="xs" 
                                    icon="edit-3" 
                                    wire:click="openEditModal({{ $item->id }})" 
                                    title="Edit Tagihan">
                                    Edit
                                </x-button>

                                <!-- Delete Button (Finance & Founder) -->
                                @if ($this->isFinanceOrAdmin())
                                    <x-button 
                                        type="button" 
                                        variant="danger" 
                                        size="xs" 
                                        icon="trash-2" 
                                        wire:click="deleteTagihan({{ $item->id }})" 
                                        wire:confirm="Apakah Anda yakin ingin menghapus tagihan ini?{{ $item->total_dibayar > 0 ? ' Riwayat pembayaran terkait tagihan ini juga akan dibatalkan/dihapus otomatis.' : '' }}" 
                                        title="Hapus Tagihan">
                                        Hapus
                                    </x-button>
                                @endif

                                <!-- Print Receipt if paid -->
                                @if ($item->pembayarans && $item->pembayarans->count() > 0)
                                    <x-button 
                                        variant="outline" 
                                        size="xs" 
                                        icon="printer" 
                                        href="{{ route('finance.pembayaran.resi', $item->pembayarans->first()->id) }}" 
                                        target="_blank" 
                                        title="Cetak Kuitansi Resi">
                                        Resi
                                    </x-button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-stone-400">
                            <x-table.empty title="Tidak ada tagihan yang sesuai" subtitle="Tidak ada item tagihan yang ditemukan untuk filter yang dipilih." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $tagihans->links() }}
        </div>
    </div>

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
                <x-search-input wire:model.live.debounce.300ms="searchBayar" placeholder="Cari No. Resi / Kasir..." />
            </div>

            <!-- Filter Bulan -->
            <div>
                <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Filter Bulan</label>
                <select wire:model.live="filterBayarBulan" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Bulan / Periode</option>
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
                        <td class="p-3.5 border-r border-stone-200 text-stone-600 font-medium text-xs">
                            {{ $rp->petugas->nama ?? 'Kasir Finance' }}
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <x-button variant="outline" size="xs" icon="printer" href="{{ route('finance.pembayaran.resi', $rp->id) }}" target="_blank" title="Cetak Kuitansi Resi">
                                    Resi
                                </x-button>
                                @if ($this->isFinanceOrAdmin())
                                    <x-button 
                                        type="button" 
                                        variant="danger" 
                                        size="xs" 
                                        icon="trash-2" 
                                        wire:click="deletePembayaran({{ $rp->id }})" 
                                        wire:confirm="Yakin ingin membatalkan dan menghapus transaksi pembayaran ini? Total terbayar dan status tagihan akan disesuaikan otomatis." 
                                        title="Hapus / Batalkan Transaksi Pembayaran">
                                        Hapus
                                    </x-button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-stone-400">
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

    <!-- Modal Create Tagihan -->
    <x-floating-card 
        :show="$showCreateModal"
        title="Tambah Tagihan Baru"
        :subtitle="'Penerima: ' . ($siswa->user->nama ?? 'Siswa') . ' (' . ($siswa->kelas->nama_kelas ?? '-') . ')'"
        badge="FORM TAGIHAN"
        badgeVariant="emerald"
        icon="plus-circle"
        maxWidth="max-w-xl"
        closeAction="closeCreateModal"
    >
        <form wire:submit.prevent="createTagihan" class="space-y-4 text-xs">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Jenis Kategori Tagihan <span class="text-rose-600">*</span></label>
                <select wire:model.live="jenis_tagihan_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold p-2.5 focus:ring-2 focus:ring-emerald-600">
                    <option value="">Pilih Jenis Tagihan</option>
                    @foreach ($jenisTagihans as $jt)
                        <option value="{{ $jt['id'] }}">{{ $jt['nama'] }} (Default: Rp {{ number_format($jt['default_nominal'] ?? 0, 0, ',', '.') }})</option>
                    @endforeach
                </select>
                @error('jenis_tagihan_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Bulan / Periode <span class="text-rose-600">*</span></label>
                    <select wire:model="bulan" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold p-2.5 focus:ring-2 focus:ring-emerald-600">
                        @foreach ($bulanOptions as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                    @error('bulan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Jatuh Tempo <span class="text-rose-600">*</span></label>
                    <input type="date" wire:model="jatuh_tempo" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold p-2.5 focus:ring-2 focus:ring-emerald-600">
                    @error('jatuh_tempo') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Nominal Tagihan (Rp) <span class="text-rose-600">*</span></label>
                <input type="number" min="0" step="1000" wire:model="nominal" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold p-2.5 focus:ring-2 focus:ring-emerald-600" placeholder="Contoh: 350000 (Isi 0 jika Siswa Bebas SPP / Beasiswa)">
                <span class="text-[10px] text-stone-500 block mt-1">Jika diisi Rp 0, tagihan otomatis berstatus <strong>Lunas</strong>.</span>
                @error('nominal') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button type="button" variant="secondary" size="md" wire:click="closeCreateModal">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="check" loadingTarget="createTagihan">
                    Simpan Tagihan
                </x-button>
            </div>
        </form>
    </x-floating-card>

    <!-- Modal Edit Tagihan -->
    <x-floating-card 
        :show="$showEditModal"
        title="Edit Data Tagihan"
        subtitle="Ubah kategori, periode, jatuh tempo, atau nominal tagihan siswa."
        badge="EDIT TAGIHAN"
        badgeVariant="emerald"
        icon="edit-3"
        maxWidth="max-w-xl"
        closeAction="closeEditModal"
    >
        <form wire:submit.prevent="updateTagihan" class="space-y-4 text-xs">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Jenis Kategori Tagihan <span class="text-rose-600">*</span></label>
                <select wire:model="edit_jenis_tagihan_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold p-2.5 focus:ring-2 focus:ring-emerald-600">
                    @foreach ($jenisTagihans as $jt)
                        <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                    @endforeach
                </select>
                @error('edit_jenis_tagihan_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Bulan / Periode <span class="text-rose-600">*</span></label>
                    <select wire:model="edit_bulan" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold p-2.5 focus:ring-2 focus:ring-emerald-600">
                        @foreach ($bulanOptions as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                    @error('edit_bulan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Jatuh Tempo <span class="text-rose-600">*</span></label>
                    <input type="date" wire:model="edit_jatuh_tempo" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold p-2.5 focus:ring-2 focus:ring-emerald-600">
                    @error('edit_jatuh_tempo') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Nominal Tagihan (Rp) <span class="text-rose-600">*</span></label>
                <input type="number" min="0" step="1000" wire:model="edit_nominal" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold p-2.5 focus:ring-2 focus:ring-emerald-600">
                @if ($edit_total_dibayar > 0)
                    <span class="text-[11px] text-amber-700 font-semibold block mt-1">
                        Catatan: Siswa telah membayar Rp {{ number_format($edit_total_dibayar, 0, ',', '.') }}.
                    </span>
                @endif
                <span class="text-[10px] text-stone-500 block mt-1">Jika nominal diubah menjadi Rp 0, status tagihan otomatis menjadi <strong>Lunas</strong>.</span>
                @error('edit_nominal') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button type="button" variant="secondary" size="md" wire:click="closeEditModal">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="check" loadingTarget="updateTagihan">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
