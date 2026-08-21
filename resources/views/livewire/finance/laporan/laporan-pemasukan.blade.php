<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Laporan Pemasukan Keuangan" 
        subtitle="Tinjau seluruh transaksi setoran pembayaran tagihan siswa dalam rentang tanggal tertentu."
        badge="LAPORAN PEMASUKAN"
        badgeVariant="emerald"
        icon="trending-up"
    >
        <x-slot:actions>
            <x-button variant="outline" size="sm" icon="file-text" wire:click="exportPdf">
                Ekspor PDF
            </x-button>
            <x-button variant="primary" size="sm" icon="download" href="{{ route('finance.export.pemasukan', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank">
                Ekspor Excel (.xlsx)
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Laporan Pemasukan Keuangan"
        :steps="[
            ['title' => 'Filter Periode', 'desc' => 'Pilih tanggal mulai dan tanggal selesai untuk memfilter penerimaan kas sekolah.'],
            ['title' => 'Cetak & Ekspor PDF', 'desc' => 'Klik Ekspor PDF untuk mencetak dokumen fisik laporan penerimaan yang disahkan QR Code & TTD.'],
            ['title' => 'Rincian Transaksi', 'desc' => 'Tabel menampilkan rincian nama siswa, jenis tagihan, kanal pembayaran, serta nominal terbayar.']
        ]"
    />

    <!-- Filters Bar -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Cari Siswa</label>
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari siswa..." />
            </div>
            
            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Mulai Tanggal</label>
                <input wire:model.live="startDate" type="date" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Sampai Tanggal</label>
                <input wire:model.live="endDate" type="date" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs" />
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Jenis Tagihan</label>
                <select wire:model.live="jenis_tagihan_id" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    <option value="">Semua Tagihan</option>
                    @foreach ($jenisTagihans as $jt)
                        <option value="{{ $jt->id }}">{{ $jt->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Metode Bayar</label>
                <select wire:model.live="metode_bayar" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    <option value="">Semua Metode</option>
                    <option value="Tunai">Tunai</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="E-Wallet">E-Wallet</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <x-table loadingTarget="search, startDate, endDate, jenis_tagihan_id, metode_bayar, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-36">Tanggal Bayar</x-table.th>
                    <x-table.th class="min-w-[180px]">Siswa</x-table.th>
                    <x-table.th class="w-36">Kelas</x-table.th>
                    <x-table.th class="w-48">Jenis Tagihan</x-table.th>
                    <x-table.th align="center" class="w-36">Metode</x-table.th>
                    <x-table.th align="right" class="w-44">Jumlah Pemasukan</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($payments as $p)
                    <tr class="hover:bg-emerald-50/40 transition">
                        <td class="p-3.5 text-xs font-bold text-stone-900 border-r border-stone-200">{{ $p->tanggal_bayar ? $p->tanggal_bayar->format('d/m/Y') : '-' }}</td>
                        <td class="p-3.5 text-xs font-extrabold text-stone-900 border-r border-stone-200">{{ $p->tagihan->siswa->user->nama ?? '-' }}</td>
                        <td class="p-3.5 border-r border-stone-200">
                            <x-badge variant="stone" size="xs">
                                {{ $p->tagihan->siswa->kelas->nama_kelas ?? '-' }}
                            </x-badge>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <span class="font-bold text-xs text-stone-900 block">{{ $p->tagihan->jenisTagihan->nama ?? '-' }}</span>
                            <span class="text-[10px] text-stone-400 font-medium block">{{ $p->tagihan->bulan ?? '-' }}</span>
                        </td>
                        <td class="p-3.5 text-center text-xs font-semibold text-stone-700 border-r border-stone-200">{{ $p->metode_bayar }}</td>
                        <td class="p-3.5 text-xs font-black text-emerald-800 text-right">Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <x-table.empty :colspan="6" title="Tidak ada data pemasukan" message="Tidak ditemukan transaksi pemasukan pada rentang tanggal terpilih." />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $payments->links() }}
        </div>
    </div>
</div>
