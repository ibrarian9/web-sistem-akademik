<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Tata Kelola Dana BOS (Bantuan Operasional Sekolah)" 
        subtitle="Pencatatan dana BOS reguler/kinerja dari pemerintah, terpisah dari kas yayasan sekolah."
        badge="DANA BOS KEMDIKBUD"
        badgeVariant="sky"
        icon="landmark"
    >
        <x-slot:actions>
            <x-button variant="primary" size="md" icon="plus" wire:click="openCreateModal('masuk')">
                Catat Penerimaan BOS
            </x-button>
            <x-button variant="danger-solid" size="md" icon="plus" wire:click="openCreateModal('keluar')">
                Catat Belanja BOS
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- 3-Stat Summary Card Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-stat-card 
            title="Total Penerimaan Dana BOS" 
            :value="'Rp ' . number_format($totalMasuk, 0, ',', '.')" 
            icon="arrow-down-left" 
            variant="emerald" 
        />
        <x-stat-card 
            title="Total Realisasi Belanja BOS" 
            :value="'Rp ' . number_format($totalKeluar, 0, ',', '.')" 
            icon="arrow-up-right" 
            variant="rose" 
        />
        <x-stat-card 
            title="Sisa Saldo Kas Dana BOS" 
            :value="'Rp ' . number_format($saldoBos, 0, ',', '.')" 
            icon="wallet" 
            variant="sky" 
        />
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Tata Kelola Dana BOS"
        :steps="[
            ['title' => 'Pencatatan Terpisah', 'desc' => 'Seluruh pencairan dan realisasi belanja BOS tercatat terpisah dengan kas yayasan demi kepatuhan RKAS.'],
            ['title' => 'Filter Periode & Tab', 'desc' => 'Gunakan tab selector (Semua, Penerimaan, Belanja) dan filter periode (Hari ini, Kemarin, Minggu ini, Bulan ini, Custom).'],
            ['title' => 'Aksi Massal (Bulk Delete)', 'desc' => 'Centang checkbox pada baris transaksi untuk menghapus banyak catatan sekaligus.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Main Table Panel (Full Width) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Tab Selector & Search Row -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Tab Buttons -->
            <div class="flex items-center p-1 bg-stone-100 border border-stone-200 rounded-xl w-fit">
                <button type="button" 
                    wire:click="selectTab('semua')" 
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition {{ $filterJenis === 'semua' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-500 hover:text-stone-900' }}">
                    Semua Transaksi
                </button>
                <button type="button" 
                    wire:click="selectTab('masuk')" 
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $filterJenis === 'masuk' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-stone-500 hover:text-stone-900' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                    <span>Penerimaan</span>
                </button>
                <button type="button" 
                    wire:click="selectTab('keluar')" 
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $filterJenis === 'keluar' ? 'bg-rose-600 text-white shadow-2xs' : 'text-stone-500 hover:text-stone-900' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-300"></span>
                    <span>Belanja BOS</span>
                </button>
            </div>

            <!-- Search Bar -->
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari rincian atau kategori belanja BOS..." />
            </div>
        </div>

        <!-- Date Range Filter Row -->
        <div class="flex items-center justify-between gap-4 border-t border-stone-100 pt-3 flex-wrap">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Periode:</span>
                <x-date-filter model="filterPeriode" startDateModel="startDate" endDateModel="endDate" />
            </div>

            @if (count($selectedIds) > 0)
                <span class="text-xs font-bold text-sky-700 bg-sky-50 px-3 py-1.5 rounded-xl border border-sky-200">
                    {{ count($selectedIds) }} transaksi dipilih
                </span>
            @endif
        </div>

        <!-- Table -->
        <x-table loadingTarget="search, filterJenis, filterPeriode, startDate, endDate, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <th class="w-12 p-3.5 text-center border-r border-emerald-700/60">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                    </th>
                    <x-table.th class="w-32">Tanggal</x-table.th>
                    <x-table.th align="center" class="w-36">Jenis</x-table.th>
                    <x-table.th class="w-48">Kategori / Rekening</x-table.th>
                    <x-table.th align="right" class="w-44">Nominal (Rp)</x-table.th>
                    <x-table.th class="min-w-[200px]">Keterangan / Rincian Belanja</x-table.th>
                    <x-table.th align="center" class="w-24">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($transactions as $t)
                    <tr class="hover:bg-emerald-50/40 transition {{ in_array($t->id, $selectedIds) ? 'bg-sky-50/60 font-semibold' : '' }}">
                        <td class="p-3.5 text-center border-r border-stone-200">
                            <input type="checkbox" wire:model.live="selectedIds" value="{{ $t->id }}" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                        </td>
                        <td class="p-3.5 text-stone-600 text-xs font-semibold border-r border-stone-200">
                            {{ date('d/m/Y', strtotime($t->tanggal)) }}
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($t->jenis === 'masuk')
                                <x-badge variant="emerald" size="xs" :dot="true">Penerimaan</x-badge>
                            @else
                                <x-badge variant="rose" size="xs" :dot="true">Belanja</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 font-bold text-stone-900 text-xs border-r border-stone-200">
                            {{ $t->kategori }}
                        </td>
                        <td class="p-3.5 text-right font-black text-sm border-r border-stone-200 {{ $t->jenis === 'masuk' ? 'text-emerald-800' : 'text-rose-700' }}">
                            {{ $t->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($t->nominal, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-stone-800 text-xs border-r border-stone-200">
                            {{ $t->keterangan }}
                        </td>
                        <td class="p-3.5 text-center">
                            <x-button variant="danger" size="xs" icon="trash-2" wire:click="deleteTransaction({{ $t->id }})" wire:confirm="Yakin ingin menghapus catatan transaksi Dana BOS ini?" />
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="7" title="Belum ada transaksi Dana BOS" message="Gunakan tombol di atas untuk mencatat penerimaan atau belanja dana BOS." />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Floating Bulk Actions Bar -->
    <x-bulk-actions :selectedCount="count($selectedIds)" deleteAction="bulkDelete" cancelAction="resetSelection" confirmText="Apakah Anda yakin ingin menghapus seluruh catatan transaksi Dana BOS yang dipilih?" />

    <!-- Floating Card Form Dana BOS (Masuk / Keluar) -->
    <x-floating-card 
        :show="$showCreateModal" 
        :title="$jenis === 'masuk' ? 'Catat Penerimaan Dana BOS' : 'Catat Belanja / Realisasi BOS'" 
        subtitle="Pencatatan pos anggaran bantuan operasional sekolah sesuai RKAS."
        :badge="$jenis === 'masuk' ? 'PENERIMAAN' : 'BELANJA BOS'"
        :badgeVariant="$jenis === 'masuk' ? 'emerald' : 'rose'"
        icon="landmark"
        maxWidth="max-w-lg"
        closeAction="closeCreateModal"
    >
        <form wire:submit.prevent="saveTransaction" class="space-y-4">
            <!-- Jenis Selector Toggle in Modal -->
            <div class="grid grid-cols-2 gap-2 p-1 bg-stone-100 rounded-xl">
                <button type="button" 
                    wire:click="$set('jenis', 'masuk')" 
                    class="py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 {{ $jenis === 'masuk' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <x-lucide-arrow-down-left class="w-4 h-4" />
                    <span>Penerimaan BOS</span>
                </button>
                <button type="button" 
                    wire:click="$set('jenis', 'keluar')" 
                    class="py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 {{ $jenis === 'keluar' ? 'bg-rose-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <x-lucide-arrow-up-right class="w-4 h-4" />
                    <span>Belanja BOS</span>
                </button>
            </div>

            <!-- Kategori Belanja / Pos Penerimaan -->
            <x-input 
                label="Kategori / Pos Anggaran BOS" 
                name="kategori" 
                wire:model="kategori" 
                placeholder="{{ $jenis === 'masuk' ? 'Contoh: BOS Reguler Tahap 1' : 'Contoh: Belanja Buku Teks Pelajaran Kurikulum Merdeka' }}" 
                required 
            />

            <!-- Nominal dengan Pemisah Titik & Logo Rp -->
            <x-input-currency 
                label="Nominal Transaksi (Rp)" 
                name="nominal" 
                wire:model="nominal" 
                placeholder="Contoh: 5.000.000" 
                required 
            />

            <!-- Tanggal -->
            <x-input 
                type="date" 
                label="Tanggal Transaksi" 
                name="tanggal" 
                wire:model="tanggal" 
                required 
            />

            <!-- Keterangan / Rincian -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Keterangan / Rincian Transaksi</label>
                <textarea wire:model="keterangan" rows="3" placeholder="Contoh: Pembelian 120 eksemplar buku paket matematika dari penyedia resmi Siplah..." class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"></textarea>
                @error('keterangan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closeCreateModal">
                    Batal
                </x-button>
                <x-button variant="{{ $jenis === 'masuk' ? 'primary' : 'danger-solid' }}" size="md" type="submit" loadingTarget="saveTransaction">
                    Simpan Transaksi BOS
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
