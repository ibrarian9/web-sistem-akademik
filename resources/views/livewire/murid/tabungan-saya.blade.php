<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
                SD Tahfizh F3 Digital System
            </span>
            <h2 class="text-xl font-extrabold text-stone-900 tracking-tight mt-1">Tabungan Saya</h2>
            <p class="text-xs text-stone-500 font-medium">Informasi saldo terkini dan riwayat mutasi tabungan santri yang tercatat di Bendahara Sekolah.</p>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-3xl p-6 shadow-md space-y-3 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-100">Saldo Tabungan Terkini</span>
                <div class="p-2 bg-white/15 backdrop-blur-md rounded-2xl text-white">
                    <x-lucide-wallet class="w-5 h-5" />
                </div>
            </div>
            <div class="text-3xl font-black tracking-tight">Rp {{ number_format($saldoTerkini, 0, ',', '.') }}</div>
            <div class="text-xs text-emerald-100 font-medium">Santri: {{ $siswa->user->nama ?? '-' }} (Kelas {{ $siswa->kelas->nama_kelas ?? '-' }})</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm space-y-2 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Setoran Tabungan</span>
                <div class="p-2.5 bg-emerald-50 text-emerald-700 rounded-2xl border border-emerald-200">
                    <x-lucide-arrow-down-left class="w-5 h-5" />
                </div>
            </div>
            <div>
                <div class="text-2xl font-black text-stone-900">Rp {{ number_format($totalSetor, 0, ',', '.') }}</div>
                <div class="text-[11px] text-stone-500 font-medium mt-1">Akumulasi dana disimpan</div>
            </div>
        </div>

        <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm space-y-2 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Penarikan Tabungan</span>
                <div class="p-2.5 bg-amber-50 text-amber-700 rounded-2xl border border-amber-200">
                    <x-lucide-arrow-up-right class="w-5 h-5" />
                </div>
            </div>
            <div>
                <div class="text-2xl font-black text-stone-900">Rp {{ number_format($totalTarik, 0, ',', '.') }}</div>
                <div class="text-[11px] text-stone-500 font-medium mt-1">Akumulasi dana diambil</div>
            </div>
        </div>
    </div>

    <!-- Mutasi Transaction History Table -->
    <div class="bg-white border border-stone-200 rounded-3xl shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-stone-100 pb-4">
            <div>
                <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wide">Riwayat Mutasi Tabungan</h3>
                <p class="text-xs text-stone-500">Daftar transaksi setoran dan penarikan yang telah diproses.</p>
            </div>
        </div>

        <x-table>
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-48">Tanggal / Kode</x-table.th>
                    <x-table.th align="center" class="w-32">Jenis Transaksi</x-table.th>
                    <x-table.th align="right" class="w-36">Nominal</x-table.th>
                    <x-table.th align="right" class="w-36">Saldo Akhir</x-table.th>
                    <x-table.th class="min-w-[180px]">Keterangan / Petugas</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($mutasiList as $tx)
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ \Carbon\Carbon::parse($tx->tanggal)->translatedFormat('l, d M Y') }}</div>
                            <div class="text-[10px] text-stone-400 font-bold font-mono">{{ $tx->kode_transaksi }}</div>
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($tx->jenis === 'setor')
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-900 border border-emerald-200 rounded-full text-[10px] font-black uppercase inline-flex items-center gap-1">
                                    <x-lucide-arrow-down-left class="w-3 h-3 text-emerald-600" /> Setor
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-900 border border-amber-200 rounded-full text-[10px] font-black uppercase inline-flex items-center gap-1">
                                    <x-lucide-arrow-up-right class="w-3 h-3 text-amber-600" /> Tarik
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5 text-right font-black text-xs border-r border-stone-200 {{ $tx->jenis === 'setor' ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ $tx->jenis === 'setor' ? '+' : '-' }} Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-right font-black text-stone-900 text-xs border-r border-stone-200">
                            Rp {{ number_format($tx->saldo_akhir, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-xs">
                            <div class="font-bold text-stone-800">{{ $tx->keterangan ?: '-' }}</div>
                            <div class="text-[10px] text-stone-400">Dicatat oleh: {{ $tx->petugas->nama ?? 'Bendahara' }}</div>
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="5" title="Belum Ada Transaksi Tabungan" message="Silakan lakukan setoran awal melalui Bendahara Sekolah untuk memulai tabungan santri." />
                @endforelse
            </tbody>
        </x-table>
    </div>
</div>
