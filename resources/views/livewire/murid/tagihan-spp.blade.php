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
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider">Daftar Tagihan SPP & Administrasi</h3>

        <x-table>
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="min-w-[180px]">Keterangan / Bulan</x-table.th>
                    <x-table.th align="right" class="w-40">Nominal Tagihan</x-table.th>
                    <x-table.th align="right" class="w-40">Terbayar</x-table.th>
                    <x-table.th align="center" class="w-32">Status</x-table.th>
                    <x-table.th align="center" class="w-32">Resi / Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($invoices as $inv)
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3.5 text-xs font-bold text-stone-900 border-r border-stone-200">
                            {{ $inv['jenis'] }} {{ $inv['bulan'] !== '-' ? '(' . $inv['bulan'] . ')' : '' }}
                        </td>
                        <td class="p-3.5 text-xs text-stone-900 font-bold text-right border-r border-stone-200">
                            Rp {{ number_format($inv['nominal'], 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-xs text-emerald-700 font-bold text-right border-r border-stone-200">
                            Rp {{ number_format($inv['total_dibayar'], 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($inv['status'] === 'lunas')
                                <x-badge variant="emerald" size="xs">Lunas</x-badge>
                            @elseif ($inv['status'] === 'sebagian')
                                <x-badge variant="amber" size="xs">Sebagian</x-badge>
                            @else
                                <x-badge variant="rose" size="xs">Belum Bayar</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            @if (!empty($inv['pembayaran']))
                                @php
                                    $lastBayar = end($inv['pembayaran']);
                                @endphp
                                @if ($lastBayar && isset($lastBayar['id']))
                                    <x-button variant="outline" size="xs" icon="printer" href="{{ route('finance.cetak-resi', $lastBayar['id']) }}" target="_blank">
                                        Resi
                                    </x-button>
                                @else
                                    <span class="text-xs text-stone-400 font-medium">-</span>
                                @endif
                            @else
                                <span class="text-xs text-stone-400 font-medium">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="5" title="Belum ada tagihan" message="Belum ada tagihan SPP yang tercatat untuk Anda." />
                @endforelse
            </tbody>
        </x-table>
    </div>
</div>
