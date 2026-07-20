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

    <!-- Guidance Card -->
    <div x-data="{ openGuide: true }" class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-4 transition-all shadow-sm">
        <div class="flex items-center justify-between cursor-pointer" @click="openGuide = !openGuide">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-sm">
                    <x-lucide-info class="w-5 h-5" />
                </div>
                <div>
                    <h4 class="text-xs font-bold text-emerald-950 uppercase tracking-wider">Petunjuk Laporan Tunggakan Siswa</h4>
                    <p class="text-xs text-emerald-800">Pantau akumulasi tunggakan SPP &amp; tagihan per siswa, per kelas, serta status pemblokiran rapor.</p>
                </div>
            </div>
            <button class="text-emerald-700 hover:text-emerald-900 text-xs font-semibold flex items-center gap-1">
                <span x-text="openGuide ? 'Sembunyikan' : 'Tampilkan'"></span>
                <x-lucide-chevron-down class="w-4 h-4 transition-transform" ::class="openGuide ? 'rotate-180' : ''" />
            </button>
        </div>
        <div x-show="openGuide" class="mt-3 pt-3 border-t border-emerald-200/60 grid grid-cols-1 md:grid-cols-3 gap-3 text-xs text-emerald-900">
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-alert-triangle class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Identifikasi Tunggakan:</strong> Menampilkan sisa piutang SPP dan nominal yang belum dibayarkan oleh siswa.</span>
            </div>
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-filter class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Filter per Kelas:</strong> Saring data per kelas untuk koordinasi dengan Wali Kelas terkait penagihan.</span>
            </div>
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-download class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Ekspor Surat Tagihan:</strong> Ekspor ke PDF / CSV sebagai lampiran pemberitahuan kepada Orang Tua / Wali Murid.</span>
            </div>
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
