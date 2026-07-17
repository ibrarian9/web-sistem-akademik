<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Laporan Tunggakan Siswa</h2>
            <p class="text-sm text-stone-500">Tinjau daftar tagihan yang belum lunas per kelas dan tahun ajaran.</p>
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

    <!-- Filters Bar -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider block mb-1">Cari Nama Siswa</label>
            <input wire:model.live="search" type="text" placeholder="Masukkan nama siswa..." class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50" />
        </div>
        
        <div class="w-48">
            <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider block mb-1">Kelas</label>
            <select wire:model.live="kelas_id" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50">
                <option value="">Semua Kelas</option>
                @foreach ($kelases as $k)
                    <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-48">
            <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider block mb-1">Tahun Ajaran</label>
            <select wire:model.live="tahun_ajaran_id" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50">
                <option value="">Semua</option>
                @foreach ($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-stone-200">
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Siswa</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Kelas</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Tagihan</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Bulan</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Nominal</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Dibayar</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Sisa Tunggakan</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($tunggakans as $t)
                        <tr class="hover:bg-stone-50">
                            <td class="py-3.5 text-sm font-semibold text-stone-800">{{ $t->siswa->user->nama ?? '-' }}</td>
                            <td class="py-3.5 text-sm text-stone-600">{{ $t->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td class="py-3.5 text-sm text-stone-600">{{ $t->jenisTagihan->nama ?? '-' }}</td>
                            <td class="py-3.5 text-sm text-stone-600 text-center">{{ $t->bulan ?? '-' }}</td>
                            <td class="py-3.5 text-sm text-stone-700 text-right">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                            <td class="py-3.5 text-sm text-stone-700 text-right text-green-700">Rp {{ number_format($t->total_dibayar, 0, ',', '.') }}</td>
                            <td class="py-3.5 text-sm font-bold text-red-600 text-right">Rp {{ number_format($t->nominal - $t->total_dibayar, 0, ',', '.') }}</td>
                            <td class="py-3.5 text-sm text-stone-500 text-center">{{ $t->jatuh_tempo ? $t->jatuh_tempo->format('d-m-Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-stone-400 font-medium text-sm">
                                Tidak ada data tunggakan pembayaran yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-stone-200">
            {{ $tunggakans->links() }}
        </div>
    </div>
</div>
