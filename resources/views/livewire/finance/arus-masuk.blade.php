<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight flex items-center gap-2">
                <x-lucide-arrow-down-left class="w-6 h-6 text-emerald-600" />
                <span>Rincian Arus Masuk Keuangan</span>
            </h2>
            <p class="text-xs text-stone-500">Rekapitulasi pemasukan dana yayasan per kategori: SPP, Uang Tahunan, Uang Pembangunan, Dana BOS, &amp; Pemasukan Lainnya.</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama siswa..." 
                    class="bg-stone-50 border border-stone-300 text-stone-800 placeholder-stone-400 rounded-xl pl-9 pr-4 py-2 text-xs focus:outline-none focus:border-indigo-500 w-64 font-medium" />
                <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" />
            </div>

            <select wire:model.live="filterJenis" class="bg-stone-50 border border-stone-300 text-stone-800 rounded-xl px-3 py-2 text-xs font-bold focus:outline-none focus:border-indigo-500">
                <option value="semua">Semua Pemasukan</option>
                <option value="spp">Uang SPP Bulanan</option>
                <option value="tahunan">Uang Tahunan / Registrasi</option>
                <option value="pembangunan">Uang Pembangunan / Gedung</option>
                <option value="lainnya">Pemasukan Lainnya</option>
            </select>
        </div>
    </div>

    <!-- SUMMARY CARDS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- SPP -->
        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-stone-500 text-xs">
                <span class="font-bold">Uang SPP</span>
                <div class="p-1.5 bg-emerald-50 rounded-lg text-emerald-600">
                    <x-lucide-calendar-check class="w-4 h-4" />
                </div>
            </div>
            <p class="text-lg font-black text-stone-800">Rp {{ number_format($sppTotal, 0, ',', '.') }}</p>
        </div>

        <!-- TAHUNAN -->
        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-stone-500 text-xs">
                <span class="font-bold">Uang Tahunan</span>
                <div class="p-1.5 bg-blue-50 rounded-lg text-blue-600">
                    <x-lucide-refresh-cw class="w-4 h-4" />
                </div>
            </div>
            <p class="text-lg font-black text-stone-800">Rp {{ number_format($tahunanTotal, 0, ',', '.') }}</p>
        </div>

        <!-- PEMBANGUNAN -->
        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-stone-500 text-xs">
                <span class="font-bold">Uang Pembangunan</span>
                <div class="p-1.5 bg-amber-50 rounded-lg text-amber-600">
                    <x-lucide-building class="w-4 h-4" />
                </div>
            </div>
            <p class="text-lg font-black text-stone-800">Rp {{ number_format($pembangunanTotal, 0, ',', '.') }}</p>
        </div>

        <!-- DANA BOS -->
        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-stone-500 text-xs">
                <span class="font-bold">Dana BOS Masuk</span>
                <div class="p-1.5 bg-indigo-50 rounded-lg text-indigo-600">
                    <x-lucide-landmark class="w-4 h-4" />
                </div>
            </div>
            <p class="text-lg font-black text-stone-800">Rp {{ number_format($bosTotal, 0, ',', '.') }}</p>
        </div>

        <!-- LAINNYA & TOTAL -->
        <div class="bg-emerald-600 text-white rounded-2xl p-4 shadow-md space-y-2">
            <div class="flex items-center justify-between text-emerald-100 text-xs">
                <span class="font-bold">Total Arus Masuk</span>
                <x-lucide-trending-up class="w-4 h-4" />
            </div>
            <p class="text-lg font-black">Rp {{ number_format($grandTotalInflow, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- TRANSACTIONS TABLE -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm overflow-x-auto space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider flex items-center gap-2">
                <x-lucide-list class="w-4 h-4 text-indigo-600" />
                <span>Riwayat Transaksi Pemasukan Terdaftar</span>
            </h3>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-stone-200 text-stone-500 text-xs uppercase tracking-wider">
                    <th class="pb-3 font-bold flex items-center gap-1"><x-lucide-receipt class="w-3.5 h-3.5 text-stone-400" /> No. Resi / Tanggal</th>
                    <th class="pb-3 font-bold"><div class="flex items-center gap-1"><x-lucide-user class="w-3.5 h-3.5 text-stone-400" /> Siswa / Sumber</div></th>
                    <th class="pb-3 font-bold"><div class="flex items-center gap-1"><x-lucide-tag class="w-3.5 h-3.5 text-stone-400" /> Kategori Pemasukan</div></th>
                    <th class="pb-3 font-bold"><div class="flex items-center gap-1"><x-lucide-credit-card class="w-3.5 h-3.5 text-stone-400" /> Metode</div></th>
                    <th class="pb-3 font-bold text-right"><div class="flex items-center justify-end gap-1"><x-lucide-dollar-sign class="w-3.5 h-3.5 text-stone-400" /> Nominal Setoran</div></th>
                    <th class="pb-3 font-bold text-center"><div class="flex items-center justify-center gap-1"><x-lucide-file-text class="w-3.5 h-3.5 text-stone-400" /> Aksi / Resi</div></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100 text-xs">
                @forelse ($pembayarans as $p)
                    <tr class="hover:bg-stone-50/50">
                        <td class="py-3.5">
                            <span class="font-mono font-bold text-stone-800 block text-[11px]">{{ $p->no_resi ?? '-' }}</span>
                            <span class="text-stone-400 font-mono text-[10px]">{{ date('d/m/Y', strtotime($p->tanggal_bayar)) }}</span>
                        </td>
                        <td class="py-3.5 font-bold text-stone-800">
                            {{ $p->tagihan->siswa->user->nama ?? '-' }}
                            <span class="text-[10px] text-stone-400 block font-normal">NIS: {{ $p->tagihan->siswa->nis ?? '-' }}</span>
                        </td>
                        <td class="py-3.5">
                            <span class="px-2.5 py-1 bg-stone-100 text-stone-700 rounded-lg font-bold text-[10px] uppercase border border-stone-200">
                                {{ $p->tagihan->jenisTagihan->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="py-3.5 text-stone-600">{{ $p->metode_bayar }}</td>
                        <td class="py-3.5 text-right font-black text-emerald-700 text-sm">
                            Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}
                            @if ($p->kelebihan_bayar > 0)
                                <span class="block text-[10px] text-emerald-600 font-normal">+Deposit: Rp {{ number_format($p->kelebihan_bayar, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 text-center">
                            @if ($p->is_void)
                                <span class="px-2 py-0.5 bg-rose-100 text-rose-700 rounded font-bold text-[10px]">VOID</span>
                            @else
                                <a href="{{ route('finance.pembayaran.resi', $p->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 bg-stone-100 hover:bg-emerald-50 text-stone-700 hover:text-emerald-700 border border-stone-200 hover:border-emerald-300 rounded-lg font-bold text-[10px] transition">
                                    <x-lucide-printer class="w-3 h-3" />
                                    <span>Cetak Resi</span>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-stone-400 text-xs">
                            Belum ada riwayat transaksi pemasukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $pembayarans->links() }}
        </div>
    </div>
</div>
