<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Laporan Pemasukan</h2>
            <p class="text-sm text-stone-500">Tinjau seluruh transaksi setoran pembayaran tagihan siswa dalam rentang tanggal tertentu.</p>
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

    <!-- Guidance Card -->
    <div x-data="{ openGuide: true }" class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-4 transition-all shadow-sm">
        <div class="flex items-center justify-between cursor-pointer" @click="openGuide = !openGuide">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-sm">
                    <x-lucide-info class="w-5 h-5" />
                </div>
                <div>
                    <h4 class="text-xs font-bold text-emerald-950 uppercase tracking-wider">Petunjuk Laporan Pemasukan</h4>
                    <p class="text-xs text-emerald-800">Filter transaksi penerimaan SPP &amp; tagihan, lalu ekspor dokumen resmi PDF / CSV.</p>
                </div>
            </div>
            <button class="text-emerald-700 hover:text-emerald-900 text-xs font-semibold flex items-center gap-1">
                <span x-text="openGuide ? 'Sembunyikan' : 'Tampilkan'"></span>
                <x-lucide-chevron-down class="w-4 h-4 transition-transform" ::class="openGuide ? 'rotate-180' : ''" />
            </button>
        </div>
        <div x-show="openGuide" class="mt-3 pt-3 border-t border-emerald-200/60 grid grid-cols-1 md:grid-cols-3 gap-3 text-xs text-emerald-900">
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-filter class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Rentang Tanggal:</strong> Pilih periode awal &amp; akhir untuk mempersempit rekapitulasi laporan.</span>
            </div>
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-credit-card class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Filter Metode:</strong> Saring penerimaan berdasarkan Tunai, Transfer Bank, atau E-Wallet.</span>
            </div>
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-download class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Ekspor Laporan:</strong> Klik tombol PDF / CSV di pojok kanan atas untuk mengunduh rekap transaksi.</span>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider block mb-1">Cari Nama Siswa</label>
            <input wire:model.live="search" type="text" placeholder="Cari siswa..." class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-green-500/50" />
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
            <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider block mb-1">Jenis Tagihan</label>
            <select wire:model.live="jenis_tagihan_id" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-green-500/50">
                <option value="">Semua Tagihan</option>
                @foreach ($jenisTagihans as $jt)
                    <option value="{{ $jt->id }}">{{ $jt->nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider block mb-1">Metode Bayar</label>
            <select wire:model.live="metode_bayar" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-green-500/50">
                <option value="">Semua Metode</option>
                <option value="Tunai">Tunai</option>
                <option value="Transfer Bank">Transfer Bank</option>
                <option value="E-Wallet">E-Wallet</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-stone-200">
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal Bayar</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Siswa</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Kelas</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Jenis Tagihan</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Metode</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Jumlah Pemasukan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($payments as $p)
                        <tr class="hover:bg-stone-50">
                            <td class="py-3.5 text-sm font-semibold text-stone-800">{{ $p->tanggal_bayar ? $p->tanggal_bayar->format('d-m-Y') : '-' }}</td>
                            <td class="py-3.5 text-sm font-semibold text-stone-800">{{ $p->tagihan->siswa->user->nama ?? '-' }}</td>
                            <td class="py-3.5 text-sm text-stone-600">{{ $p->tagihan->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td class="py-3.5 text-sm text-stone-600">
                                <span class="font-semibold text-stone-700 block">{{ $p->tagihan->jenisTagihan->nama ?? '-' }}</span>
                                <span class="text-xs text-stone-400 block">{{ $p->tagihan->bulan ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 text-sm text-stone-600 text-center">{{ $p->metode_bayar }}</td>
                            <td class="py-3.5 text-sm font-bold text-green-700 text-right">Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-stone-400 font-medium text-sm">
                                Tidak ada data transaksi pemasukan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-stone-200">
            {{ $payments->links() }}
        </div>
    </div>
</div>
