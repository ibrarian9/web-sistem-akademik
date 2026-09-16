<!-- ========================================================================= -->
<!-- 2. TABEL BAWAH: JURNAL & RIWAYAT SELURUH MUTASI TRANSAKSI TABUNGAN SISWA -->
<!-- ========================================================================= -->
<div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-xs space-y-5">
    <div class="flex items-center justify-between border-b border-stone-100 pb-3 flex-wrap gap-2">
        <div>
            <h3 class="text-sm font-black text-stone-900 uppercase tracking-tight flex items-center gap-2">
                <x-lucide-receipt class="w-4 h-4 text-emerald-600" />
                <span>Jurnal & Riwayat Seluruh Transaksi Tabungan</span>
            </h3>
            <p class="text-[11px] text-stone-500 font-medium">Seluruh mutasi setoran dan penarikan kas tabungan yang pernah diinputkan oleh petugas.</p>
        </div>
        <span class="text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-xl">
            {{ number_format($allHistoryTransactions->total()) }} Catatan Mutasi
        </span>
    </div>

    <!-- Controls: Filters for History Table -->
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1">Cari Mutasi</label>
                <x-search-input wire:model.live.debounce.300ms="historySearch" placeholder="Cari kode transaksi, nama santri, NIS, atau petugas..." />
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1">Jenis Transaksi</label>
                <select wire:model.live="historyJenis" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-2xs transition">
                    <option value="">Semua Jenis (Setor & Tarik)</option>
                    <option value="setor">Setoran Saja (+)</option>
                    <option value="tarik">Penarikan Saja (-)</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wider block mb-1">Filter Kelas</label>
                <select wire:model.live="historyFilterKelas" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-2xs transition">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Global Date Filter for History -->
        <div class="border-t border-stone-100 pt-3">
            <x-date-filter 
                model="historyFilterPeriode" 
                startDateModel="historyStartDate" 
                endDateModel="historyEndDate" 
                label="Filter Periode Tanggal Transaksi Mutasi" 
            />
        </div>

        <!-- Export Buttons for History Table -->
        <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-100 flex-wrap">
            <a 
                href="{{ route('finance.tabungan.pdf', array_filter(['view' => 'history', 'filter_periode' => $historyFilterPeriode, 'start_date' => $historyStartDate, 'end_date' => $historyEndDate, 'jenis' => $historyJenis, 'kelas_id' => $historyFilterKelas, 'search' => $historySearch])) }}" 
                target="_blank" 
                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-rose-50 text-rose-700 hover:bg-rose-100 hover:text-rose-800 border border-rose-200 rounded-xl text-xs font-bold transition shadow-2xs"
            >
                <x-lucide-printer class="w-4 h-4 text-rose-600" />
                <span>Cetak Jurnal Mutasi (PDF)</span>
            </a>

            <a 
                href="{{ route('finance.tabungan.excel', array_filter(['view' => 'history', 'filter_periode' => $historyFilterPeriode, 'start_date' => $historyStartDate, 'end_date' => $historyEndDate, 'jenis' => $historyJenis, 'kelas_id' => $historyFilterKelas, 'search' => $historySearch])) }}" 
                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold transition shadow-2xs"
            >
                <x-lucide-file-spreadsheet class="w-4 h-4 text-emerald-600" />
                <span>Ekspor Jurnal Mutasi (Excel)</span>
            </a>
        </div>
    </div>

    <!-- History Table -->
    <x-table loadingTarget="historySearch, historyJenis, historyFilterKelas, historyFilterPeriode, historyStartDate, historyEndDate, historyPage">
        <thead class="bg-stone-900 text-white font-extrabold uppercase tracking-wider border-b border-stone-950 text-[11px]">
            <tr>
                <x-table.th align="center" class="w-12">No</x-table.th>
                <x-table.th class="w-36">Tanggal & Kode</x-table.th>
                <x-table.th class="min-w-[200px]">Santri / Siswa</x-table.th>
                <x-table.th align="center" class="w-24">Kelas</x-table.th>
                <x-table.th align="center" class="w-24">Jenis</x-table.th>
                <x-table.th align="right" class="w-36">Nominal (Rp)</x-table.th>
                <x-table.th align="right" class="w-36">Saldo Akhir (Rp)</x-table.th>
                <x-table.th class="min-w-[160px]">Petugas / Catatan</x-table.th>
                <x-table.th align="center" class="w-28">Aksi</x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-200 bg-white">
            @forelse ($allHistoryTransactions as $htx)
                <tr class="hover:bg-stone-50 transition">
                    <td class="p-3.5 text-center text-xs text-stone-400 font-mono font-bold border-r border-stone-200">
                        {{ $loop->iteration + ($allHistoryTransactions->currentPage() - 1) * $allHistoryTransactions->perPage() }}
                    </td>
                    <td class="p-3.5 border-r border-stone-200">
                        <div class="font-bold text-xs text-stone-900">
                            {{ $htx->tanggal ? \Carbon\Carbon::parse($htx->tanggal)->translatedFormat('d M Y') : '-' }}
                        </div>
                        <div class="text-[10px] text-stone-400 font-mono">{{ $htx->kode_transaksi }}</div>
                    </td>
                    <td class="p-3.5 border-r border-stone-200">
                        <div class="font-bold text-xs text-stone-900">{{ $htx->siswa->user->nama ?? '-' }}</div>
                        <div class="text-[10px] text-stone-400 font-mono">NIS: {{ $htx->siswa->nis ?? '-' }}</div>
                    </td>
                    <td class="p-3.5 text-center border-r border-stone-200">
                        <x-badge variant="stone" size="xs">
                            {{ $htx->siswa->kelas->nama_kelas ?? '-' }}
                        </x-badge>
                    </td>
                    <td class="p-3.5 text-center border-r border-stone-200">
                        @if ($htx->jenis === 'setor')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                SETOR
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                TARIK
                            </span>
                        @endif
                    </td>
                    <td class="p-3.5 text-right font-bold text-xs border-r border-stone-200 {{ $htx->jenis === 'setor' ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $htx->jenis === 'setor' ? '+' : '-' }} Rp {{ number_format($htx->nominal, 0, ',', '.') }}
                    </td>
                    <td class="p-3.5 text-right font-black text-xs text-stone-900 border-r border-stone-200">
                        Rp {{ number_format($htx->saldo_akhir, 0, ',', '.') }}
                    </td>
                    <td class="p-3.5 border-r border-stone-200">
                        <div class="text-xs font-semibold text-stone-800">{{ $htx->petugas->nama ?? 'Sistem' }}</div>
                        @if ($htx->keterangan)
                            <div class="text-[10px] text-stone-400 italic leading-tight">{{ $htx->keterangan }}</div>
                        @endif
                    </td>
                    <td class="p-3.5 text-center">
                        <div class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            @if (!auth()->user()->isSuperAdmin2())
                                <x-button 
                                    type="button" 
                                    variant="secondary" 
                                    size="xs" 
                                    icon="edit-3" 
                                    wire:click="openEditTransaction({{ $htx->id }})" 
                                    title="Edit Transaksi Mutasi Ini"
                                >
                                    Edit
                                </x-button>

                                @if ($isFounder || auth()->user()->role?->nama === 'finance')
                                    <x-button 
                                        type="button" 
                                        variant="danger" 
                                        size="xs" 
                                        icon="trash-2" 
                                        wire:click="deleteTransaction({{ $htx->id }})" 
                                        data-confirm="{{ auth()->user()->role?->nama === 'finance' ? 'Ajukan permohonan penghapusan transaksi mutasi ini ke Super Admin / Super Admin 2?' : 'Hapus transaksi mutasi ini? Saldo tabungan santri terkait akan dihitung ulang secara otomatis.' }}" 
                                        title="Hapus Transaksi"
                                    />
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <x-table.empty :colspan="9" title="Tidak ada riwayat mutasi tabungan" message="Belum ada transaksi setoran atau penarikan yang sesuai filter." />
            @endforelse
        </tbody>
    </x-table>

    @if ($allHistoryTransactions->hasPages())
        <div class="pt-2">
            {{ $allHistoryTransactions->links() }}
        </div>
    @endif
</div>
