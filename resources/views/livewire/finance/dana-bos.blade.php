<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Tata Kelola Dana BOS</h2>
        <p class="text-xs text-slate-500">Catat realisasi masuk dan penggunaan operasional dana Bantuan Operasional Sekolah (BOS).</p>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Input Form Panel -->
        <div class="lg:col-span-1 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6 h-fit">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-slate-850 pb-2">Catat Transaksi BOS</h3>
            
            <form wire:submit.prevent="saveTransaction" class="space-y-4">
                <!-- Jenis Aliran -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jenis Arus</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach (['masuk' => 'Dana Masuk', 'keluar' => 'Dana Keluar'] as $key => $label)
                            <label class="flex items-center justify-center gap-2 p-3 bg-slate-950/30 border rounded-xl cursor-pointer text-xs font-semibold select-none transition duration-150
                                {{ $jenis === $key ? ($key === 'masuk' ? 'border-emerald-650 text-emerald-400 bg-emerald-500/5' : 'border-rose-650 text-rose-450 bg-rose-500/5') : 'border-slate-800 text-slate-400 hover:border-slate-750' }}
                            ">
                                <input type="radio" wire:model.live="jenis" value="{{ $key }}" class="hidden" />
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('jenis') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Nominal -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nominal (Rp)</label>
                    <input wire:model="nominal" type="number" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 text-right font-bold 
                        {{ $jenis === 'masuk' ? 'text-emerald-400' : 'text-rose-400' }}" />
                    @error('nominal') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Kategori / Komponen BOS -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kategori / Komponen</label>
                    <input wire:model="kategori" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Contoh: Belanja Buku, Gaji Pegawai..." />
                    @error('kategori') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Transaksi -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal</label>
                    <input wire:model="tanggal" type="date" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
                    @error('tanggal') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Keterangan / Deskripsi -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Keterangan Tambahan</label>
                    <textarea wire:model="keterangan" rows="3" class="w-full px-3 py-2.5 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Rincian alokasi anggaran dana BOS..."></textarea>
                    @error('keterangan') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition duration-200 shadow-lg shadow-indigo-600/10">
                        Simpan Transaksi BOS
                    </button>
                </div>
            </form>
        </div>

        <!-- Transactions List Panel -->
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Riwayat Mutasi Dana BOS</h3>

            <!-- List Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Tahun Ajaran</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Arus</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori / Keterangan</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @forelse ($transactions as $tx)
                            <tr class="hover:bg-slate-950/20">
                                <td class="py-3.5 text-xs font-semibold text-white">{{ date('d-m-Y', strtotime($tx->tanggal)) }}</td>
                                <td class="py-3.5 text-xs text-slate-350">{{ $tx->tahunAjaran->nama ?? '-' }}</td>
                                <td class="py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider
                                        {{ $tx->jenis === 'masuk' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-450 border border-rose-500/20' }}
                                    ">
                                        {{ $tx->jenis }}
                                    </span>
                                </td>
                                <td class="py-3.5 text-xs text-slate-400">
                                    <span class="font-bold text-slate-300 block">{{ $tx->kategori }}</span>
                                    <span class="text-[9px] text-slate-500 block truncate max-w-xs" title="{{ $tx->keterangan }}">{{ $tx->keterangan }}</span>
                                </td>
                                <td class="py-3.5 text-xs font-bold text-right
                                    {{ $tx->jenis === 'masuk' ? 'text-emerald-400' : 'text-rose-450' }}">
                                    Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500 font-semibold text-xs">
                                    Belum ada transaksi Dana BOS yang direkam.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-4 border-t border-slate-850">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
