<!-- Unified Cash Flow Jurnal Table with Checkbox Selection and Floating Bulk Action -->
<div 
    x-data="{
        selectedRows: @entangle('selectedTransactionIds'),
        allIds: {{ json_encode($paginatedTransactions->pluck('id')->toArray()) }},
        toggleAll(checked) {
            const current = Array.isArray(this.selectedRows) ? this.selectedRows.map(String) : [];
            if (checked) {
                this.selectedRows = Array.from(new Set([...current, ...this.allIds]));
            } else {
                this.selectedRows = current.filter(id => !this.allIds.includes(id));
            }
        },
        isAllSelected() {
            const current = Array.isArray(this.selectedRows) ? this.selectedRows.map(String) : [];
            return this.allIds.length > 0 && this.allIds.every(id => current.includes(id));
        }
    }"
    class="space-y-4"
>
    <x-table loadingTarget="search, tab, stream, filterPeriode, startDate, endDate, filterKategoriMasuk, filterKategoriKeluar, filterMetode, nominalMin, nominalMax, page, deleteIncome, deleteExpense, deleteSppPayment, bulkDeleteTransactions">
        <thead class="bg-stone-100/90 text-stone-700 font-extrabold uppercase tracking-wider text-[11px] border-b border-stone-200">
            <tr>
                @if (!auth()->user()->isSuperAdmin2())
                    <th class="w-10 p-3.5 text-center border-r border-stone-200">
                        <input type="checkbox" :checked="isAllSelected()" @change="toggleAll($event.target.checked)" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" title="Pilih semua transaksi pada halaman ini" />
                    </th>
                @endif
                <x-table.th class="w-32">Tanggal</x-table.th>
                <x-table.th align="center" class="w-24">Tipe</x-table.th>
                <x-table.th class="w-44">Stream / Sumber</x-table.th>
                <x-table.th class="w-40">Kategori / Pos</x-table.th>
                @if ($tab === 'semua' || $tab === 'masuk')
                    <x-table.th align="right" class="w-36 text-emerald-800">{{ $tab === 'masuk' ? 'Nominal Masuk (Rp)' : 'Kas Masuk (Rp)' }}</x-table.th>
                @endif
                @if ($tab === 'semua' || $tab === 'keluar')
                    <x-table.th align="right" class="w-36 text-rose-800">{{ $tab === 'keluar' ? 'Nominal Keluar (Rp)' : 'Kas Keluar (Rp)' }}</x-table.th>
                @endif
                <x-table.th class="min-w-[180px]">Keterangan / Rincian</x-table.th>
                <x-table.th align="center" class="w-32">Metode / Resi</x-table.th>
                <x-table.th align="center" class="w-24">Bukti</x-table.th>
                <x-table.th align="center" class="w-24">Aksi</x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100 bg-white">
            @php
                $tableColspan = ($tab === 'semua') ? (!auth()->user()->isSuperAdmin2() ? 11 : 10) : (!auth()->user()->isSuperAdmin2() ? 10 : 9);
            @endphp
            @forelse ($paginatedTransactions as $item)
                @php
                    $isPendingDelete = ($item->stream === 'operasional' && in_array($item->raw_id, $pendingApprovalPengeluaranIds ?? []))
                        || ($item->stream === 'infaq' && in_array($item->raw_id, $pendingApprovalPemasukanIds ?? []))
                        || ($item->stream === 'spp' && in_array($item->raw_id, $pendingApprovalPembayaranIds ?? []));
                @endphp
                <tr :class="selectedRows && selectedRows.map(String).includes('{{ $item->id }}') ? 'bg-emerald-50/40' : ({{ $isPendingDelete ? 'true' : 'false' }} ? 'bg-amber-50/75 hover:bg-amber-100/75 border-l-4 border-l-amber-500' : 'hover:bg-stone-50/80')" class="transition-colors">
                    @if (!auth()->user()->isSuperAdmin2())
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($isPendingDelete)
                                <span class="text-amber-500 font-bold" title="Sedang menunggu persetujuan Super Admin">•</span>
                            @else
                                <input type="checkbox" value="{{ $item->id }}" x-model="selectedRows" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                            @endif
                        </td>
                    @endif
                    <td class="p-3.5">
                        <div class="font-bold text-xs text-stone-900">{{ $item->tanggal->translatedFormat('d M Y') }}</div>
                        @if($item->tanggal->format('H:i') !== '00:00')
                            <div class="text-[10px] text-stone-400 font-mono">{{ $item->tanggal->format('H:i') }} WIB</div>
                        @endif
                    </td>
                    <td class="p-3.5 text-center">
                        @if ($item->type === 'masuk')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <x-lucide-arrow-down-left class="w-3 h-3 text-emerald-600" /> Masuk
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                <x-lucide-arrow-up-right class="w-3 h-3 text-rose-600" /> Keluar
                            </span>
                        @endif
                    </td>
                    <td class="p-3.5">
                        <x-badge :variant="$item->stream_badge" size="xs">
                            {{ $item->stream_label }}
                        </x-badge>
                    </td>
                    <td class="p-3.5 font-bold text-stone-800 text-xs">
                        {{ $item->kategori }}
                    </td>
                    @if ($tab === 'semua' || $tab === 'masuk')
                        <td class="p-3.5 text-right font-black text-emerald-700 text-xs {{ $item->nominal_masuk > 0 ? 'bg-emerald-50/20' : '' }}">
                            {{ $item->nominal_masuk > 0 ? ('Rp ' . number_format($item->nominal_masuk, 0, ',', '.')) : '-' }}
                        </td>
                    @endif
                    @if ($tab === 'semua' || $tab === 'keluar')
                        <td class="p-3.5 text-right font-black text-rose-700 text-xs {{ $item->nominal_keluar > 0 ? 'bg-rose-50/20' : '' }}">
                            {{ $item->nominal_keluar > 0 ? ('Rp ' . number_format($item->nominal_keluar, 0, ',', '.')) : '-' }}
                        </td>
                    @endif
                    <td class="p-3.5 text-xs text-stone-700 font-medium">
                        {{ $item->keterangan }}
                    </td>
                    <td class="p-3.5 text-center text-xs text-stone-600 font-medium">
                        <span class="font-bold block">{{ $item->metode_resi }}</span>
                        @if ($item->no_resi)
                            <span class="text-[10px] font-mono text-stone-400 block">{{ $item->no_resi }}</span>
                        @endif
                    </td>
                    <td class="p-3.5 text-center text-xs">
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
                        @elseif (in_array(auth()->user()->role?->nama, ['finance', 'super_admin', 'founder']) && !auth()->user()->isSuperAdmin2() && in_array(substr($item->id, 0, strpos($item->id, '_')), ['spp', 'infaq', 'op', 'bos', 'gaji', 'tab', 'loan']))
                            <button type="button" 
                                    wire:click="openUploadBuktiModal('{{ $item->id }}', '{{ addslashes($item->kategori) }}', '{{ addslashes($item->keterangan) }}', {{ $item->nominal_masuk > 0 ? $item->nominal_masuk : $item->nominal_keluar }})" 
                                    class="inline-flex items-center gap-1 px-2 py-1 bg-stone-50 hover:bg-emerald-50 text-stone-600 hover:text-emerald-700 border border-dashed border-stone-300 hover:border-emerald-300 rounded-lg text-[10px] font-semibold transition cursor-pointer shadow-2xs hover:scale-105 active:scale-95"
                                    title="Upload Foto Bukti">
                                <x-lucide-camera class="w-3.5 h-3.5 text-stone-400" />
                                <span>Bukti</span>
                            </button>
                        @else
                            <span class="text-xs text-stone-400 font-medium italic">-</span>
                        @endif
                    </td>
                    <td class="p-3.5 text-center">
                        <div class="flex items-center justify-center gap-1">
                            @if ($isPendingDelete)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-800 bg-amber-100/90 px-2 py-1 rounded-lg border border-amber-300 shadow-2xs" title="Penghapusan sedang menunggu persetujuan Super Admin">
                                    <x-lucide-clock class="w-3 h-3" />
                                    <span>Menunggu Persetujuan</span>
                                </span>
                            @else
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
                                        <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteIncome({{ $item->raw_id }})" loadingTarget="deleteIncome({{ $item->raw_id }})" data-confirm="{{ auth()->user()->role?->nama === 'finance' ? 'Ajukan permohonan penghapusan catatan penerimaan kas ini ke Super Admin / Super Admin 2?' : 'Hapus catatan penerimaan kas ini?' }}" title="Hapus Kas Masuk">
                                        </x-button>
                                    @else
                                        <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteExpense({{ $item->raw_id }})" loadingTarget="deleteExpense({{ $item->raw_id }})" data-confirm="{{ auth()->user()->role?->nama === 'finance' ? 'Ajukan permohonan penghapusan catatan pengeluaran kas ini ke Super Admin / Super Admin 2?' : 'Hapus catatan pengeluaran kas ini?' }}" title="Hapus Kas Keluar">
                                        </x-button>
                                    @endif
                                @elseif ($item->stream === 'spp' && $item->raw_id)
                                    <a href="{{ route('finance.cetak-resi', $item->raw_id) }}" target="_blank" class="p-1.5 bg-stone-100 hover:bg-emerald-100 text-stone-700 hover:text-emerald-900 rounded-lg inline-flex items-center justify-center border border-stone-300 transition" title="Cetak Resi">
                                        <x-lucide-printer class="w-3.5 h-3.5" />
                                    </a>
                                    @if (!auth()->user()->isSuperAdmin2() && in_array(auth()->user()->role?->nama, ['finance', 'super_admin', 'founder']))
                                        <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteSppPayment({{ $item->raw_id }})" loadingTarget="deleteSppPayment({{ $item->raw_id }})" data-confirm="{{ auth()->user()->role?->nama === 'finance' ? 'Ajukan permohonan pembatalan pembayaran SPP ini ke Super Admin / Super Admin 2?' : 'Batalkan dan hapus pembayaran SPP ini? Saldo tagihan dan deposit akan disesuaikan otomatis.' }}" title="Batalkan Pembayaran SPP">
                                        </x-button>
                                    @endif
                                @else
                                    <span class="text-[10px] text-stone-400 font-mono italic">Sistem</span>
                                @endif
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

    <!-- Floating Bulk Action Bar: Transaksi Arus Kas Terpilih (Instant Alpine UI) -->
    <div 
        x-show="selectedRows && selectedRows.length > 0"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-stone-900/95 backdrop-blur-md text-white px-5 py-3 rounded-2xl shadow-2xl border border-stone-700 flex items-center gap-4 max-w-[95vw] sm:max-w-xl flex-wrap sm:flex-nowrap justify-between sm:justify-start"
    >
        <div class="flex items-center gap-2">
            <span wire:loading.remove wire:target="bulkDeleteTransactions, openBulkUploadBuktiModal" class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
            <svg wire:loading wire:target="bulkDeleteTransactions, openBulkUploadBuktiModal" class="w-3.5 h-3.5 animate-spin text-amber-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span wire:loading.remove wire:target="bulkDeleteTransactions" class="text-xs font-black tracking-wide whitespace-nowrap"><span x-text="selectedRows ? selectedRows.length : 0"></span> Transaksi Terpilih</span>
            <span wire:loading wire:target="bulkDeleteTransactions" class="text-xs font-black tracking-wide whitespace-nowrap text-amber-300">Menghapus <span x-text="selectedRows ? selectedRows.length : 0"></span> Transaksi...</span>
        </div>

        <div class="h-4 w-px bg-stone-700 hidden sm:block"></div>

        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            <!-- Tombol Upload Bukti -->
            <button 
                type="button" 
                wire:loading.attr="disabled"
                wire:target="bulkDeleteTransactions, openBulkUploadBuktiModal"
                @click="$wire.set('selectedTransactionIds', selectedRows).then(() => $wire.openBulkUploadBuktiModal())" 
                class="min-h-[38px] px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 disabled:opacity-50 text-white font-black text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer disabled:cursor-not-allowed"
                title="Unggah satu foto bukti untuk semua transaksi kas yang dipilih">
                <x-lucide-camera wire:loading.remove wire:target="openBulkUploadBuktiModal" class="w-3.5 h-3.5 shrink-0" />
                <svg wire:loading wire:target="openBulkUploadBuktiModal" class="w-3.5 h-3.5 animate-spin shrink-0 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span wire:loading.remove wire:target="openBulkUploadBuktiModal">Upload Bukti (<span x-text="selectedRows ? selectedRows.length : 0"></span>)</span>
                <span wire:loading wire:target="openBulkUploadBuktiModal">Membuka...</span>
            </button>

            <!-- Tombol Hapus / Ajukan Hapus -->
            @if (auth()->user()->role?->nama === 'finance')
                <button 
                    type="button" 
                    wire:loading.attr="disabled"
                    wire:target="bulkDeleteTransactions, openBulkUploadBuktiModal"
                    @click="window.showAlert('Konfirmasi Tindakan', 'Ajukan permohonan penghapusan untuk ' + (selectedRows ? selectedRows.length : 0) + ' data transaksi kas terpilih ke Super Admin?', () => { $wire.set('selectedTransactionIds', selectedRows).then(() => $wire.bulkDeleteTransactions()); }, 'delete')"
                    class="min-h-[38px] px-3.5 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 disabled:opacity-50 text-stone-950 font-black text-xs rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer disabled:cursor-not-allowed"
                    title="Ajukan permohonan penghapusan transaksi terpilih">
                    <svg wire:loading wire:target="bulkDeleteTransactions" class="w-3.5 h-3.5 animate-spin shrink-0 text-stone-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <x-lucide-send wire:loading.remove wire:target="bulkDeleteTransactions" class="w-3.5 h-3.5 shrink-0" />
                    <span wire:loading.remove wire:target="bulkDeleteTransactions">Ajukan Hapus Terpilih</span>
                    <span wire:loading wire:target="bulkDeleteTransactions">Mengajukan Hapus...</span>
                </button>
            @elseif (in_array(auth()->user()->role?->nama, ['super_admin', 'founder']))
                <button 
                    type="button" 
                    wire:loading.attr="disabled"
                    wire:target="bulkDeleteTransactions, openBulkUploadBuktiModal"
                    @click="window.showAlert('Konfirmasi Tindakan', 'Yakin ingin menghapus ' + (selectedRows ? selectedRows.length : 0) + ' data transaksi kas terpilih?', () => { $wire.set('selectedTransactionIds', selectedRows).then(() => $wire.bulkDeleteTransactions()); }, 'delete')"
                    class="min-h-[38px] px-3.5 py-2 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 disabled:opacity-50 text-white font-black text-xs rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer disabled:cursor-not-allowed"
                    title="Hapus transaksi terpilih">
                    <svg wire:loading wire:target="bulkDeleteTransactions" class="w-3.5 h-3.5 animate-spin shrink-0 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <x-lucide-trash-2 wire:loading.remove wire:target="bulkDeleteTransactions" class="w-3.5 h-3.5 shrink-0" />
                    <span wire:loading.remove wire:target="bulkDeleteTransactions">Hapus Terpilih</span>
                    <span wire:loading wire:target="bulkDeleteTransactions">Menghapus Data...</span>
                </button>
            @endif

            <!-- Tombol Batal -->
            <button 
                type="button" 
                wire:loading.attr="disabled"
                wire:target="bulkDeleteTransactions"
                @click="selectedRows = []; $wire.set('selectedTransactionIds', [])" 
                class="min-h-[38px] px-3 py-2 bg-stone-800 hover:bg-stone-700 disabled:opacity-50 text-stone-300 hover:text-white font-bold text-xs rounded-xl transition cursor-pointer disabled:cursor-not-allowed">
                Batal
            </button>
        </div>
    </div>
</div>
