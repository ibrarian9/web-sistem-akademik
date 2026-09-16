<!-- Unified Cash Flow Jurnal Table -->
<x-table loadingTarget="search, tab, stream, filterPeriode, startDate, endDate, filterKategoriMasuk, filterKategoriKeluar, filterMetode, nominalMin, nominalMax, page">
    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
        <tr>
            <x-table.th class="w-32">Tanggal</x-table.th>
            <x-table.th align="center" class="w-24">Tipe</x-table.th>
            <x-table.th class="w-44">Stream / Sumber</x-table.th>
            <x-table.th class="w-40">Kategori / Pos</x-table.th>
            @if ($tab === 'semua' || $tab === 'masuk')
                <x-table.th align="right" class="w-36">{{ $tab === 'masuk' ? 'Nominal Masuk (Rp)' : 'Kas Masuk (Rp)' }}</x-table.th>
            @endif
            @if ($tab === 'semua' || $tab === 'keluar')
                <x-table.th align="right" class="w-36">{{ $tab === 'keluar' ? 'Nominal Keluar (Rp)' : 'Kas Keluar (Rp)' }}</x-table.th>
            @endif
            <x-table.th class="min-w-[180px]">Keterangan / Rincian</x-table.th>
            <x-table.th align="center" class="w-32">Metode / Resi</x-table.th>
            <x-table.th align="center" class="w-24">Bukti</x-table.th>
            <x-table.th align="center" class="w-24">Aksi</x-table.th>
        </tr>
    </thead>
    <tbody class="divide-y divide-stone-200 bg-white">
        @php
            $tableColspan = ($tab === 'semua') ? 10 : 9;
        @endphp
        @forelse ($paginatedTransactions as $item)
            <tr class="hover:bg-stone-50/80 transition">
                <td class="p-3.5 border-r border-stone-200">
                    <div class="font-bold text-xs text-stone-900">{{ $item->tanggal->translatedFormat('d M Y') }}</div>
                    @if($item->tanggal->format('H:i') !== '00:00')
                        <div class="text-[10px] text-stone-400 font-mono">{{ $item->tanggal->format('H:i') }} WIB</div>
                    @endif
                </td>
                <td class="p-3.5 text-center border-r border-stone-200">
                    @if ($item->type === 'masuk')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <x-lucide-arrow-down-left class="w-3 h-3 text-emerald-600" /> Masuk
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200">
                            <x-lucide-arrow-up-right class="w-3 h-3 text-rose-600" /> Keluar
                        </span>
                    @endif
                </td>
                <td class="p-3.5 border-r border-stone-200">
                    <x-badge :variant="$item->stream_badge" size="xs">
                        {{ $item->stream_label }}
                    </x-badge>
                </td>
                <td class="p-3.5 border-r border-stone-200 font-bold text-stone-800 text-xs">
                    {{ $item->kategori }}
                </td>
                @if ($tab === 'semua' || $tab === 'masuk')
                    <td class="p-3.5 text-right font-black text-emerald-700 text-xs border-r border-stone-200">
                        {{ $item->nominal_masuk > 0 ? ('Rp ' . number_format($item->nominal_masuk, 0, ',', '.')) : '-' }}
                    </td>
                @endif
                @if ($tab === 'semua' || $tab === 'keluar')
                    <td class="p-3.5 text-right font-black text-rose-700 text-xs border-r border-stone-200">
                        {{ $item->nominal_keluar > 0 ? ('Rp ' . number_format($item->nominal_keluar, 0, ',', '.')) : '-' }}
                    </td>
                @endif
                <td class="p-3.5 text-xs text-stone-700 font-medium border-r border-stone-200">
                    {{ $item->keterangan }}
                </td>
                <td class="p-3.5 text-center text-xs text-stone-600 border-r border-stone-200 font-medium">
                    <span class="font-bold block">{{ $item->metode_resi }}</span>
                    @if ($item->no_resi)
                        <span class="text-[10px] font-mono text-stone-400 block">{{ $item->no_resi }}</span>
                    @endif
                </td>
                <td class="p-3.5 text-center text-xs border-r border-stone-200">
                    @if (!empty($item->bukti))
                        <div class="flex items-center justify-center">
                            <img src="{{ asset('storage/' . $item->bukti) }}" 
                                 alt="Bukti {{ $item->kategori }}"
                                 @click="previewSrc = '{{ asset('storage/' . $item->bukti) }}'; previewTitle = 'Bukti {{ addslashes($item->kategori) }} ({{ $item->tanggal->format('d/m/Y') }})'; previewOpen = true"
                                 class="w-10 h-10 object-cover rounded-lg border border-stone-200 shadow-2xs cursor-pointer hover:opacity-80 hover:scale-105 transition duration-150"
                                 title="Klik untuk memperbesar bukti"
                                 loading="lazy"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';" />
                            <button type="button" 
                                 style="display: none;"
                                 @click="previewSrc = '{{ asset('storage/' . $item->bukti) }}'; previewTitle = 'Bukti {{ addslashes($item->kategori) }} ({{ $item->tanggal->format('d/m/Y') }})'; previewOpen = true"
                                 class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs text-[11px] font-bold cursor-pointer"
                                 title="Lihat Bukti">
                                <x-lucide-image class="w-3.5 h-3.5 text-emerald-600" />
                                <span>Lihat</span>
                            </button>
                        </div>
                    @else
                        <span class="text-xs text-stone-400 font-medium italic">-</span>
                    @endif
                </td>
                <td class="p-3.5 text-center">
                    <div class="flex items-center justify-center gap-1">
                        @if (isset($item->can_edit) && $item->can_edit && !auth()->user()->isSuperAdmin2())
                            <button type="button" 
                                wire:click="openEditExpenseModal({{ $item->raw_id }})" 
                                class="p-1.5 bg-stone-100 hover:bg-amber-100 text-stone-700 hover:text-amber-900 rounded-lg inline-flex items-center justify-center border border-stone-300 transition shadow-2xs cursor-pointer" 
                                title="Edit Transaksi & Bukti">
                                <x-lucide-edit-3 class="w-3.5 h-3.5 text-amber-600" />
                            </button>
                        @endif

                        @if ($item->can_delete && !auth()->user()->isSuperAdmin2())
                            @if ($item->type === 'masuk')
                                <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteIncome({{ $item->raw_id }})" data-confirm="{{ auth()->user()->role?->nama === 'finance' ? 'Ajukan permohonan penghapusan catatan penerimaan kas ini ke Super Admin / Super Admin 2?' : 'Hapus catatan penerimaan kas ini?' }}" title="Hapus Kas Masuk">
                                </x-button>
                            @else
                                <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteExpense({{ $item->raw_id }})" data-confirm="{{ auth()->user()->role?->nama === 'finance' ? 'Ajukan permohonan penghapusan catatan pengeluaran kas ini ke Super Admin / Super Admin 2?' : 'Hapus catatan pengeluaran kas ini?' }}" title="Hapus Kas Keluar">
                                </x-button>
                            @endif
                        @elseif ($item->stream === 'spp' && $item->raw_id)
                            <a href="{{ route('finance.cetak-resi', $item->raw_id) }}" target="_blank" class="p-1.5 bg-stone-100 hover:bg-emerald-100 text-stone-700 hover:text-emerald-900 rounded-lg inline-flex items-center justify-center border border-stone-300 transition" title="Cetak Resi">
                                <x-lucide-printer class="w-3.5 h-3.5" />
                            </a>
                        @else
                            <span class="text-[10px] text-stone-400 font-mono italic">Sistem</span>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <x-table.empty :colspan="$tableColspan" title="Belum ada catatan arus kas" message="Tidak ada transaksi pembukuan kas yang sesuai dengan filter yang dipilih." />
        @endforelse
    </tbody>
</x-table>

<!-- Pagination -->
<div class="pt-2">
    {{ $paginatedTransactions->links() }}
</div>
