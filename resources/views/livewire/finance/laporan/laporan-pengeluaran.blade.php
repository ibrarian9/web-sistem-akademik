<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Laporan Pengeluaran</h2>
            <p class="text-sm text-stone-500">Tinjau seluruh transaksi pengeluaran operasional yayasan dalam rentang tanggal tertentu.</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="exportPdf" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold transition flex items-center gap-2 shadow-md shadow-red-600/10">
                <x-lucide-file-text class="w-4 h-4" />
                <span>Ekspor PDF</span>
            </button>
            <button wire:click="exportCsv" class="px-4 py-2.5 bg-stone-800 hover:bg-stone-900 text-white rounded-xl text-sm font-bold transition flex items-center gap-2 shadow-md shadow-stone-800/10">
                <x-lucide-download class="w-4 h-4" />
                <span>Ekspor CSV</span>
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider block mb-1">Cari Keterangan</label>
            <input wire:model.live="search" type="text" placeholder="Cari deskripsi pengeluaran..." class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-green-500/50" />
        </div>
        
        <div>
            <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider block mb-1">Mulai Tanggal</label>
            <input wire:model.live="startDate" type="date" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-green-500/50" />
        </div>

        <div>
            <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider block mb-1">Sampai Tanggal</label>
            <input wire:model.live="endDate" type="date" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-green-500/50" />
        </div>

        <div>
            <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider block mb-1">Kategori Pengeluaran</label>
            <select wire:model.live="kategori_pengeluaran_id" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-green-500/50">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-stone-200">
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Keterangan</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Petugas</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Jumlah Pengeluaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($expenditures as $e)
                        <tr class="hover:bg-stone-50">
                            <td class="py-3.5 text-sm font-semibold text-stone-800">{{ $e->tanggal ? $e->tanggal->format('d-m-Y') : '-' }}</td>
                            <td class="py-3.5 text-sm text-stone-600">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-stone-100 text-stone-700">
                                    {{ $e->kategori->nama ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 text-sm text-stone-700 max-w-sm">{{ $e->keterangan ?? '-' }}</td>
                            <td class="py-3.5 text-sm text-stone-600">{{ $e->petugas->nama ?? '-' }}</td>
                            <td class="py-3.5 text-sm font-bold text-red-600 text-right">Rp {{ number_format($e->jumlah, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-stone-400 font-medium text-sm">
                                Tidak ada data transaksi pengeluaran yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-stone-200">
            {{ $expenditures->links() }}
        </div>
    </div>
</div>
