<!-- Main Invoices Table Card with Multi-Filter -->
<div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-5">
    <!-- Category Filter Tabs (Semua, SPP, Non-SPP) -->
    <div class="flex items-center p-1 bg-stone-100 rounded-2xl border border-stone-200 gap-1">
        <button 
            type="button" 
            wire:click="setCategoryTab('all')" 
            class="flex-1 py-2 text-xs font-black rounded-xl transition flex items-center justify-center gap-2 cursor-pointer {{ $activeCategoryTab === 'all' ? 'bg-white text-emerald-800 shadow-xs' : 'text-stone-600 hover:text-stone-900' }}">
            <x-lucide-layers class="w-4 h-4" />
            <span>Semua Tagihan ({{ $countAll }})</span>
        </button>
        <button 
            type="button" 
            wire:click="setCategoryTab('spp')" 
            class="flex-1 py-2 text-xs font-black rounded-xl transition flex items-center justify-center gap-2 cursor-pointer {{ $activeCategoryTab === 'spp' ? 'bg-white text-indigo-800 shadow-xs' : 'text-stone-600 hover:text-stone-900' }}">
            <x-lucide-calendar class="w-4 h-4" />
            <span>Tagihan SPP Bulanan ({{ $countSpp }})</span>
        </button>
        <button 
            type="button" 
            wire:click="setCategoryTab('non_spp')" 
            class="flex-1 py-2 text-xs font-black rounded-xl transition flex items-center justify-center gap-2 cursor-pointer {{ $activeCategoryTab === 'non_spp' ? 'bg-white text-amber-800 shadow-xs' : 'text-stone-600 hover:text-stone-900' }}">
            <x-lucide-file-text class="w-4 h-4" />
            <span>Non-SPP dan Biaya Lainnya ({{ $countNonSpp }})</span>
        </button>
    </div>

    <!-- Title & Filter Bar -->
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between flex-wrap gap-2 border-b border-stone-100 pb-3">
            <div>
                <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-tight">
                    {{ $activeCategoryTab === 'spp' ? 'Daftar Tagihan SPP Bulanan' : ($activeCategoryTab === 'non_spp' ? 'Daftar Tagihan Non-SPP (Biaya Lainnya)' : 'Daftar Seluruh Tagihan Siswa') }}
                </h3>
                <p class="text-xs text-stone-500">Seluruh tagihan (SPP & non-SPP) terdaftar lengkap untuk siswa ini.</p>
            </div>
            @if ($filterBulan || $filterJenis || $filterStatus || $filterTahunAjaran || $search || $activeCategoryTab !== 'all')
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
                    <option value="">Semua Periode</option>
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

            <!-- Tampilkan Baris (perPage) -->
            <div>
                <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Tampilkan</label>
                <select wire:model.live="perPage" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="15">15 Baris</option>
                    <option value="25">25 Baris</option>
                    <option value="50">50 Baris</option>
                    <option value="100">100 Baris (Semua)</option>
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
                <x-table.th class="w-28">Bulan Tagihan</x-table.th>
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
                            @if (!auth()->user()->isSuperAdmin2())
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
                                        wire:confirm="{{ auth()->user()->role?->nama === 'finance' ? 'Aksi penghapusan oleh Keuangan membutuhkan persetujuan Super Admin atau Super Admin 2. Ajukan penghapusan tagihan ini?' : 'Apakah Anda yakin ingin menghapus tagihan ini?' }}" 
                                        title="Hapus Tagihan">
                                        Hapus
                                    </x-button>
                                @endif
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
