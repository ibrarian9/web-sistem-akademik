<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Laporan Pemasukan Keuangan"
        :steps="[
            ['title' => 'Filter Periode', 'desc' => 'Pilih tanggal mulai dan tanggal selesai untuk memfilter penerimaan kas sekolah.'],
            ['title' => 'Cetak & Ekspor PDF', 'desc' => 'Klik Ekspor PDF untuk mencetak dokumen fisik laporan penerimaan yang disahkan QR Code & TTD.'],
            ['title' => 'Rincian Transaksi', 'desc' => 'Tabel menampilkan rincian nama siswa, jenis tagihan, kanal pembayaran, serta nominal terbayar.']
        ]"
    />

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
            <a href="{{ route('finance.export.pemasukan', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition flex items-center gap-2 shadow-md shadow-emerald-600/10">
                <x-lucide-download class="w-4 h-4" />
                <span>Ekspor Excel (.xlsx)</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->

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
