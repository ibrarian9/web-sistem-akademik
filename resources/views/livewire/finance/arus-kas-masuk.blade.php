<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Arus Kas Masuk (Non-SPP)</h2>
            <p class="text-sm text-stone-500">Pencatatan dan pemantauan arus kas masuk khusus Infaq, Sedekah Subuh, Maghrib Mengaji, Donasi, dll.</p>
        </div>

        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-3 flex items-center gap-3">
            <div class="p-2 bg-emerald-600 text-white rounded-xl">
                <x-lucide-heart-handshake class="w-5 h-5" />
            </div>
            <div>
                <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider block">Total Pemasukan Kas Non-SPP</span>
                <span class="text-lg font-black text-emerald-900">Rp {{ number_format($totalPemasukanKas, 0, ',', '.') }}</span>
            </div>
        </div>


    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Input Form Panel -->
        <div class="lg:col-span-1 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-5 h-fit">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider border-b border-stone-200 pb-2 flex items-center gap-2">
                <x-lucide-plus-circle class="w-4 h-4 text-emerald-600" />
                <span>Form Input Pemasukan Kas</span>
            </h3>
            
            <form wire:submit.prevent="saveIncome" class="space-y-4">
                <!-- Kategori Pemasukan -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori Pemasukan</label>
                    <select wire:model="kategori" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500">
                        @foreach ($kategoriOptions as $kat)
                            <option value="{{ $kat }}">{{ $kat }}</option>
                        @endforeach
                    </select>
                    @error('kategori') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Nominal -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Nominal Pemasukan (Rp)</label>
                    <input type="number" wire:model="jumlah" placeholder="0" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 font-bold text-right text-emerald-700" />
                    @error('jumlah') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal Penerimaan</label>
                    <input type="date" wire:model="tanggal" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                    @error('tanggal') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Keterangan -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Keterangan / Sumber</label>
                    <textarea wire:model="keterangan" rows="3" placeholder="Contoh: Infaq Jumat Jamaah, Donasi Habaib, Sedekah Subuh..." class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500"></textarea>
                    @error('keterangan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-emerald-600/10 flex items-center justify-center gap-2">
                        <x-lucide-check-circle class="w-4 h-4" />
                        <span>Simpan Pemasukan Kas</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Table List Panel -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="w-full sm:w-64 relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari keterangan..." 
                        class="w-full pl-9 pr-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                    <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" />
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Kategori:</span>
                    <select wire:model.live="filterKategori" class="px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoriOptions as $kat)
                            <option value="{{ $kat }}">{{ $kat }}</option>
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
                        @forelse ($pemasukans as $item)
                            <tr class="hover:bg-stone-50">
                                <td class="py-3 text-xs font-semibold text-stone-700 whitespace-nowrap">
                                    {{ date('d M Y', strtotime($item->tanggal)) }}
                                </td>
                                <td class="py-3">
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-bold inline-block">
                                        {{ $item->kategori }}
                                    </span>
                                </td>
                                <td class="py-3 text-stone-700 text-xs max-w-xs">
                                    {{ $item->keterangan ?: '-' }}
                                </td>
                                <td class="py-3 text-right font-black text-emerald-700">
                                    Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-center text-xs text-stone-500">
                                    {{ $item->petugas->nama ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-stone-400 font-medium text-sm">
                                    Belum ada data pemasukan kas non-SPP.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2 border-t border-stone-200">
                {{ $pemasukans->links() }}
            </div>
        </div>
    </div>
</div>
