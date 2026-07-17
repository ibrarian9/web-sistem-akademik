<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Arus Kas & Pengeluaran</h2>
        <p class="text-sm text-stone-500">Rekam pengeluaran rutin, belanja operasional yayasan, dan laporan kas keluar.</p>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Input Form Panel -->
        <div class="lg:col-span-1 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6 h-fit">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider border-b border-stone-200 pb-2">Catat Kas Keluar</h3>
            
            <form wire:submit.prevent="saveExpense" class="space-y-4">
                <!-- Kategori Pengeluaran -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori</label>
                    <select wire:model.live="kategori_pengeluaran_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat['id'] }}">{{ $cat['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('kategori_pengeluaran_id') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Jumlah / Nominal -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Jumlah (Rp)</label>
                    <input wire:model="jumlah" type="number" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500 text-right font-bold" />
                    @error('jumlah') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Pengeluaran -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal</label>
                    <input wire:model="tanggal" type="date" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500" />
                    @error('tanggal') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Keterangan / Deskripsi -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Keterangan Pengeluaran</label>
                    <textarea wire:model="keterangan" rows="3" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500" placeholder="Belanja ATK kantor, token listrik, dll..."></textarea>
                    @error('keterangan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-red-600/10">
                        Catat Kas Keluar
                    </button>
                </div>
            </form>
        </div>

        <!-- History & Filtering List Panel -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
            <!-- Filtering Bar -->
            <div class="flex items-center justify-between bg-stone-50 p-4 border border-stone-200 rounded-2xl">
                <div class="w-full sm:w-72 space-y-1">
                    <label class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Filter Kategori</label>
                    <select wire:model.live="filterKategori" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded-lg text-stone-800 text-sm focus:ring-1 focus:ring-green-500">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat['id'] }}">{{ $cat['nama'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- List Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-stone-200">
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Keterangan</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Jumlah</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($expenses as $exp)
                            <tr class="hover:bg-stone-50">
                                <td class="py-3 text-sm font-semibold text-stone-800">{{ date('d-m-Y', strtotime($exp->tanggal)) }}</td>
                                <td class="py-3 text-sm text-stone-600 font-medium">{{ $exp->kategori->nama ?? '-' }}</td>
                                <td class="py-3 text-sm text-stone-500 max-w-xs truncate" title="{{ $exp->keterangan }}">{{ $exp->keterangan }}</td>
                                <td class="py-3 text-sm font-bold text-red-600 text-right">Rp {{ number_format($exp->jumlah, 0, ',', '.') }}</td>
                                <td class="py-3 text-sm text-stone-500 text-center">{{ $exp->petugas->nama ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-stone-400 font-medium text-sm">
                                    Belum ada catatan pengeluaran kas keluar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-4 border-t border-stone-200">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
</div>
