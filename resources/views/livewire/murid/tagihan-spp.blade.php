<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-stone-900 tracking-tight">Tagihan & Keuangan Murid</h2>
        <p class="text-xs text-stone-500">Lihat status pembayaran SPP dan tagihan administrasi sekolah lainnya.</p>
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk & Ketentuan Pembayaran Keuangan SPP"
        :steps="[
            ['title' => 'Jatuh Tempo Tanggal 10', 'desc' => 'Batas akhir pembayaran SPP bulanan adalah tanggal 10 setiap bulannya.'],
            ['title' => 'Metode Pembayaran', 'desc' => 'Pembayaran dapat dilakukan tunai di kasir bendahara sekolah atau via Transfer Bank / QRIS.'],
            ['title' => 'Cetak Kuitansi Resi', 'desc' => 'Klik tombol Cetak Resi pada baris tagihan lunas untuk menyimpan bukti fisik pembayaran.']
        ]"
    />

    @php
        $unpaidInvoices = array_filter($invoices, fn($i) => ($i['status'] ?? '') !== 'lunas');
        $unpaidCount = count($unpaidInvoices);
    @endphp

    <!-- Outstanding Summary Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="space-y-1 text-center sm:text-left">
            <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Total Tunggakan Aktif</span>
            <span class="text-3xl font-black {{ $totalTunggakan > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
            </span>
        </div>
        <div class="px-4 py-2 bg-stone-50 border border-stone-200 rounded-xl text-center sm:text-right">
            <span class="text-xs text-stone-600 font-semibold block">Status Pembayaran SPP:</span>
            @if ($totalTunggakan > 0)
                <span class="text-xs font-bold text-rose-600 inline-flex items-center gap-1 mt-0.5">
                    <x-lucide-alert-circle class="w-3.5 h-3.5" /> Ada {{ $unpaidCount }} Tagihan Aktif
                </span>
            @else
                <span class="text-xs font-bold text-emerald-600 inline-flex items-center gap-1 mt-0.5">
                    <x-lucide-check-circle class="w-3.5 h-3.5" /> Seluruh Tagihan Lunas
                </span>
            @endif
        </div>
    </div>

    <!-- Invoices Table Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <h3 class="text-sm font-bold text-stone-900 uppercase tracking-wider">Daftar Tagihan SPP & Administrasi</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-stone-200 bg-stone-50">
                        <th class="py-3 px-4 text-xs font-bold text-stone-600 uppercase tracking-wider">Keterangan / Bulan</th>
                        <th class="py-3 px-4 text-xs font-bold text-stone-600 uppercase tracking-wider text-right">Nominal Tagihan</th>
                        <th class="py-3 px-4 text-xs font-bold text-stone-600 uppercase tracking-wider text-right">Terbayar</th>
                        <th class="py-3 px-4 text-xs font-bold text-stone-600 uppercase tracking-wider text-center">Status</th>
                        <th class="py-3 px-4 text-xs font-bold text-stone-600 uppercase tracking-wider text-center">Resi / Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
                    @forelse ($invoices as $inv)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="py-3.5 px-4 text-xs font-bold text-stone-900">
                                {{ $inv['jenis'] }} {{ $inv['bulan'] !== '-' ? '(' . $inv['bulan'] . ')' : '' }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-stone-800 font-bold text-right">
                                Rp {{ number_format($inv['nominal'], 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-emerald-700 font-bold text-right">
                                Rp {{ number_format($inv['total_dibayar'], 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($inv['status'] === 'lunas')
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-bold">Lunas</span>
                                @elseif ($inv['status'] === 'sebagian')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 border border-amber-200 rounded-lg text-xs font-bold">Sebagian</span>
                                @else
                                    <span class="px-2.5 py-1 bg-rose-100 text-rose-800 border border-rose-200 rounded-lg text-xs font-bold">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if (!empty($inv['pembayaran']))
                                    @php
                                        $lastBayar = end($inv['pembayaran']);
                                    @endphp
                                    @if ($lastBayar && isset($lastBayar['id']))
                                        <a href="{{ route('finance.cetak-resi', $lastBayar['id']) }}" target="_blank" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-sm">
                                            <x-lucide-printer class="w-3.5 h-3.5" />
                                            Cetak Resi
                                        </a>
                                    @else
                                        <span class="text-xs text-stone-400 font-medium">-</span>
                                    @endif
                                @else
                                    <span class="text-xs text-stone-400 font-medium">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-xs text-stone-500 font-medium">
                                Belum ada tagihan SPP yang tercatat untuk Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
