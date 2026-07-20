<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Arus Kas Keluar (Pengeluaran)</h2>
            <p class="text-sm text-stone-500">Pencatatan dan pemantauan pengeluaran kas operasional sekolah dan yayasan.</p>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-3 flex items-center gap-3">
            <div class="p-2 bg-red-600 text-white rounded-xl">
                <x-lucide-receipt class="w-5 h-5" />
            </div>
            <div>
                <span class="text-xs font-semibold text-red-700 uppercase tracking-wider block">Total Pengeluaran Kas</span>
                <span class="text-lg font-black text-red-900">Rp {{ number_format($totalPengeluaranKas, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Guidance Card -->
    <div x-data="{ openGuide: true }" class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-4 transition-all shadow-sm">
        <div class="flex items-center justify-between cursor-pointer" @click="openGuide = !openGuide">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-sm">
                    <x-lucide-info class="w-5 h-5" />
                </div>
                <div>
                    <h4 class="text-xs font-bold text-emerald-950 uppercase tracking-wider">Petunjuk Arus Kas Keluar (Pengeluaran Operasional)</h4>
                    <p class="text-xs text-emerald-800">Catat dan pantau beban pengeluaran rutin, pemeliharaan, dan biaya operasional.</p>
                </div>
            </div>
            <button class="text-emerald-700 hover:text-emerald-900 text-xs font-semibold flex items-center gap-1">
                <span x-text="openGuide ? 'Sembunyikan' : 'Tampilkan'"></span>
                <x-lucide-chevron-down class="w-4 h-4 transition-transform" ::class="openGuide ? 'rotate-180' : ''" />
            </button>
        </div>
        <div x-show="openGuide" class="mt-3 pt-3 border-t border-emerald-200/60 grid grid-cols-1 md:grid-cols-3 gap-3 text-xs text-emerald-900">
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-receipt class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Kategori Pengeluaran:</strong> Kelompokkan pengeluaran berdasarkan Listrik/Air, Pemeliharaan, ATK, atau Gaji.</span>
            </div>
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-file-text class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Bukti Transaksi:</strong> Unggah struk / nota transaksi fisik untuk kepentingan audit keuangan internal.</span>
            </div>
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-lock class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Integritas Audit:</strong> Catatan pengeluaran tidak dapat dihapus untuk mencegah manipulasi laporan kas.</span>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Input Form Panel -->
        <div class="lg:col-span-1 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-5 h-fit">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider border-b border-stone-200 pb-2 flex items-center gap-2">
                <x-lucide-plus-circle class="w-4 h-4 text-red-600" />
                <span>Form Input Kas Keluar</span>
            </h3>
            
            <form wire:submit.prevent="saveExpense" class="space-y-4">
                <!-- Kategori Pengeluaran -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori Pengeluaran</label>
                    <select wire:model="kategori_pengeluaran_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-red-500/50 focus:border-red-500">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat['id'] }}">{{ $cat['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('kategori_pengeluaran_id') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Nominal -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Nominal Pengeluaran (Rp)</label>
                    <input type="number" wire:model="jumlah" placeholder="0" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-red-500/50 focus:border-red-500 font-bold text-right text-red-700" />
                    @error('jumlah') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal Pengeluaran</label>
                    <input type="date" wire:model="tanggal" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-red-500/50 focus:border-red-500" />
                    @error('tanggal') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Keterangan -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Keterangan / Rincian</label>
                    <textarea wire:model="keterangan" rows="3" placeholder="Contoh: Pembelian spidol dan ATK kelas, Bayar listrik bulan Juli..." class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-red-500/50 focus:border-red-500"></textarea>
                    @error('keterangan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-red-600/10 flex items-center justify-center gap-2">
                        <x-lucide-check-circle class="w-4 h-4" />
                        <span>Simpan Kas Keluar</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Table List Panel -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="w-full sm:w-64 relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari keterangan..." 
                        class="w-full pl-9 pr-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-red-500/50 focus:border-red-500" />
                    <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" />
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Kategori:</span>
                    <select wire:model.live="filterKategori" class="px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-red-500">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat['id'] }}">{{ $cat['nama'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-stone-200 text-xs font-semibold text-stone-500 uppercase tracking-wider">
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3">Kategori</th>
                            <th class="pb-3">Keterangan</th>
                            <th class="pb-3 text-right">Nominal</th>
                            <th class="pb-3 text-center">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-sm">
                        @forelse ($pengeluarans as $item)
                            <tr class="hover:bg-stone-50">
                                <td class="py-3 text-xs font-semibold text-stone-700 whitespace-nowrap">
                                    {{ date('d M Y', strtotime($item->tanggal)) }}
                                </td>
                                <td class="py-3">
                                    <span class="px-2.5 py-1 bg-red-50 text-red-800 border border-red-200 rounded-lg text-xs font-bold inline-block">
                                        {{ $item->kategori->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3 text-stone-700 text-xs max-w-xs">
                                    {{ $item->keterangan ?: '-' }}
                                </td>
                                <td class="py-3 text-right font-black text-red-700">
                                    Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-center text-xs text-stone-500">
                                    {{ $item->petugas->nama ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-stone-400 font-medium text-sm">
                                    Belum ada data pengeluaran kas operasional.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2 border-t border-stone-200">
                {{ $pengeluarans->links() }}
            </div>
        </div>
    </div>
</div>
