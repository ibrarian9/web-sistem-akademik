<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Laporan Pengeluaran Keuangan" 
        subtitle="Tinjau seluruh transaksi pengeluaran operasional yayasan dalam rentang tanggal tertentu."
        badge="LAPORAN PENGELUARAN"
        badgeVariant="rose"
        icon="trending-down"
    >
        <x-slot:actions>
            <x-button variant="outline" size="sm" icon="file-text" wire:click="exportPdf">
                Ekspor PDF
            </x-button>
            <x-button variant="primary" size="sm" icon="download" href="{{ route('finance.export.pengeluaran', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank">
                Ekspor Excel (.xlsx)
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Laporan Pengeluaran Keuangan"
        :steps="[
            ['title' => 'Filter Rentang Tanggal', 'desc' => 'Tentukan periode tanggal pengeluaran yang ingin ditinjau.'],
            ['title' => 'Cetak & Ekspor PDF', 'desc' => 'Cetak dokumen laporan pengeluaran kas resmi ber-QR Code & TTD untuk arsip bendahara.'],
            ['title' => 'Kategori Operasional', 'desc' => 'Tabel merangkum beban pengadaan, gaji guru, pengajuan dana BOS, dan pemeliharaan.']
        ]"
    />

    <!-- Filters Bar -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Pencarian</label>
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari deskripsi pengeluaran..." />
            </div>
            
            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Mulai Tanggal</label>
                <input wire:model.live="startDate" type="date" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Sampai Tanggal</label>
                <input wire:model.live="endDate" type="date" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Kategori Pengeluaran</label>
                <select wire:model.live="kategori_pengeluaran_id" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Table -->
        <x-table loadingTarget="search, startDate, endDate, kategori_pengeluaran_id, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-36">Tanggal</x-table.th>
                    <x-table.th class="w-48">Kategori</x-table.th>
                    <x-table.th class="min-w-[200px]">Keterangan</x-table.th>
                    <x-table.th align="center" class="w-36">Petugas</x-table.th>
                    <x-table.th align="right" class="w-44">Jumlah Pengeluaran</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($expenditures as $e)
                    <tr class="hover:bg-rose-50/40 transition">
                        <td class="p-3.5 text-xs font-bold text-stone-900 border-r border-stone-200">{{ $e->tanggal ? $e->tanggal->format('d/m/Y') : '-' }}</td>
                        <td class="p-3.5 border-r border-stone-200">
                            <x-badge variant="rose" size="xs">
                                {{ $e->kategori->nama ?? 'Umum' }}
                            </x-badge>
                        </td>
                        <td class="p-3.5 text-xs text-stone-700 font-medium border-r border-stone-200">{{ $e->keterangan ?? '-' }}</td>
                        <td class="p-3.5 text-xs font-semibold text-stone-600 text-center border-r border-stone-200">{{ $e->petugas->nama ?? '-' }}</td>
                        <td class="p-3.5 text-xs font-black text-rose-700 text-right">Rp {{ number_format($e->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <x-table.empty :colspan="5" title="Tidak ada data pengeluaran" message="Tidak ditemukan transaksi pengeluaran pada rentang tanggal terpilih." />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $expenditures->links() }}
        </div>
    </div>
</div>
