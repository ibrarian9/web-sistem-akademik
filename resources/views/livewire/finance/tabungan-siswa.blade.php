<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <x-page-header 
        title="Manajemen Tabungan Siswa" 
        subtitle="Kelola transaksi setoran & penarikan tabungan siswa serta pantau saldo dan jurnal mutasi secara akurat."
        badge="TABUNGAN & SIMPANAN SISWA"
        badgeVariant="emerald"
        icon="wallet"
    />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Manajemen Tabungan Siswa"
        :steps="[
            ['title' => 'Tabel Saldo Santri', 'desc' => 'Tabel atas menampilkan saldo terkini setiap santri. Klik Setor atau Tarik untuk mencatat mutasi baru secara instan.'],
            ['title' => 'Jurnal & Riwayat Seluruh Mutasi', 'desc' => 'Tabel bawah menampilkan catatan kronologis seluruh mutasi tabungan yang pernah diinputkan, dilengkapi filter tanggal & ekspor PDF/Excel.'],
            ['title' => 'Koreksi Data', 'desc' => 'Finance dan Founder dapat mengedit atau menghapus entri transaksi tabungan secara langsung pada buku mutasi.']
        ]"
    />

    <!-- Alert Success / Error Notification -->
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

    <!-- ========================================================================= -->
    <!-- 1. TABEL UTAMA: SALDO TABUNGAN PER SISWA -->
    <!-- ========================================================================= -->
    <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-xs space-y-5">
        <div class="flex items-center justify-between border-b border-stone-100 pb-3 flex-wrap gap-2">
            <div>
                <h3 class="text-sm font-black text-stone-900 uppercase tracking-tight flex items-center gap-2">
                    <x-lucide-users class="w-4 h-4 text-emerald-600" />
                    <span>Daftar Santri & Saldo Tabungan</span>
                </h3>
                <p class="text-[11px] text-stone-500 font-medium">Rekapitulasi saldo tabungan masing-masing siswa per kelas.</p>
            </div>
            <span class="text-xs font-bold text-stone-500 bg-stone-100 px-3 py-1 rounded-xl">
                Total {{ number_format($siswas->total()) }} Siswa
            </span>
        </div>

        <!-- Controls: Search & Filter & Exports -->
        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama siswa, NIS, atau NISN..." />
            </div>

            <div class="flex items-center gap-2.5 flex-wrap justify-end">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-stone-500 uppercase tracking-wider shrink-0">Filter Kelas:</span>
                    <select wire:model.live="filterKelas" class="px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-2xs transition">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <a href="{{ route('finance.tabungan.pdf', array_filter(['kelas_id' => $filterKelas, 'search' => $search])) }}" 
                   target="_blank" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-rose-50 text-rose-700 hover:bg-rose-100 hover:text-rose-800 border border-rose-200 rounded-xl text-xs font-bold transition shadow-2xs">
                    <x-lucide-file-text class="w-4 h-4 text-rose-600" />
                    <span>Rekap Saldo PDF</span>
                </a>

                <a href="{{ route('finance.tabungan.excel', array_filter(['kelas_id' => $filterKelas, 'search' => $search])) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold transition shadow-2xs">
                    <x-lucide-file-spreadsheet class="w-4 h-4 text-emerald-600" />
                    <span>Rekap Saldo Excel</span>
                </a>
            </div>
        </div>

        <!-- Student Savings Table -->
        <x-table loadingTarget="search, filterKelas, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900 text-[11px]">
                <tr>
                    <x-table.th align="center" class="w-12">No</x-table.th>
                    <x-table.th class="min-w-[240px]">Siswa & Identitas</x-table.th>
                    <x-table.th class="w-36">NIS / NISN</x-table.th>
                    <x-table.th align="center" class="w-32">Kelas</x-table.th>
                    <x-table.th align="right" class="w-48">Saldo Tabungan</x-table.th>
                    <x-table.th align="center" class="w-64">Aksi Kas Tabungan</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($siswas as $siswa)
                    @php
                        $saldo = (float) ($siswa->latestTabungan->saldo_akhir ?? 0);
                        $initials = collect(explode(' ', $siswa->user->nama ?? 'S'))
                            ->map(fn($part) => substr($part, 0, 1))
                            ->take(2)
                            ->join('');
                    @endphp
                    <tr class="hover:bg-emerald-50/40 transition">
                        <td class="p-3.5 text-center text-xs text-stone-400 font-mono font-bold border-r border-stone-200">
                            {{ $loop->iteration + ($siswas->currentPage() - 1) * $siswas->perPage() }}
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xs shrink-0 border border-emerald-200 shadow-2xs">
                                    {{ strtoupper($initials ?: 'S') }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-xs text-stone-900 leading-snug">{{ $siswa->user->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-stone-500 font-medium">Wali: {{ $siswa->nama_wali ?: '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-mono font-bold text-stone-900 text-xs">{{ $siswa->nis }}</div>
                            <div class="font-mono text-[10px] text-stone-400">{{ $siswa->nisn ?: '-' }}</div>
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-stone-100 text-stone-700 border border-stone-200">
                                Kelas {{ $siswa->kelas->nama_kelas ?? '-' }}
                            </span>
                        </td>
                        <td class="p-3.5 text-right border-r border-stone-200">
                            @if ($saldo > 0)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-black text-emerald-800 bg-emerald-50 border border-emerald-200 shadow-2xs">
                                    Rp {{ number_format($saldo, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold text-stone-400 bg-stone-50 border border-stone-200">
                                    Rp 0
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                                <button 
                                    type="button" 
                                    wire:click="openTransactionModal({{ $siswa->id }}, 'setor')"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-2xs hover:shadow-xs cursor-pointer"
                                    title="Setor Tabungan Siswa (+)"
                                >
                                    <x-lucide-plus class="w-3.5 h-3.5" />
                                    <span>Setor</span>
                                </button>

                                <button 
                                    type="button" 
                                    wire:click="openTransactionModal({{ $siswa->id }}, 'tarik')"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-xs font-bold transition shadow-2xs hover:shadow-xs cursor-pointer"
                                    title="Penarikan Tabungan Siswa (-)"
                                >
                                    <x-lucide-minus class="w-3.5 h-3.5" />
                                    <span>Tarik</span>
                                </button>

                                <button 
                                    type="button" 
                                    wire:click="openHistoryModal({{ $siswa->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-300 rounded-xl text-xs font-bold transition shadow-2xs hover:shadow-xs cursor-pointer"
                                    title="Lihat Histori Buku Mutasi Tabungan Santri Ini"
                                >
                                    <x-lucide-history class="w-3.5 h-3.5 text-stone-500" />
                                    <span>Buku</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="6" title="Tidak ada data siswa ditemukan" message="Coba sesuaikan kata kunci pencarian atau filter kelas." />
                @endforelse
            </tbody>
        </x-table>

        @if ($siswas->hasPages())
            <div class="pt-2">
                {{ $siswas->links() }}
            </div>
        @endif
    </div>

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

                                @if ($isFounder)
                                    <x-button 
                                        type="button" 
                                        variant="danger" 
                                        size="xs" 
                                        icon="trash-2" 
                                        wire:click="deleteTransaction({{ $htx->id }})" 
                                        data-confirm="Hapus transaksi mutasi ini? Saldo tabungan santri terkait akan dihitung ulang secara otomatis." 
                                        title="Hapus Transaksi"
                                    />
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
        <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl flex items-center justify-between shadow-2xs mb-4">
            <span class="text-xs font-bold text-stone-600 uppercase tracking-wider">Saldo Tabungan Terkini:</span>
            <span class="text-base font-black text-emerald-800">Rp {{ number_format($selectedSiswaSaldo, 0, ',', '.') }}</span>
        </div>

        <form wire:submit.prevent="saveTransaction" class="space-y-4 font-sans">
            <!-- Jenis Transaksi -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Jenis Transaksi</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer text-xs font-bold select-none transition {{ $jenis === 'setor' ? 'bg-emerald-50 border-emerald-500 text-emerald-900 ring-2 ring-emerald-500/20 shadow-2xs' : 'bg-stone-50 border-stone-300 text-stone-600' }}">
                        <input type="radio" wire:model.live="jenis" value="setor" class="hidden" />
                        <x-lucide-arrow-down-left class="w-4 h-4 text-emerald-600" />
                        <span>Setor Tabungan (+)</span>
                    </label>
                    <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer text-xs font-bold select-none transition {{ $jenis === 'tarik' ? 'bg-amber-50 border-amber-500 text-amber-900 ring-2 ring-amber-500/20 shadow-2xs' : 'bg-stone-50 border-stone-300 text-stone-600' }}">
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

                                        <!-- Delete Button (Founder Only) -->
                                        @if ($isFounder)
                                            <x-button 
                                                type="button" 
                                                variant="danger" 
                                                size="xs" 
                                                icon="trash-2" 
                                                wire:click="deleteTransaction({{ $tx->id }})" 
                                                data-confirm="Apakah Anda yakin ingin menghapus catatan transaksi tabungan ini? Saldo tabungan siswa akan dihitung ulang secara otomatis." 
                                                title="Hapus Transaksi">
                                                Hapus
                                            </x-button>
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

    <!-- Floating Card Form Edit Transaksi Tabungan (Founder & Finance) - Top-level Overlay -->
    <x-floating-card 
        :show="$showEditTransactionModal" 
        title="Edit Transaksi Tabungan" 
        :subtitle="'Koreksi data mutasi untuk: ' . $edit_siswa_nama"
        badge="EDIT MUTASI TABUNGAN"
        badgeVariant="indigo"
        icon="edit-3"
        maxWidth="max-w-lg"
        closeAction="closeEditTransactionModal"
        zIndex="z-[99998]"
    >
        <form wire:submit.prevent="saveEditTransaction" class="space-y-4 font-sans">
            <!-- Jenis Transaksi -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Jenis Transaksi</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer text-xs font-bold select-none transition {{ $edit_jenis === 'setor' ? 'bg-emerald-50 border-emerald-500 text-emerald-900 ring-2 ring-emerald-500/20 shadow-2xs' : 'bg-stone-50 border-stone-300 text-stone-600' }}">
                        <input type="radio" wire:model.live="edit_jenis" value="setor" class="hidden" />
                        <x-lucide-arrow-down-left class="w-4 h-4 text-emerald-600" />
                        <span>Setor Tabungan (+)</span>
                    </label>
                    <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer text-xs font-bold select-none transition {{ $edit_jenis === 'tarik' ? 'bg-amber-50 border-amber-500 text-amber-900 ring-2 ring-amber-500/20 shadow-2xs' : 'bg-stone-50 border-stone-300 text-stone-600' }}">
                        <input type="radio" wire:model.live="edit_jenis" value="tarik" class="hidden" />
                        <x-lucide-arrow-up-right class="w-4 h-4 text-amber-600" />
                        <span>Tarik Tabungan (-)</span>
                    </label>
                </div>
            </div>

            <!-- Nominal -->
            <x-input-currency 
                label="Nominal Transaksi Baru (Rp)" 
                name="edit_nominal" 
                wire:model="edit_nominal" 
                placeholder="Contoh: 50.000" 
                required 
            />

            <!-- Tanggal Transaksi -->
            <x-input 
                type="date" 
                label="Tanggal Transaksi" 
                name="edit_tanggal" 
                wire:model="edit_tanggal" 
                required 
            />

            <!-- Keterangan -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Catatan / Keterangan</label>
                <textarea wire:model="edit_keterangan" rows="2" placeholder="Catatan transaksi tabungan (opsional)..." class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closeEditTransactionModal">
                    Batal
                </x-button>
                <x-button variant="primary" size="md" type="submit" loadingTarget="saveEditTransaction">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
