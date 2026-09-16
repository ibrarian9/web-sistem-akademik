<!-- Floating Card History Mutasi 1 Siswa -->
@if ($showHistoryModal && $selectedSiswaHistory)
    <x-floating-card 
        :show="true" 
        :title="$selectedSiswaHistory->user->nama ?? '-'" 
        :subtitle="'NIS: ' . $selectedSiswaHistory->nis . ' | Kelas: ' . ($selectedSiswaHistory->kelas->nama_kelas ?? '-')"
        badge="BUKU MUTASI SANTRI"
        badgeVariant="emerald"
        icon="list"
        maxWidth="max-w-3xl"
        closeAction="closeModals"
        zIndex="z-[99990]"
    >
        <div class="max-h-96 overflow-y-auto border border-stone-200 rounded-2xl">
            <x-table>
                <thead class="bg-emerald-900 text-white font-extrabold uppercase tracking-wider text-[10px] sticky top-0">
                    <tr>
                        <th class="p-3 text-left">Tanggal / Kode</th>
                        <th class="p-3 text-center">Jenis</th>
                        <th class="p-3 text-right">Nominal</th>
                        <th class="p-3 text-right">Saldo Akhir</th>
                        <th class="p-3 text-left">Petugas</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($historyTransactions as $tx)
                        <tr class="hover:bg-stone-50">
                            <td class="p-3 border-r border-stone-200">
                                <div class="font-bold text-xs text-stone-900">{{ \Carbon\Carbon::parse($tx->tanggal)->translatedFormat('d M Y') }}</div>
                                <div class="text-[10px] text-stone-400 font-mono">{{ $tx->kode_transaksi }}</div>
                            </td>
                            <td class="p-3 text-center border-r border-stone-200">
                                @if ($tx->jenis === 'setor')
                                     <x-badge variant="emerald" size="xs">Setor</x-badge>
                                @else
                                    <x-badge variant="amber" size="xs">Tarik</x-badge>
                                @endif
                            </td>
                            <td class="p-3 text-right font-black text-xs border-r border-stone-200 {{ $tx->jenis === 'setor' ? 'text-emerald-800' : 'text-amber-800' }}">
                                {{ $tx->jenis === 'setor' ? '+' : '-' }} Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-right font-black text-xs text-stone-900 border-r border-stone-200">
                                Rp {{ number_format($tx->saldo_akhir, 0, ',', '.') }}
                            </td>
                            <td class="p-3 border-r border-stone-200">
                                <div class="text-xs font-semibold text-stone-800">{{ $tx->petugas->nama ?? 'Sistem' }}</div>
                                @if ($tx->keterangan)
                                    <div class="text-[10px] text-stone-400 italic">{{ $tx->keterangan }}</div>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if (!auth()->user()->isSuperAdmin2())
                                        <!-- Edit Button (Founder & Finance) -->
                                        <x-button 
                                            type="button" 
                                            variant="secondary" 
                                            size="xs" 
                                            icon="edit-3" 
                                            wire:click="openEditTransaction({{ $tx->id }})" 
                                            title="Edit Transaksi">
                                            Edit
                                        </x-button>

                                        <!-- Delete Button (Founder & Finance) -->
                                        @if ($isFounder || auth()->user()->role?->nama === 'finance')
                                            <x-button 
                                                type="button" 
                                                variant="danger" 
                                                size="xs" 
                                                icon="trash-2" 
                                                wire:click="deleteTransaction({{ $tx->id }})" 
                                                data-confirm="{{ auth()->user()->role?->nama === 'finance' ? 'Ajukan permohonan penghapusan transaksi tabungan ini ke Super Admin / Super Admin 2?' : 'Apakah Anda yakin ingin menghapus catatan transaksi tabungan ini? Saldo tabungan siswa akan dihitung ulang secara otomatis.' }}" 
                                                title="Hapus Transaksi">
                                                Hapus
                                            </x-button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-stone-400 font-medium text-xs">
                                Belum ada riwayat mutasi tabungan untuk siswa ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-stone-200 flex-wrap gap-2">
            <div class="flex items-center gap-2 flex-wrap">
                @if ($selectedSiswaHistory)
                    <a href="{{ route('finance.tabungan.pdf', ['siswa_id' => $selectedSiswaHistory->id]) }}" 
                       target="_blank" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 rounded-xl text-xs font-bold transition shadow-2xs">
                        <x-lucide-printer class="w-3.5 h-3.5 text-rose-600" />
                        <span>Cetak Buku Tabungan (PDF)</span>
                    </a>

                    <a href="{{ route('finance.tabungan.excel', ['siswa_id' => $selectedSiswaHistory->id]) }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 rounded-xl text-xs font-bold transition shadow-2xs">
                        <x-lucide-file-spreadsheet class="w-3.5 h-3.5 text-emerald-600" />
                        <span>Ekspor Mutasi (Excel)</span>
                    </a>
                @endif
            </div>

            <x-button variant="secondary" size="md" wire:click="closeModals">
                Tutup
            </x-button>
        </div>
    </x-floating-card>
@endif
