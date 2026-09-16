<!-- Unified Outflow Table -->
<x-table loadingTarget="search, filterKategori, filterPeriode, startDate, endDate, stream, page">
    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
        <tr>
            @if ($stream === 'operasional')
                <th class="w-12 p-3.5 text-center border-r border-emerald-700/60">
                    <input type="checkbox" wire:model.live="selectAll" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                </th>
            @endif
            <x-table.th class="w-36">Tanggal</x-table.th>
            <x-table.th class="w-48">Stream / Sumber</x-table.th>
            <x-table.th class="w-44">Kategori</x-table.th>
            <x-table.th align="right" class="w-44">Nominal Beban</x-table.th>
            <x-table.th class="min-w-[200px]">Keterangan / Penerima</x-table.th>
            <x-table.th align="center" class="w-28">Bukti Struk</x-table.th>
            <x-table.th align="center" class="w-36">Petugas / PIC</x-table.th>
            <x-table.th align="center" class="w-32">Aksi</x-table.th>
        </tr>
    </thead>
    <tbody class="divide-y divide-stone-200 bg-white">
        @forelse ($paginatedOutflows as $item)
            <tr class="hover:bg-rose-50/30 transition {{ in_array($item->raw_id, $selectedIds) ? 'bg-rose-50/60 font-semibold' : '' }}">
                @if ($stream === 'operasional')
                    <td class="p-3.5 text-center border-r border-stone-200">
                        <input type="checkbox" wire:model.live="selectedIds" value="{{ $item->raw_id }}" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                    </td>
                @endif
                <td class="p-3.5 border-r border-stone-200">
                    <div class="font-bold text-xs text-stone-900">{{ $item->tanggal->translatedFormat('d M Y') }}</div>
                    @if($item->tanggal->format('H:i') !== '00:00')
                        <div class="text-[10px] text-stone-400 font-mono">{{ $item->tanggal->format('H:i') }} WIB</div>
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
                <td class="p-3.5 text-right font-black text-rose-700 text-xs border-r border-stone-200">
                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                </td>
                <td class="p-3.5 text-xs text-stone-700 font-medium border-r border-stone-200">
                    {{ $item->keterangan }}
                </td>
                <td class="p-3.5 text-center border-r border-stone-200">
                    @if ($item->bukti)
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
                                 class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-lg text-[11px] font-bold shadow-2xs transition cursor-pointer">
                                <x-lucide-image class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                <span>Bukti</span>
                            </button>
                        </div>
                    @elseif ($item->can_edit && !auth()->user()->isSuperAdmin2())
                        <button 
                            type="button" 
                            wire:click="openEditModal({{ $item->raw_id }})" 
                            class="inline-flex items-center gap-1 px-2 py-1 bg-stone-50 hover:bg-stone-100 text-stone-500 hover:text-stone-800 border border-dashed border-stone-300 rounded-lg text-[10px] font-semibold transition cursor-pointer"
                            title="Upload Bukti Struk/TF"
                        >
                            <x-lucide-plus class="w-3 h-3 text-stone-400 shrink-0" />
                            <span>+ Bukti</span>
                        </button>
                    @else
                        <span class="text-xs text-stone-400 font-medium italic">-</span>
                    @endif
                </td>
                <td class="p-3.5 text-center text-xs font-semibold text-stone-600 border-r border-stone-200">
                    {{ $item->petugas }}
                </td>
                <td class="p-3.5 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        @if ($item->can_edit && !auth()->user()->isSuperAdmin2())
                            <x-button type="button" variant="outline" size="xs" icon="edit" wire:click="openEditModal({{ $item->raw_id }})" title="Edit Pengeluaran & Bukti">
                                Edit
                            </x-button>
                        @endif
                        @if ($item->can_delete && !auth()->user()->isSuperAdmin2())
                            <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteExpense({{ $item->raw_id }})" data-confirm="{{ auth()->user()->role?->nama === 'finance' ? 'Ajukan permohonan penghapusan catatan pengeluaran ini ke Super Admin / Super Admin 2?' : 'Apakah Anda yakin ingin menghapus catatan pengeluaran ini?' }}" title="Hapus Pengeluaran">
                                Hapus
                            </x-button>
                        @elseif(!$item->can_edit)
                            <span class="text-[10px] text-stone-400 font-mono italic">Terkunci</span>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <x-table.empty :colspan="$stream === 'operasional' ? 9 : 8" title="Belum ada transaksi kas keluar" message="Tidak ada catatan beban pengeluaran yang sesuai dengan filter yang dipilih." />
        @endforelse
    </tbody>
</x-table>

<!-- Pagination -->
<div class="pt-2">
    {{ $paginatedOutflows->links() }}
</div>
