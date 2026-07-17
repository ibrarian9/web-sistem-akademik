<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Tata Kelola Dana BOS</h2>
        <p class="text-sm text-stone-500">Catat realisasi masuk dan penggunaan operasional dana Bantuan Operasional Sekolah (BOS).</p>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Input Form Panel -->
        <div class="lg:col-span-1 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6 h-fit">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider border-b border-stone-200 pb-2">Catat Transaksi BOS</h3>
            
            <form wire:submit.prevent="saveTransaction" class="space-y-4">
                <!-- Jenis Aliran -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Jenis Arus</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach (['masuk' => 'Dana Masuk', 'keluar' => 'Dana Keluar'] as $key => $label)
                            <label class="flex items-center justify-center gap-2 p-3 bg-stone-50 border rounded-xl cursor-pointer text-sm font-semibold select-none transition duration-150
                                {{ $jenis === $key ? ($key === 'masuk' ? 'border-green-500 text-green-700 bg-green-50' : 'border-red-500 text-red-700 bg-red-50') : 'border-stone-300 text-stone-500 hover:border-stone-400' }}
                            ">
                                <input type="radio" wire:model.live="jenis" value="{{ $key }}" class="hidden" />
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('jenis') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Nominal -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Nominal (Rp)</label>
                    <input wire:model="nominal" type="number" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500 text-right font-bold" />
                    @error('nominal') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Kategori / Komponen BOS -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori / Komponen</label>
                    <input wire:model="kategori" type="text" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500" placeholder="Contoh: Belanja Buku, Gaji Pegawai..." />
                    @error('kategori') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Transaksi -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal</label>
                    <input wire:model="tanggal" type="date" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500" />
                    @error('tanggal') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Keterangan / Deskripsi -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Keterangan Tambahan</label>
                    <textarea wire:model="keterangan" rows="3" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500" placeholder="Rincian alokasi anggaran dana BOS..."></textarea>
                    @error('keterangan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-green-600/10">
                        Simpan Transaksi BOS
                    </button>
                </div>
            </form>
        </div>

        <!-- Transactions List Panel -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider">Riwayat Mutasi Dana BOS</h3>

            <!-- List Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-stone-200">
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Tahun Ajaran</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Arus</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori / Keterangan</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($transactions as $tx)
                            <tr class="hover:bg-stone-50">
                                <td class="py-3.5 text-sm font-semibold text-stone-800">{{ date('d-m-Y', strtotime($tx->tanggal)) }}</td>
                                <td class="py-3.5 text-sm text-stone-600">{{ $tx->tahunAjaran->nama ?? '-' }}</td>
                                <td class="py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $tx->jenis === 'masuk' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}
                                    ">
                                        {{ ucfirst($tx->jenis) }}
                                    </span>
                                </td>
                                <td class="py-3.5 text-sm text-stone-600">
                                    <span class="font-semibold text-stone-700 block">{{ $tx->kategori }}</span>
                                    <span class="text-xs text-stone-400 block truncate max-w-xs" title="{{ $tx->keterangan }}">{{ $tx->keterangan }}</span>
                                </td>
                                <td class="py-3.5 text-sm font-bold text-right
                                    {{ $tx->jenis === 'masuk' ? 'text-green-700' : 'text-red-600' }}">
                                    Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-stone-400 font-medium text-sm">
                                    Belum ada transaksi Dana BOS yang direkam.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-4 border-t border-stone-200">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
