<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <x-page-header 
        title="Manajemen Tabungan Siswa" 
        subtitle="Kelola transaksi setoran &amp; penarikan tabungan siswa serta pantau saldo terkini secara akurat."
        badge="TABUNGAN &amp; SIMPANAN SISWA"
        badgeVariant="emerald"
        icon="wallet"
    />

    <!-- Alert Success Notification -->
    @if (session()->has('success'))
        <x-alert-banner type="success" :message="session('success')" />
    @endif
    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card 
            title="Total Saldo Tabungan" 
            :value="'Rp ' . number_format($totalSaldoGlobal, 0, ',', '.')" 
            subtitle="Saldo kumulatif seluruh siswa"
            icon="wallet" 
            variant="white" 
        />
        <x-stat-card 
            title="Total Akumulasi Setor" 
            :value="'Rp ' . number_format($totalSetorAll, 0, ',', '.')" 
            subtitle="Total dana masuk tabungan"
            icon="arrow-down-left" 
            variant="white" 
        />
        <x-stat-card 
            title="Total Akumulasi Tarik" 
            :value="'Rp ' . number_format($totalTarikAll, 0, ',', '.')" 
            subtitle="Total dana ditarik siswa"
            icon="arrow-up-right" 
            variant="white" 
        />
        <x-stat-card 
            title="Siswa Aktif Menabung" 
            :value="number_format($jumlahSiswaMenabung) . ' Siswa'" 
            subtitle="Memiliki transaksi aktif"
            icon="users" 
            variant="white" 
        />
    </div>

    <!-- Content Table Card (Full Width) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Controls: Search & Filter -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama siswa, NIS, atau NISN..." />
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-stone-600 uppercase tracking-wider shrink-0">Filter Kelas:</span>
                <select wire:model.live="filterKelas" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Student Savings Table -->
        <x-table loadingTarget="search, filterKelas, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="min-w-[200px]">Siswa</x-table.th>
                    <x-table.th class="w-40">NIS / NISN</x-table.th>
                    <x-table.th class="w-36">Kelas</x-table.th>
                    <x-table.th align="right" class="w-48">Saldo Tabungan</x-table.th>
                    <x-table.th align="center" class="w-64">Aksi Transaksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($siswas as $siswa)
                    @php
                        $saldo = (float) ($siswa->latestTabungan->saldo_akhir ?? 0);
                    @endphp
                    <tr class="hover:bg-emerald-50/40 transition">
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ $siswa->user->nama ?? '-' }}</div>
                            <div class="text-[10px] text-stone-500 font-medium">Wali: {{ $siswa->nama_wali ?? '-' }}</div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-bold text-stone-800 text-xs">{{ $siswa->nis }}</div>
                            <div class="text-[10px] text-stone-400">{{ $siswa->nisn ?? '-' }}</div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <x-badge variant="stone" size="xs">
                                Kelas {{ $siswa->kelas->nama_kelas ?? '-' }}
                            </x-badge>
                        </td>
                        <td class="p-3.5 text-right font-black text-emerald-800 text-sm border-r border-stone-200">
                            Rp {{ number_format($saldo, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <x-button variant="primary" size="xs" icon="plus" wire:click="openTransactionModal({{ $siswa->id }}, 'setor')">
                                    Setor
                                </x-button>
                                <x-button variant="warning" size="xs" icon="minus" wire:click="openTransactionModal({{ $siswa->id }}, 'tarik')">
                                    Tarik
                                </x-button>
                                <x-button variant="secondary" size="xs" icon="history" wire:click="openHistoryModal({{ $siswa->id }})">
                                    Histori
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="5" title="Tidak ada data siswa ditemukan" message="Coba ubah kata kunci pencarian atau filter kelas." />
                @endforelse
            </tbody>
        </x-table>

        @if ($siswas->hasPages())
            <div class="pt-2">
                {{ $siswas->links() }}
            </div>
        @endif
    </div>

    <!-- Floating Card Form Transaction (Setor / Tarik) -->
    <x-floating-card 
        :show="$showTransactionModal" 
        :title="$jenis === 'setor' ? 'Setor Tabungan Siswa' : 'Penarikan Tabungan Siswa'" 
        :subtitle="$selectedSiswaNama"
        :badge="$jenis === 'setor' ? 'SETOR TABUNGAN (+)' : 'TARIK TABUNGAN (-)'"
        :badgeVariant="$jenis === 'setor' ? 'emerald' : 'amber'"
        icon="wallet"
        maxWidth="max-w-lg"
        closeAction="closeModals"
    >
        <!-- Info Current Balance -->
        <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-2xl flex items-center justify-between shadow-2xs">
            <span class="text-xs font-bold text-stone-600 uppercase tracking-wider">Saldo Saat Ini:</span>
            <span class="text-sm font-black text-emerald-800">Rp {{ number_format($selectedSiswaSaldo, 0, ',', '.') }}</span>
        </div>

        <form wire:submit.prevent="saveTransaction" class="space-y-4">
            <!-- Jenis Transaksi -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Jenis Transaksi</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer text-xs font-bold select-none transition {{ $jenis === 'setor' ? 'bg-emerald-50 border-emerald-500 text-emerald-900 ring-2 ring-emerald-500/20' : 'bg-stone-50 border-stone-300 text-stone-600' }}">
                        <input type="radio" wire:model.live="jenis" value="setor" class="hidden" />
                        <x-lucide-arrow-down-left class="w-4 h-4 text-emerald-600" />
                        <span>Setor Tabungan (+)</span>
                    </label>
                    <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer text-xs font-bold select-none transition {{ $jenis === 'tarik' ? 'bg-amber-50 border-amber-500 text-amber-900 ring-2 ring-amber-500/20' : 'bg-stone-50 border-stone-300 text-stone-600' }}">
                        <input type="radio" wire:model.live="jenis" value="tarik" class="hidden" />
                        <x-lucide-arrow-up-right class="w-4 h-4 text-amber-600" />
                        <span>Tarik Tabungan (-)</span>
                    </label>
                </div>
            </div>

            <!-- Nominal dengan Pemisah Titik & Logo Rp -->
            <x-input-currency 
                label="Nominal Transaksi (Rp)" 
                name="nominal" 
                wire:model="nominal" 
                placeholder="Contoh: 50.000" 
                required 
            />

            <!-- Tanggal Transaksi -->
            <x-input 
                type="date" 
                label="Tanggal Transaksi" 
                name="tanggal" 
                wire:model="tanggal" 
                required 
            />

            <!-- Keterangan -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Catatan / Keterangan</label>
                <textarea wire:model="keterangan" rows="2" placeholder="Catatan transaksi tabungan (opsional)..." class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closeModals">
                    Batal
                </x-button>
                <x-button variant="{{ $jenis === 'setor' ? 'primary' : 'warning' }}" size="md" type="submit" loadingTarget="saveTransaction">
                    Simpan Transaksi
                </x-button>
            </div>
        </form>
    </x-floating-card>

    <!-- Floating Card History Mutasi -->
    @if ($showHistoryModal && $selectedSiswaHistory)
        <x-floating-card 
            :show="true" 
            :title="$selectedSiswaHistory->user->nama ?? '-'" 
            :subtitle="'NIS: ' . $selectedSiswaHistory->nis . ' | Kelas: ' . ($selectedSiswaHistory->kelas->nama_kelas ?? '-')"
            badge="HISTORI MUTASI TABUNGAN"
            badgeVariant="emerald"
            icon="list"
            maxWidth="max-w-2xl"
            closeAction="closeModals"
        >
            <div class="max-h-96 overflow-y-auto border border-stone-200 rounded-2xl">
                <x-table>
                    <thead class="bg-stone-900 text-white font-extrabold uppercase tracking-wider text-[10px] sticky top-0">
                        <tr>
                            <th class="p-3 text-left">Tanggal / Kode</th>
                            <th class="p-3 text-center">Jenis</th>
                            <th class="p-3 text-right">Nominal</th>
                            <th class="p-3 text-right">Saldo Akhir</th>
                            <th class="p-3 text-left">Petugas</th>
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
                                <td class="p-3">
                                    <div class="text-xs font-semibold text-stone-800">{{ $tx->petugas->nama ?? 'Sistem' }}</div>
                                    @if ($tx->keterangan)
                                        <div class="text-[10px] text-stone-400 italic">{{ $tx->keterangan }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-stone-400 font-medium text-xs">
                                    Belum ada riwayat mutasi tabungan untuk siswa ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <div class="flex items-center justify-end pt-3 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closeModals">
                    Tutup
                </x-button>
            </div>
        </x-floating-card>
    @endif
</div>
