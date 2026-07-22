<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pencatatan Arus Kas Yayasan"
        :steps="[
            ['title' => 'Pilih Mode Kas', 'desc' => 'Gunakan tombol tab di pojok kanan atas untuk beralih antara Kas Keluar (Pengeluaran) dan Kas Masuk (Infaq/Non-SPP).'],
            ['title' => 'Input Transaksi', 'desc' => 'Isi kategori, nominal, tanggal, dan deskripsi pengeluaran atau donasi masuk.'],
            ['title' => 'Filter Riwayat', 'desc' => 'Gunakan filter kategori untuk mengelompokkan pencatatan transaksi kas per periode.']
        ]"
    />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Arus Kas Operasional Yayasan</h2>
            <p class="text-sm text-stone-500">Catat dan pantau pengeluaran operasional serta pemasukan kas non-SPP (Infaq, Sedekah Subuh, Maghrib Mengaji, dll.).</p>
        </div>

        <!-- Mode Toggle Tabs -->
        <div class="flex bg-stone-200/70 p-1 rounded-2xl text-xs font-bold w-fit">
            <button type="button" wire:click="setType('pengeluaran')" class="px-4 py-2 rounded-xl transition flex items-center gap-1.5 {{ $type === 'pengeluaran' ? 'bg-white text-red-700 shadow-sm font-black' : 'text-stone-600 hover:text-stone-900' }}">
                <x-lucide-arrow-up-right class="w-4 h-4 text-red-600" />
                <span>Kas Keluar (Pengeluaran)</span>
            </button>
            <button type="button" wire:click="setType('pemasukan')" class="px-4 py-2 rounded-xl transition flex items-center gap-1.5 {{ $type === 'pemasukan' ? 'bg-white text-emerald-700 shadow-sm font-black' : 'text-stone-600 hover:text-stone-900' }}">
                <x-lucide-arrow-down-left class="w-4 h-4 text-emerald-600" />
                <span>Kas Masuk (Infaq &amp; Non-SPP)</span>
            </button>
        </div>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-stone-400 uppercase tracking-wider block">Total Pemasukan Kas Non-SPP</span>
                <span class="text-xl font-black text-emerald-700 block mt-1">Rp {{ number_format($totalPemasukanKas, 0, ',', '.') }}</span>
            </div>
            <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-600">
                <x-lucide-heart-handshake class="w-6 h-6" />
            </div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-stone-400 uppercase tracking-wider block">Total Pengeluaran Kas Operasional</span>
                <span class="text-xl font-black text-red-600 block mt-1">Rp {{ number_format($totalPengeluaranKas, 0, ',', '.') }}</span>
            </div>
            <div class="p-3 bg-red-50 rounded-2xl text-red-600">
                <x-lucide-receipt class="w-6 h-6" />
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Input Form Panel -->
        <div class="lg:col-span-1 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6 h-fit">
            @if ($type === 'pengeluaran')
                <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider border-b border-stone-200 pb-2 flex items-center gap-2">
                    <x-lucide-arrow-up-right class="w-4 h-4 text-red-600" />
                    <span>Catat Kas Keluar</span>
                </h3>
                
                <form wire:submit.prevent="saveExpense" class="space-y-4">
                    <!-- Kategori Pengeluaran -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori</label>
                        <select wire:model.live="kategori_pengeluaran_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-red-500/50 focus:border-red-500">
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
                        <input wire:model="jumlah" type="number" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-red-500/50 focus:border-red-500 text-right font-bold" />
                        @error('jumlah') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Pengeluaran -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal</label>
                        <input wire:model="tanggal" type="date" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-red-500/50 focus:border-red-500" />
                        @error('tanggal') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Keterangan / Deskripsi -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Keterangan Pengeluaran</label>
                        <textarea wire:model="keterangan" rows="3" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-red-500/50 focus:border-red-500" placeholder="Belanja ATK kantor, token listrik, dll..."></textarea>
                        @error('keterangan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-red-600/10">
                            Catat Kas Keluar
                        </button>
                    </div>
                </form>
            @else
                <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider border-b border-stone-200 pb-2 flex items-center gap-2">
                    <x-lucide-arrow-down-left class="w-4 h-4 text-emerald-600" />
                    <span>Catat Kas Masuk (Non-SPP)</span>
                </h3>
                
                <form wire:submit.prevent="saveIncome" class="space-y-4">
                    <!-- Kategori Pemasukan -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Sumber / Kategori Pemasukan</label>
                        <select wire:model.live="kategori_pemasukan" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500">
                            @foreach ($pemasukanCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('kategori_pemasukan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jumlah / Nominal -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Jumlah (Rp)</label>
                        <input wire:model="jumlah_pemasukan" type="number" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-right font-bold text-emerald-700" />
                        @error('jumlah_pemasukan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Pemasukan -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal</label>
                        <input wire:model="tanggal_pemasukan" type="date" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                        @error('tanggal_pemasukan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Keterangan / Deskripsi -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Keterangan / Catatan</label>
                        <textarea wire:model="keterangan_pemasukan" rows="3" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" placeholder="Infaq Jumat, Sedekah Subuh Jamaah, Donasi Alumni, dll..."></textarea>
                        @error('keterangan_pemasukan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-emerald-600/10">
                            Catat Kas Masuk (Non-SPP)
                        </button>
                    </div>
                </form>
            @endif
        </div>

        <!-- History & Filtering List Panel -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
            <!-- Filtering Bar -->
            <div class="flex items-center justify-between bg-stone-50 p-4 border border-stone-200 rounded-2xl">
                <div class="w-full sm:w-72 space-y-1">
                    <label class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Filter Kategori</label>
                    @if ($type === 'pengeluaran')
                        <select wire:model.live="filterKategori" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded-lg text-stone-800 text-sm focus:ring-1 focus:ring-red-500">
                            <option value="">Semua Kategori Pengeluaran</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat['id'] }}">{{ $cat['nama'] }}</option>
                            @endforeach
                        </select>
                    @else
                        <select wire:model.live="filterKategoriPemasukan" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded-lg text-stone-800 text-sm focus:ring-1 focus:ring-emerald-500">
                            <option value="">Semua Kategori Pemasukan</option>
                            @foreach ($pemasukanCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    @endif
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
                        @if ($type === 'pengeluaran')
                            @forelse ($items as $exp)
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
                        @else
                            @forelse ($items as $inc)
                                <tr class="hover:bg-stone-50">
                                    <td class="py-3 text-sm font-semibold text-stone-800">{{ date('d-m-Y', strtotime($inc->tanggal)) }}</td>
                                    <td class="py-3 text-sm text-stone-600 font-medium">
                                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold uppercase">
                                            {{ $inc->kategori }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-sm text-stone-500 max-w-xs truncate" title="{{ $inc->keterangan }}">{{ $inc->keterangan ?: '-' }}</td>
                                    <td class="py-3 text-sm font-black text-emerald-600 text-right">Rp {{ number_format($inc->jumlah, 0, ',', '.') }}</td>
                                    <td class="py-3 text-sm text-stone-500 text-center">{{ $inc->petugas->nama ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-stone-400 font-medium text-sm">
                                        Belum ada catatan pemasukan kas non-SPP.
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-4 border-t border-stone-200">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>

