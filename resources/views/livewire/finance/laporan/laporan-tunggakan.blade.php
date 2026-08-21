<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Laporan Tunggakan Siswa" 
        subtitle="Tinjau daftar tagihan yang belum lunas per kelas dan tahun ajaran."
        badge="MONITORING TUNGGAKAN"
        badgeVariant="rose"
        icon="alert-circle"
    >
        <x-slot:actions>
            <x-button variant="outline" size="sm" icon="file-text" wire:click="exportPdf">
                Ekspor PDF
            </x-button>
            <x-button variant="primary" size="sm" icon="download" href="{{ route('finance.export.tunggakan', ['kelas_id' => $kelas_id, 'tahun_ajaran_id' => $tahun_ajaran_id]) }}" target="_blank">
                Ekspor Excel (.xlsx)
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Monitoring Laporan Tunggakan Siswa"
        :steps="[
            ['title' => 'Filter Rombel Kelas', 'desc' => 'Filter daftar tunggakan SPP berdasarkan kelas perwalian atau tampilkan seluruh kelas.'],
            ['title' => 'Cetak PDF Rekap Tagihan', 'desc' => 'Ekspor dokumen laporan tunggakan untuk rekapitulasi penagihan orang tua murid.'],
            ['title' => 'Kunci Rapor Otomatis', 'desc' => 'Tunggakan yang melewati batas tanggal 10 otomatis mengunci penerbitan rapor murid.']
        ]"
    />

    <!-- Filters Bar -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Cari Nama Siswa</label>
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Masukkan nama siswa..." />
            </div>
            
            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Filter Kelas</label>
                <select wire:model.live="kelas_id" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelases as $k)
                        <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1.5">Tahun Ajaran</label>
                <select wire:model.live="tahun_ajaran_id" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Table -->
        <x-table loadingTarget="search, kelas_id, tahun_ajaran_id, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="min-w-[180px]">Siswa</x-table.th>
                    <x-table.th class="w-36">Kelas</x-table.th>
                    <x-table.th class="w-48">Tagihan</x-table.th>
                    <x-table.th align="center" class="w-28">Bulan</x-table.th>
                    <x-table.th align="right" class="w-36">Nominal</x-table.th>
                    <x-table.th align="right" class="w-36">Dibayar</x-table.th>
                    <x-table.th align="right" class="w-40">Sisa Tunggakan</x-table.th>
                    <x-table.th align="center" class="w-32">Jatuh Tempo</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($tunggakans as $t)
                    <tr class="hover:bg-rose-50/40 transition">
                        <td class="p-3.5 font-extrabold text-stone-900 text-xs border-r border-stone-200">{{ $t->siswa->user->nama ?? '-' }}</td>
                        <td class="p-3.5 border-r border-stone-200">
                            <x-badge variant="stone" size="xs">
                                {{ $t->siswa->kelas->nama_kelas ?? '-' }}
                            </x-badge>
                        </td>
                        <td class="p-3.5 text-xs font-semibold text-stone-800 border-r border-stone-200">{{ $t->jenisTagihan->nama ?? '-' }}</td>
                        <td class="p-3.5 text-xs text-stone-600 text-center font-bold border-r border-stone-200">{{ $t->bulan ?? '-' }}</td>
                        <td class="p-3.5 text-xs font-bold text-stone-900 text-right border-r border-stone-200">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                        <td class="p-3.5 text-xs text-emerald-700 text-right font-bold border-r border-stone-200">Rp {{ number_format($t->total_dibayar, 0, ',', '.') }}</td>
                        <td class="p-3.5 text-xs font-black text-rose-700 text-right border-r border-stone-200">Rp {{ number_format($t->nominal - $t->total_dibayar, 0, ',', '.') }}</td>
                        <td class="p-3.5 text-xs text-stone-600 text-center font-semibold">{{ $t->jatuh_tempo ? $t->jatuh_tempo->format('d/m/Y') : '-' }}</td>
                    </tr>
                @empty
                    <x-table.empty :colspan="8" title="Tidak ada data tunggakan" message="Seluruh tagihan lunas atau tidak ada data yang sesuai filter." />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $tunggakans->links() }}
        </div>
    </div>
</div>
