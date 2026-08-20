<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Kas Keluar Yayasan" 
        subtitle="Pencatatan pengeluaran kas non-BOS (Operasional, Sarana Prasarana, Listrik/Air, Konsumsi, dsb.)."
        badge="PENGELUARAN YAYASAN &amp; OPERASIONAL"
        badgeVariant="rose"
        icon="arrow-up-right"
    >
        <x-slot:actions>
            <x-button variant="outline" size="md" icon="file-text" wire:click="exportPdf">
                Ekspor PDF
            </x-button>
            <x-button variant="danger-solid" size="md" icon="plus" wire:click="openCreateModal">
                Catat Kas Keluar
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Kas Keluar Yayasan"
        :steps="[
            ['title' => 'Pencatatan Pengeluaran', 'desc' => 'Klik Catat Kas Keluar untuk mendokumentasikan beban operasional, belanja ATK, sarpras, atau tagihan utilitas.'],
            ['title' => 'Filter Periode & Kategori', 'desc' => 'Gunakan filter periode (Hari ini, Kemarin, Minggu ini, Bulan ini, Custom) atau kategori belanja untuk audit cepat.'],
            ['title' => 'Aksi Massal (Bulk Delete)', 'desc' => 'Centang checkbox pada baris transaksi untuk menghapus banyak catatan sekaligus.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Total Outflow Metric Banner -->
    <x-stat-card 
        title="Akumulasi Seluruh Kas Keluar Yayasan" 
        :value="'Rp ' . number_format($totalPengeluaranKas, 0, ',', '.')" 
        icon="trending-down" 
        variant="rose" 
    />

    <!-- Table Panel (Full Width) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari keterangan pengeluaran..." />
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-xs font-bold text-stone-600 uppercase tracking-wider shrink-0">Kategori:</span>
                <select wire:model.live="filterKategori" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c['id'] }}">{{ $c['nama'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Date Range Filter Row -->
        <div class="flex items-center justify-between gap-4 border-t border-stone-100 pt-3 flex-wrap">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Periode:</span>
                <x-date-filter model="filterPeriode" startDateModel="startDate" endDateModel="endDate" />
            </div>

            @if (count($selectedIds) > 0)
                <span class="text-xs font-bold text-rose-700 bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-200">
                    {{ count($selectedIds) }} pengeluaran dipilih
                </span>
            @endif
        </div>

        <!-- Outflow Table -->
        <x-table loadingTarget="search, filterKategori, filterPeriode, startDate, endDate, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <th class="w-12 p-3.5 text-center border-r border-emerald-700/60">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                    </th>
                    <x-table.th class="w-36">Tanggal</x-table.th>
                    <x-table.th class="w-48">Kategori</x-table.th>
                    <x-table.th align="right" class="w-44">Nominal Belanja</x-table.th>
                    <x-table.th class="min-w-[200px]">Keterangan</x-table.th>
                    <x-table.th align="center" class="w-36">Petugas</x-table.th>
                    <x-table.th align="center" class="w-24">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($pengeluarans as $item)
                    <tr class="hover:bg-rose-50/30 transition {{ in_array($item->id, $selectedIds) ? 'bg-rose-50/60 font-semibold' : '' }}">
                        <td class="p-3.5 text-center border-r border-stone-200">
                            <input type="checkbox" wire:model.live="selectedIds" value="{{ $item->id }}" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                        </td>
                        <td class="p-3.5 text-stone-600 text-xs font-semibold border-r border-stone-200">
                            {{ date('d/m/Y', strtotime($item->tanggal)) }}
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <x-badge variant="rose" size="xs">
                                {{ $item->kategori->nama ?? 'Umum' }}
                            </x-badge>
                        </td>
                        <td class="p-3.5 text-right font-black text-rose-700 text-sm border-r border-stone-200">
                            Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-stone-800 text-xs border-r border-stone-200">
                            {{ $item->keterangan ?: '-' }}
                        </td>
                        <td class="p-3.5 text-center text-xs font-medium text-stone-500 border-r border-stone-200">
                            {{ $item->petugas->nama ?? 'Sistem' }}
                        </td>
                        <td class="p-3.5 text-center">
                            <x-button variant="danger" size="xs" icon="trash-2" wire:click="deleteExpense({{ $item->id }})" data-confirm="Yakin ingin menghapus catatan pengeluaran ini?" />
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="7" title="Belum ada transaksi kas keluar" message="Klik tombol Catat Kas Keluar untuk menambahkan data baru." />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $pengeluarans->links() }}
        </div>
    </div>

    <!-- Floating Bulk Actions Bar -->
    <x-bulk-actions :selectedCount="count($selectedIds)" deleteAction="bulkDelete" cancelAction="resetSelection" confirmText="Apakah Anda yakin ingin menghapus seluruh catatan pengeluaran kas yang dipilih?" />

    <!-- Floating Card Form Tambah Pengeluaran -->
    <x-floating-card 
        :show="$showCreateModal" 
        title="Catat Kas Keluar Yayasan" 
        subtitle="Dokumentasikan pengeluaran operasional, sarpras, utilitas, atau belanja sekolah."
        badge="KAS KELUAR"
        badgeVariant="rose"
        icon="plus-circle"
        maxWidth="max-w-lg"
        closeAction="closeCreateModal"
    >
        <form wire:submit.prevent="saveExpense" class="space-y-4">
            <!-- Kategori Pengeluaran -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Kategori Pengeluaran <span class="text-rose-500">*</span></label>
                <select wire:model="kategori_pengeluaran_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat['id'] }}">{{ $cat['nama'] }}</option>
                    @endforeach
                </select>
                @error('kategori_pengeluaran_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Nominal dengan Pemisah Titik & Logo Rp -->
            <x-input-currency 
                label="Nominal Pengeluaran (Rp)" 
                name="jumlah" 
                wire:model="jumlah" 
                placeholder="Contoh: 250.000" 
                required 
            />

            <!-- Tanggal -->
            <x-input 
                type="date" 
                label="Tanggal Pengeluaran" 
                name="tanggal" 
                wire:model="tanggal" 
                required 
            />

            <!-- Keterangan -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Keterangan / Rincian Belanja</label>
                <textarea wire:model="keterangan" rows="3" placeholder="Contoh: Pembelian spidol whiteboard, kertas HVS A4..." class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"></textarea>
                @error('keterangan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closeCreateModal">
                    Batal
                </x-button>
                <x-button variant="danger-solid" size="md" type="submit" loadingTarget="saveExpense">
                    Simpan Pengeluaran
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
