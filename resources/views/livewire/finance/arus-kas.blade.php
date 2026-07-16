<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Arus Kas & Pengeluaran</h2>
        <p class="text-xs text-slate-500">Rekam pengeluaran rutin, belanja operasional yayasan, dan laporan kas keluar.</p>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Input Form Panel -->
        <div class="lg:col-span-1 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6 h-fit">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-slate-850 pb-2">Catat Kas Keluar</h3>
            
            <form wire:submit.prevent="saveExpense" class="space-y-4">
                <!-- Kategori Pengeluaran -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kategori</label>
                    <select wire:model.live="kategori_pengeluaran_id" class="w-full px-3 py-2.5 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat['id'] }}">{{ $cat['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('kategori_pengeluaran_id') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Jumlah / Nominal -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jumlah (Rp)</label>
                    <input wire:model="jumlah" type="number" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 text-right font-bold text-rose-400" />
                    @error('jumlah') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Pengeluaran -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal</label>
                    <input wire:model="tanggal" type="date" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
                    @error('tanggal') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Keterangan / Deskripsi -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Keterangan Pengeluaran</label>
                    <textarea wire:model="keterangan" rows="3" class="w-full px-3 py-2.5 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Belanja ATK kantor, token listrik, dll..."></textarea>
                    @error('keterangan') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-3 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold transition duration-200 shadow-lg shadow-rose-600/10">
                        Catat Kas Keluar
                    </button>
                </div>
            </form>
        </div>

        <!-- History & Filtering List Panel -->
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
            <!-- Filtering Bar -->
            <div class="flex items-center justify-between bg-slate-950/30 p-4 border border-slate-850 rounded-2xl">
                <div class="w-full sm:w-72 space-y-1">
                    <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Filter Kategori</label>
                    <select wire:model.live="filterKategori" class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-lg text-white text-xs focus:ring-1 focus:ring-indigo-500">
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
                        <tr class="border-b border-slate-800">
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Keterangan</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Jumlah</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @forelse ($expenses as $exp)
                            <tr class="hover:bg-slate-950/20">
                                <td class="py-3 text-xs font-semibold text-white">{{ date('d-m-Y', strtotime($exp->tanggal)) }}</td>
                                <td class="py-3 text-xs text-slate-350 font-medium">{{ $exp->kategori->nama ?? '-' }}</td>
                                <td class="py-3 text-xs text-slate-400 max-w-xs truncate" title="{{ $exp->keterangan }}">{{ $exp->keterangan }}</td>
                                <td class="py-3 text-xs font-bold text-rose-400 text-right">Rp {{ number_format($exp->jumlah, 0, ',', '.') }}</td>
                                <td class="py-3 text-xs text-slate-500 text-center">{{ $exp->petugas->nama ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500 font-semibold text-xs">
                                    Belum ada catatan pengeluaran kas keluar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-4 border-t border-slate-850">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
</div>
