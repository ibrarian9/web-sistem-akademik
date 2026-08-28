<div class="space-y-6 font-sans pb-16">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Honorarium & Gaji Pegawai" 
        subtitle="Kelola honorarium bulanan pegawai Yayasan F3, gaji pokok, berkala, insentif, ekskul, potongan sosial, kasbon, dan slip gaji digital."
        badge="PAYROLL YAYASAN"
        badgeVariant="emerald"
        icon="wallet"
    >
        <x-slot:actions>
            <x-button variant="secondary" size="md" icon="user-plus" wire:click="openCreateModal">
                Buat Gaji Manual
            </x-button>

            <x-button variant="primary" size="md" icon="calendar-plus" wire:click="openGenerateModal">
                Generate Draf Gaji
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-stone-500 block">Total Beban Anggaran</span>
                <span class="text-lg font-black text-stone-900 mt-0.5 block">Rp {{ number_format($statTotalAnggaran, 0, ',', '.') }}</span>
                <span class="text-[10px] text-stone-400 font-medium">Data terfilter saat ini</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shadow-2xs shrink-0">
                <x-lucide-calculator class="w-5 h-5" />
            </div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 block">Gaji Sudah Dibayar</span>
                <span class="text-lg font-black text-emerald-950 mt-0.5 block">Rp {{ number_format($statTotalDibayar, 0, ',', '.') }}</span>
                <span class="text-[10px] text-emerald-600 font-semibold">{{ $statCountDibayar }} Pegawai Selesai</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-xs shrink-0">
                <x-lucide-check-circle-2 class="w-5 h-5" />
            </div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 block">Gaji Masih Draf</span>
                <span class="text-lg font-black text-amber-950 mt-0.5 block">Rp {{ number_format($statTotalDraft, 0, ',', '.') }}</span>
                <span class="text-[10px] text-amber-600 font-semibold">{{ $statCountDraft }} Pegawai Menunggu</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shadow-2xs shrink-0">
                <x-lucide-clock class="w-5 h-5" />
            </div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-700 block">Potongan Kasbon</span>
                <span class="text-lg font-black text-rose-950 mt-0.5 block">Rp {{ number_format($statTotalKasbon, 0, ',', '.') }}</span>
                <span class="text-[10px] text-rose-500 font-medium">Cicilan terpotong</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center shadow-2xs shrink-0">
                <x-lucide-piggy-bank class="w-5 h-5" />
            </div>
        </div>
    </div>

    <!-- Search & Filters Bar (Full Width) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 sm:p-6 shadow-xs space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-1">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama pegawai / jabatan..." />
            </div>
            
            <div>
                <select wire:model.live="filterStatus" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="dibayar">Dibayar</option>
                </select>
            </div>

            <div>
                <select wire:model.live="filterBulan" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Bulan</option>
                    @foreach ($listBulan as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <input wire:model.live="filterTahun" type="number" placeholder="Tahun" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center shadow-2xs" />
            </div>
        </div>

        <!-- Bulk Actions Floating / Top Bar (When items are selected) -->
        @if (count($selectedGajiIds) > 0)
            <div class="bg-gradient-to-r from-emerald-900 to-emerald-800 text-white rounded-2xl p-4 shadow-md flex flex-col sm:flex-row items-center justify-between gap-4 border border-emerald-700">
                <div class="flex items-center gap-3.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600/80 text-white flex items-center justify-center font-black text-sm border border-emerald-500 shadow-2xs shrink-0">
                        {{ count($selectedGajiIds) }}
                    </div>
                    <div>
                        <div class="text-xs font-black tracking-wide text-white">{{ count($selectedGajiIds) }} Data Gaji Pegawai Terpilih</div>
                        <div class="text-[11px] text-emerald-200/80 font-medium">Pilih aksi untuk mengunduh slip PDF atau menghapus data sekaligus.</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto justify-end">
                    <x-button 
                        variant="primary" 
                        size="sm" 
                        icon="download" 
                        href="{{ route('finance.gaji-guru.bulk-slip', ['ids' => implode(',', $selectedGajiIds)]) }}"
                        :wireNavigate="false"
                        target="_blank"
                    >
                        Unduh ({{ count($selectedGajiIds) }}) Slip PDF
                    </x-button>

                    <x-button 
                        variant="danger" 
                        size="sm" 
                        icon="trash-2" 
                        wire:click="deleteSelected"
                        data-confirm="Apakah Anda yakin ingin menghapus {{ count($selectedGajiIds) }} data gaji yang dipilih? Data pengeluaran kas terkait juga akan disinkronkan."
                    >
                        Hapus ({{ count($selectedGajiIds) }}) Terpilih
                    </x-button>

                    <button 
                        type="button" 
                        wire:click="$set('selectedGajiIds', []); $set('selectAll', false)" 
                        class="px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-200 hover:text-white bg-white/10 hover:bg-white/20 transition cursor-pointer"
                    >
                        Batal
                    </button>
                </div>
            </div>
        @endif

        <!-- Salary List Table -->
        <x-table loadingTarget="search, filterStatus, filterBulan, filterTahun, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider select-none text-[11px]">
                <tr>
                    <th class="p-3.5 text-center w-10 border-b border-r border-emerald-700/60">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer" title="Pilih Semua" />
                    </th>
                    <x-table.th class="min-w-[240px]">Pegawai & Jabatan</x-table.th>
                    <x-table.th align="center" class="w-28">Periode</x-table.th>
                    <x-table.th align="right" class="w-36">Gaji Pokok</x-table.th>
                    <x-table.th align="right" class="w-40">Tunjangan / Insentif</x-table.th>
                    <x-table.th align="right" class="w-36">Potongan</x-table.th>
                    <x-table.th align="right" class="w-40">Take Home Pay</x-table.th>
                    <x-table.th align="center" class="w-24">Status</x-table.th>
                    <x-table.th align="center" class="w-48">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse ($salaries as $sal)
                    @php
                        $totalIns = $sal->insentif + $sal->honor_ekskul + $sal->insentif_bpjs + $sal->insentif_maghrib_mengaji;
                        $totalPot = $sal->potongan_sosial + $sal->potongan_peminjaman + $sal->potongan_bpjstk + $sal->potongan_lainnya;

                        $activeInsentifs = [];
                        if ($sal->insentif > 0) $activeInsentifs[] = 'Insentif: Rp ' . number_format($sal->insentif, 0, ',', '.');
                        if ($sal->honor_ekskul > 0) $activeInsentifs[] = 'Ekskul (' . $sal->jumlah_ekskul . 'x): Rp ' . number_format($sal->honor_ekskul, 0, ',', '.');
                        if ($sal->insentif_bpjs > 0) $activeInsentifs[] = 'BPJSTK: Rp ' . number_format($sal->insentif_bpjs, 0, ',', '.');
                        if ($sal->insentif_maghrib_mengaji > 0) $activeInsentifs[] = 'Maghrib: Rp ' . number_format($sal->insentif_maghrib_mengaji, 0, ',', '.');
                    @endphp
                    <tr class="hover:bg-emerald-50/40 transition group {{ in_array((string)$sal->id, $selectedGajiIds) ? 'bg-emerald-50/70' : '' }}">
                        <td class="p-3.5 text-center border-b border-r border-stone-200">
                            <input type="checkbox" wire:model.live="selectedGajiIds" value="{{ (string)$sal->id }}" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                        </td>
                        <td class="p-3.5 border-b border-r border-stone-200">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 font-black flex items-center justify-center text-xs shrink-0 shadow-2xs border border-emerald-200">
                                    {{ strtoupper(substr($sal->guru->user->nama ?? 'G', 0, 2)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <button 
                                        type="button" 
                                        wire:click="openDetailModal({{ $sal->id }})" 
                                        class="text-xs font-black text-stone-900 hover:text-emerald-700 leading-tight text-left transition cursor-pointer hover:underline block truncate"
                                        title="Klik untuk melihat rincian lengkap gaji"
                                    >
                                        {{ $sal->guru->user->nama ?? '-' }}
                                    </button>
                                    <div class="text-[11px] text-stone-500 font-medium mt-0.5 flex items-center gap-1.5 flex-wrap">
                                        <span class="text-emerald-800 font-bold">{{ $sal->jabatan ?: ($sal->guru->jabatan ?? 'Guru / Pegawai') }}</span>
                                        @if ($sal->guru->niy || $sal->guru->nip)
                                            <span class="text-stone-300">&bull;</span>
                                            <span class="font-mono text-[10px] text-stone-400">NIY: {{ $sal->guru->niy ?? $sal->guru->nip }}</span>
                                        @endif
                                        <span class="text-stone-300">&bull;</span>
                                        <a 
                                            href="{{ route('finance.gaji-guru.detail', $sal->guru_id) }}" 
                                            class="text-[10px] font-bold text-emerald-700 hover:text-emerald-900 hover:underline inline-flex items-center gap-0.5" 
                                            title="Buka seluruh riwayat penggajian pegawai ini"
                                        >
                                            <x-lucide-history class="w-3 h-3 text-emerald-600" />
                                            <span>Riwayat Gaji</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3.5 text-center border-b border-r border-stone-200">
                            <span class="px-2.5 py-1 bg-stone-100 border border-stone-200 rounded-lg text-xs font-bold text-stone-700 whitespace-nowrap inline-block">
                                {{ $sal->bulan }} {{ $sal->tahun }}
                            </span>
                        </td>
                        <td class="p-3.5 text-right border-b border-r border-stone-200">
                            <span class="font-extrabold text-stone-900 text-xs block whitespace-nowrap">Rp {{ number_format($sal->gaji_pokok, 0, ',', '.') }}</span>
                            @if ($sal->gaji_berkala > 0)
                                <span class="text-[10px] text-emerald-700 font-semibold block whitespace-nowrap">+ Berkala: Rp {{ number_format($sal->gaji_berkala, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-right border-b border-r border-stone-200">
                            @if ($totalIns > 0)
                                <span class="font-extrabold text-stone-900 text-xs block whitespace-nowrap">+Rp {{ number_format($totalIns, 0, ',', '.') }}</span>
                                @if (count($activeInsentifs) > 1)
                                    <div class="text-[10px] text-stone-500 font-medium space-y-0.5 mt-0.5">
                                        @foreach ($activeInsentifs as $insLabel)
                                            <span class="block whitespace-nowrap">{{ $insLabel }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <span class="text-stone-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-right border-b border-r border-stone-200">
                            @if ($totalPot > 0)
                                <span class="font-extrabold text-rose-700 text-xs block whitespace-nowrap">-Rp {{ number_format($totalPot, 0, ',', '.') }}</span>
                                @if ($sal->potongan_peminjaman > 0)
                                    <span class="text-[10px] text-rose-600 font-bold block whitespace-nowrap">Kasbon: Rp {{ number_format($sal->potongan_peminjaman, 0, ',', '.') }}</span>
                                @endif
                            @else
                                <span class="text-stone-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-right border-b border-r border-stone-200">
                            <span class="font-black text-xs sm:text-sm text-emerald-950 px-2.5 py-1 bg-emerald-50 border border-emerald-300 rounded-xl inline-block whitespace-nowrap shadow-2xs">
                                Rp {{ number_format($sal->total_diterima, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="p-3.5 text-center border-b border-r border-stone-200">
                            @if ($sal->status === 'dibayar')
                                <x-badge variant="emerald" size="xs" :dot="true">Dibayar</x-badge>
                            @else
                                <x-badge variant="amber" size="xs" :dot="true">Draft</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 text-center border-b border-stone-200">
                            <!-- Clean, Sleek Action Buttons Without Duplication -->
                            @if ($sal->status === 'draft')
                                <div class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                                    <button 
                                        type="button" 
                                        wire:click="paySalary({{ $sal->id }})" 
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-emerald-700 hover:bg-emerald-800 text-white shadow-xs hover:shadow-md transition"
                                        title="Bayar / Cairkan Gaji Ini"
                                    >
                                        <x-lucide-credit-card class="w-3.5 h-3.5" />
                                        <span>Bayar</span>
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="openEditModal({{ $sal->id }})" 
                                        class="p-1.5 rounded-xl text-stone-600 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 border border-stone-300 transition"
                                        title="Ubah Rincian Gaji"
                                    >
                                        <x-lucide-edit class="w-4 h-4" />
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="openDetailModal({{ $sal->id }})" 
                                        class="p-1.5 rounded-xl text-stone-600 hover:text-emerald-700 bg-stone-100 hover:bg-emerald-50 border border-stone-300 hover:border-emerald-300 transition"
                                        title="Lihat Detail Rincian Gaji"
                                    >
                                        <x-lucide-receipt class="w-4 h-4" />
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="deleteSalary({{ $sal->id }})" 
                                        data-confirm="Apakah Anda yakin ingin menghapus draf gaji ini?" 
                                        class="p-1.5 rounded-xl text-stone-400 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition"
                                        title="Hapus Draf"
                                    >
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </div>
                            @else
                                <div class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                                    <button 
                                        type="button" 
                                        wire:click="openPreview({{ $sal->id }})" 
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs transition"
                                        title="Pratinjau Slip Gaji PDF"
                                    >
                                        <x-lucide-eye class="w-3.5 h-3.5 text-emerald-700" />
                                        <span>Slip</span>
                                    </button>

                                    <a 
                                        href="{{ route('finance.gaji-guru.slip', ['id' => $sal->id, 'download' => 1]) }}" 
                                        target="_blank" 
                                        class="p-1.5 rounded-xl text-stone-600 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 border border-stone-300 transition"
                                        title="Unduh File PDF"
                                    >
                                        <x-lucide-download class="w-4 h-4" />
                                    </a>

                                    <button 
                                        type="button" 
                                        wire:click="openDetailModal({{ $sal->id }})" 
                                        class="p-1.5 rounded-xl text-stone-600 hover:text-emerald-700 bg-stone-100 hover:bg-emerald-50 border border-stone-300 hover:border-emerald-300 transition"
                                        title="Lihat Detail Rincian Gaji"
                                    >
                                        <x-lucide-receipt class="w-4 h-4" />
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="openEditModal({{ $sal->id }})" 
                                        class="p-1.5 rounded-xl text-stone-600 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 border border-stone-300 transition"
                                        title="Ubah Rincian Gaji"
                                    >
                                        <x-lucide-edit class="w-4 h-4" />
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="revertToDraft({{ $sal->id }})" 
                                        data-confirm="Batalkan status bayar dan kembalikan gaji ini ke Draf?" 
                                        class="p-1.5 rounded-xl text-stone-400 hover:text-amber-700 hover:bg-amber-50 border border-transparent hover:border-amber-200 transition"
                                        title="Batalkan Pembayaran (Kembalikan ke Draf)"
                                    >
                                        <x-lucide-rotate-ccw class="w-4 h-4" />
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="9" title="Belum ada data gaji" message="Klik 'Buat Gaji Manual' atau 'Generate Draf Gaji' untuk membuat data honorarium pegawai." />
                @endforelse
            </tbody>
        </x-table>

        @if ($salaries->hasPages())
            <div class="pt-4 border-t border-stone-200">
                {{ $salaries->links() }}
            </div>
        @endif
    </div>

    <!-- 1. Generate Modal (Massal dengan Pratinjau & Edit Pra-Generate dengan Pemisah Titik & Tanpa Panah) -->
    <x-floating-card 
        :show="$showGenerateModal" 
        title="Generate Draf Honorarium Pegawai" 
        subtitle="Tinjau, sesuaikan nominal gaji per guru, lalu generate draf sekaligus dalam satu klik."
        badge="DRAF PAYROLL PRA-GENERATE"
        badgeVariant="emerald"
        icon="calendar-plus"
        maxWidth="max-w-7xl"
        closeAction="closeGenerateModal"
    >
        <div class="space-y-4 font-sans">
            <!-- Filter Periode Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-emerald-50/60 p-3.5 rounded-2xl border border-emerald-200/80 items-center">
                <div>
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Bulan Gaji</label>
                    <select wire:model.live="generateBulan" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                        @foreach ($listBulan as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Tahun</label>
                    <input type="number" wire:model.live.debounce.400ms="generateTahun" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center" />
                </div>
                <div class="pt-3 sm:pt-0 sm:text-right">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 block">Status Draf:</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-700 text-white text-xs font-bold shadow-xs">
                        {{ count($generateItems) }} Pegawai Siap Digenerate
                    </span>
                </div>
            </div>

            <!-- Pre-Generation Table with Formatted Dot Separators and Clean Look -->
            @if (count($generateItems) > 0)
                <div class="overflow-x-auto max-h-[50vh] rounded-2xl border border-stone-200 shadow-inner custom-scrollbar bg-white">
                    <table class="w-full text-left border-separate border-spacing-0 text-xs text-stone-800">
                        <thead class="bg-stone-900 text-white font-extrabold uppercase tracking-wider sticky top-0 z-20 select-none">
                            <tr>
                                <th class="p-2.5 text-center w-10 border-b border-r border-stone-700">
                                    <input type="checkbox" wire:model.live="generateSelectAll" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer" title="Pilih Semua" />
                                </th>
                                <th class="p-2.5 border-b border-r border-stone-700 min-w-[150px]">Pegawai & Jabatan</th>
                                <th class="p-2.5 border-b border-r border-stone-700 w-32 text-right">Gaji Pokok (Rp)</th>
                                <th class="p-2.5 border-b border-r border-stone-700 w-28 text-right">Berkala (Rp)</th>
                                <th class="p-2.5 border-b border-r border-stone-700 w-32 text-right">Insentif (Rp)</th>
                                <th class="p-2.5 border-b border-r border-stone-700 w-32 text-right">Ekskul (Rp)</th>
                                <th class="p-2.5 border-b border-r border-stone-700 w-28 text-right">Pot. Sosial</th>
                                <th class="p-2.5 border-b border-r border-stone-700 w-28 text-right">Kasbon</th>
                                <th class="p-2.5 border-b border-stone-700 w-36 text-right">Take Home Pay</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200">
                            @foreach ($generateItems as $gId => $item)
                                <tr class="hover:bg-emerald-50/50 transition {{ !empty($item['selected']) ? 'bg-white' : 'bg-stone-50/80 opacity-60' }}">
                                    <td class="p-2.5 text-center border-b border-r border-stone-200">
                                        <input type="checkbox" wire:model.live="generateItems.{{ $gId }}.selected" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                                    </td>
                                    <td class="p-2.5 font-extrabold text-stone-900 text-xs border-b border-r border-stone-200">
                                        <div class="leading-tight">{{ $item['nama'] }}</div>
                                        <div class="text-[10px] text-emerald-700 font-bold mt-0.5">{{ $item['jabatan'] }}</div>
                                        <span class="text-[9px] text-stone-400 font-mono">NIY: {{ $item['nip'] }}</span>
                                    </td>
                                    
                                    <!-- Gaji Pokok Formatted Input -->
                                    <td class="p-2 border-b border-r border-stone-200 text-right">
                                        <div x-data="{
                                            val: @entangle('generateItems.' . $gId . '.gaji_pokok'),
                                            fmt: '',
                                            format(v) {
                                                if (v === null || v === undefined || v === '') return '0';
                                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                                let s = v.toString().trim();
                                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                                let clean = s.replace(/[^0-9]/g, '');
                                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                                            },
                                            onInput(e) {
                                                let c = e.target.value.replace(/[^0-9]/g, '');
                                                this.val = c ? parseInt(c, 10) : 0;
                                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                                e.target.value = this.fmt;
                                                $wire.recalculateGenerateRow({{ $gId }});
                                            },
                                            init() {
                                                this.fmt = this.format(this.val);
                                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                                            }
                                        }">
                                            <input 
                                                type="text" 
                                                inputmode="numeric" 
                                                x-model="fmt" 
                                                @input="onInput($event)"
                                                class="w-full px-2.5 py-1.5 bg-white border border-stone-300 rounded-lg text-stone-900 text-xs font-black text-right focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-2xs" 
                                            />
                                        </div>
                                    </td>

                                    <!-- Gaji Berkala Formatted Input -->
                                    <td class="p-2 border-b border-r border-stone-200 text-right">
                                        <div x-data="{
                                            val: @entangle('generateItems.' . $gId . '.gaji_berkala'),
                                            fmt: '',
                                            format(v) {
                                                if (v === null || v === undefined || v === '') return '0';
                                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                                let s = v.toString().trim();
                                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                                let clean = s.replace(/[^0-9]/g, '');
                                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                                            },
                                            onInput(e) {
                                                let c = e.target.value.replace(/[^0-9]/g, '');
                                                this.val = c ? parseInt(c, 10) : 0;
                                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                                e.target.value = this.fmt;
                                                $wire.recalculateGenerateRow({{ $gId }});
                                            },
                                            init() {
                                                this.fmt = this.format(this.val);
                                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                                            }
                                        }">
                                            <input 
                                                type="text" 
                                                inputmode="numeric" 
                                                x-model="fmt" 
                                                @input="onInput($event)"
                                                class="w-full px-2.5 py-1.5 bg-white border border-stone-300 rounded-lg text-stone-900 text-xs font-black text-right focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-2xs" 
                                            />
                                        </div>
                                    </td>

                                    <!-- Insentif Formatted Input -->
                                    <td class="p-2 border-b border-r border-stone-200 text-right">
                                        <div x-data="{
                                            val: @entangle('generateItems.' . $gId . '.insentif'),
                                            fmt: '',
                                            format(v) {
                                                if (v === null || v === undefined || v === '') return '0';
                                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                                let s = v.toString().trim();
                                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                                let clean = s.replace(/[^0-9]/g, '');
                                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                                            },
                                            onInput(e) {
                                                let c = e.target.value.replace(/[^0-9]/g, '');
                                                this.val = c ? parseInt(c, 10) : 0;
                                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                                e.target.value = this.fmt;
                                                $wire.recalculateGenerateRow({{ $gId }});
                                            },
                                            init() {
                                                this.fmt = this.format(this.val);
                                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                                            }
                                        }">
                                            <input 
                                                type="text" 
                                                inputmode="numeric" 
                                                x-model="fmt" 
                                                @input="onInput($event)"
                                                class="w-full px-2.5 py-1.5 bg-white border border-stone-300 rounded-lg text-stone-900 text-xs font-black text-right focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-2xs" 
                                            />
                                        </div>
                                    </td>

                                    <!-- Honor Ekskul Formatted Input -->
                                    <td class="p-2 border-b border-r border-stone-200 text-right">
                                        <div x-data="{
                                            val: @entangle('generateItems.' . $gId . '.honor_ekskul'),
                                            fmt: '',
                                            format(v) {
                                                if (v === null || v === undefined || v === '') return '0';
                                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                                let s = v.toString().trim();
                                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                                let clean = s.replace(/[^0-9]/g, '');
                                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                                            },
                                            onInput(e) {
                                                let c = e.target.value.replace(/[^0-9]/g, '');
                                                this.val = c ? parseInt(c, 10) : 0;
                                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                                e.target.value = this.fmt;
                                                $wire.recalculateGenerateRow({{ $gId }});
                                            },
                                            init() {
                                                this.fmt = this.format(this.val);
                                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                                            }
                                        }">
                                            <input 
                                                type="text" 
                                                inputmode="numeric" 
                                                x-model="fmt" 
                                                @input="onInput($event)"
                                                class="w-full px-2.5 py-1.5 bg-white border border-stone-300 rounded-lg text-stone-900 text-xs font-black text-right focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-2xs" 
                                            />
                                        </div>
                                    </td>

                                    <!-- Potongan Sosial Formatted Input -->
                                    <td class="p-2 border-b border-r border-stone-200 text-right">
                                        <div x-data="{
                                            val: @entangle('generateItems.' . $gId . '.potongan_sosial'),
                                            fmt: '',
                                            format(v) {
                                                if (v === null || v === undefined || v === '') return '0';
                                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                                let s = v.toString().trim();
                                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                                let clean = s.replace(/[^0-9]/g, '');
                                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                                            },
                                            onInput(e) {
                                                let c = e.target.value.replace(/[^0-9]/g, '');
                                                this.val = c ? parseInt(c, 10) : 0;
                                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                                e.target.value = this.fmt;
                                                $wire.recalculateGenerateRow({{ $gId }});
                                            },
                                            init() {
                                                this.fmt = this.format(this.val);
                                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                                            }
                                        }">
                                            <input 
                                                type="text" 
                                                inputmode="numeric" 
                                                x-model="fmt" 
                                                @input="onInput($event)"
                                                class="w-full px-2.5 py-1.5 bg-white border border-stone-300 rounded-lg text-stone-900 text-xs font-black text-right focus:ring-2 focus:ring-rose-500 focus:border-rose-500 shadow-2xs" 
                                            />
                                        </div>
                                    </td>

                                    <!-- Potongan Kasbon Formatted Input -->
                                    <td class="p-2 border-b border-r border-stone-200 text-right">
                                        <div x-data="{
                                            val: @entangle('generateItems.' . $gId . '.potongan_peminjaman'),
                                            fmt: '',
                                            format(v) {
                                                if (v === null || v === undefined || v === '') return '0';
                                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                                let s = v.toString().trim();
                                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                                let clean = s.replace(/[^0-9]/g, '');
                                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                                            },
                                            onInput(e) {
                                                let c = e.target.value.replace(/[^0-9]/g, '');
                                                this.val = c ? parseInt(c, 10) : 0;
                                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                                e.target.value = this.fmt;
                                                $wire.recalculateGenerateRow({{ $gId }});
                                            },
                                            init() {
                                                this.fmt = this.format(this.val);
                                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                                            }
                                        }">
                                            <input 
                                                type="text" 
                                                inputmode="numeric" 
                                                x-model="fmt" 
                                                @input="onInput($event)"
                                                class="w-full px-2.5 py-1.5 bg-white border border-stone-300 rounded-lg text-stone-900 text-xs font-black text-right focus:ring-2 focus:ring-rose-500 focus:border-rose-500 shadow-2xs" 
                                            />
                                        </div>
                                    </td>

                                    <!-- Calculated THP -->
                                    <td class="p-2.5 border-b border-stone-200 text-right font-black text-emerald-800 text-xs">
                                        <span class="px-2 py-1 rounded bg-emerald-50 border border-emerald-200 block text-right font-black text-emerald-900">
                                            Rp {{ number_format($item['total_diterima'], 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Bottom Summary & Actions -->
                @php
                    $selectedCount = count(array_filter($generateItems, fn($i) => !empty($i['selected'])));
                    $totalEstimatedThp = array_sum(array_map(fn($i) => !empty($i['selected']) ? $i['total_diterima'] : 0, $generateItems));
                @endphp
                <div class="p-4 bg-stone-900 text-white rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-3 shadow-md">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Ringkasan Draf Terpilih</span>
                        <div class="text-xs text-stone-200">
                            <strong>{{ $selectedCount }}</strong> dari {{ count($generateItems) }} pegawai dipilih &bull; Total THP: <strong class="text-emerald-400 font-black">Rp {{ number_format($totalEstimatedThp, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <x-button variant="secondary" size="md" wire:click="closeGenerateModal">Batal</x-button>
                        <x-button 
                            variant="primary" 
                            size="md" 
                            wire:click="generateDrafts" 
                            loadingTarget="generateDrafts"
                            :disabled="$selectedCount === 0"
                        >
                            Generate & Simpan ({{ $selectedCount }} Guru)
                        </x-button>
                    </div>
                </div>
            @else
                <div class="p-8 text-center bg-stone-50 rounded-2xl border border-stone-200 space-y-2">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto">
                        <x-lucide-check-circle-2 class="w-6 h-6" />
                    </div>
                    <h4 class="text-sm font-extrabold text-stone-900">Seluruh Pegawai Telah Memiliki Draf</h4>
                    <p class="text-xs text-stone-500 max-w-md mx-auto">
                        Tidak ada pegawai aktif yang belum digenerate untuk periode <strong>{{ $generateBulan }} {{ $generateTahun }}</strong>. Silakan pilih bulan/tahun lain atau ubah draf yang sudah ada di tabel utama.
                    </p>
                    <div class="pt-2">
                        <x-button variant="secondary" size="sm" wire:click="closeGenerateModal">Tutup</x-button>
                    </div>
                </div>
            @endif
        </div>
    </x-floating-card>

    <!-- 2. Buat Gaji Manual Modal (Satuan) dengan Format Titik Pemisah & Tanpa Panah -->
    <x-floating-card 
        :show="$showCreateModal" 
        title="Buat Gaji Pegawai Manual" 
        subtitle="Input honorarium atau gaji baru untuk seorang guru/pegawai secara spesifik."
        badge="INPUT GAJI BARU"
        badgeVariant="emerald"
        icon="user-plus"
        maxWidth="max-w-4xl"
        closeAction="closeCreateModal"
    >
        <div class="space-y-4 font-sans">
            <!-- Header Pegawai & Periode -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-emerald-50/50 p-3.5 rounded-2xl border border-emerald-200/80">
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Pilih Pegawai / Guru *</label>
                    <select wire:model.live="createGuruId" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                        <option value="">-- Pilih Guru --</option>
                        @foreach ($activeGurusList as $g)
                            <option value="{{ $g->id }}">{{ $g->user->nama ?? '-' }} ({{ $g->jabatan ?: ($g->jenis_guru === 'tahfidz' ? 'Tahfizh' : 'Guru') }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Bulan</label>
                    <select wire:model="createBulan" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                        @foreach ($listBulan as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Tahun</label>
                    <input type="number" wire:model="createTahun" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-stone-50 p-3.5 rounded-2xl border border-stone-200">
                <div>
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Jabatan</label>
                    <input type="text" wire:model="createJabatan" placeholder="Mudir F3 / Wali Tahfizh" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Jam Kerja</label>
                    <input type="text" wire:model="createJamKerja" placeholder="07.00-14.00" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Dibayar Oleh</label>
                    <input type="text" wire:model="createSumberDana" placeholder="Yayasan" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Status Awal</label>
                    <select wire:model="createStatus" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                        <option value="draft">Draf (Belum Bayar)</option>
                        <option value="dibayar">Langsung Dibayar</option>
                    </select>
                </div>
            </div>

            <!-- Two-Column Breakdown: Penerimaan vs Potongan -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- A. PENERIMAAN (EARNINGS) -->
                <div class="bg-emerald-50/40 border border-emerald-200/80 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-emerald-200 pb-2">
                        <span class="text-xs font-extrabold text-emerald-900 uppercase tracking-wider">A. Penerimaan (Earnings)</span>
                        <span class="text-xs font-black text-emerald-800">Bruto: Rp {{ number_format($createTotalBruto, 0, ',', '.') }}</span>
                    </div>

                    <div class="space-y-2.5">
                        <div x-data="{
                            val: @entangle('createGajiPokok'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateCreateTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">1. Gaji Pokok (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                        </div>

                        <div x-data="{
                            val: @entangle('createGajiBerkala'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateCreateTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">2. Gaji Berkala (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-bold text-stone-700 mb-1">3a. Pertemuan Ekskul</label>
                                <input type="number" min="0" wire:model.live.debounce.300ms="createJumlahEkskul" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center shadow-2xs" placeholder="0" />
                            </div>
                            <div x-data="{
                                val: @entangle('createHonorEkskul'),
                                fmt: '',
                                format(v) {
                                    if (v === null || v === undefined || v === '') return '0';
                                    if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                    let s = v.toString().trim();
                                    if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                    let clean = s.replace(/[^0-9]/g, '');
                                    return clean ? Number(clean).toLocaleString('id-ID') : '0';
                                },
                                onInput(e) {
                                    let c = e.target.value.replace(/[^0-9]/g, '');
                                    this.val = c ? parseInt(c, 10) : 0;
                                    this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                    e.target.value = this.fmt;
                                    $wire.calculateCreateTotal();
                                },
                                init() {
                                    this.fmt = this.format(this.val);
                                    this.$watch('val', (v) => { this.fmt = this.format(v); });
                                }
                            }">
                                <label class="block text-[11px] font-bold text-stone-700 mb-1">3b. Honor Ekskul (Rp)</label>
                                <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                            </div>
                        </div>

                        <div x-data="{
                            val: @entangle('createInsentif'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateCreateTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">4. Incentive / Insentif Jabatan (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                        </div>

                        <div x-data="{
                            val: @entangle('createInsentifBpjs'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateCreateTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">5. Tunjangan BPJSTK (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                        </div>
                    </div>
                </div>

                <!-- B. POTONGAN (DEDUCTIONS) -->
                <div class="bg-rose-50/40 border border-rose-200/80 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-rose-200 pb-2">
                        <span class="text-xs font-extrabold text-rose-900 uppercase tracking-wider">B. Potongan (Deductions)</span>
                        <span class="text-xs font-black text-rose-800">Total: Rp {{ number_format($createTotalPotongan, 0, ',', '.') }}</span>
                    </div>

                    <div class="space-y-2.5">
                        <div x-data="{
                            val: @entangle('createPotonganSosial'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateCreateTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">1. Potongan Sosial Yayasan (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-rose-500 shadow-2xs" />
                        </div>

                        <div x-data="{
                            val: @entangle('createPotonganPinjaman'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateCreateTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">2. Potongan Hutang / Kasbon Pinjaman (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-rose-500 shadow-2xs" />
                        </div>

                        <div x-data="{
                            val: @entangle('createPotonganBpjstk'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateCreateTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">3. Potongan Iuran BPJSTK (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-rose-500 shadow-2xs" />
                        </div>

                        <div x-data="{
                            val: @entangle('createPotonganLainnya'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateCreateTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">4. Potongan Lain-lain (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-rose-500 shadow-2xs" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Total Bar -->
            <div class="p-3.5 bg-emerald-700 text-white rounded-2xl flex items-center justify-between shadow-md">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-200 block">Total Gaji Bersih (Take Home Pay)</span>
                    <span class="text-xs text-emerald-100">Formula: Penerimaan Bruto - Total Potongan</span>
                </div>
                <div class="text-right">
                    <span class="text-xl sm:text-2xl font-black tracking-tight">Rp {{ number_format($createTotalDiterima, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closeCreateModal">Batal</x-button>
                <x-button variant="primary" size="md" wire:click="saveCreate" loadingTarget="saveCreate">
                    Simpan Gaji Pegawai
                </x-button>
            </div>
        </div>
    </x-floating-card>

    <!-- 3. Edit Salary Modal dengan Format Titik Pemisah & Tanpa Panah -->
    <x-floating-card 
        :show="$showEditModal" 
        title="Ubah Rincian Honorarium Pegawai" 
        :subtitle="'Pegawai: ' . ($editGuruNama ?? '-')"
        badge="UBAH RINCIAN GAJI"
        badgeVariant="emerald"
        icon="file-edit"
        maxWidth="max-w-4xl"
        closeAction="closeEditModal"
    >
        <div class="space-y-4 font-sans">
            @if ($editStatus === 'dibayar')
                <div class="p-3 bg-amber-50 border border-amber-300 rounded-xl text-xs text-amber-900 flex items-center gap-2">
                    <x-lucide-alert-circle class="w-4 h-4 text-amber-700 shrink-0" />
                    <span><strong>Gaji ini telah berstatus Dibayar:</strong> Mengubah nominal akan secara otomatis menyinkronkan nilai pengeluaran kas di Buku Kas Keuangan Yayasan.</span>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-stone-50 p-3.5 rounded-2xl border border-stone-200">
                <div>
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Jabatan</label>
                    <input type="text" wire:model="editJabatan" placeholder="Contoh: Mudir F3 / Guru" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Jam Kerja</label>
                    <input type="text" wire:model="editJamKerja" placeholder="07.00-14.00 (Fleksibel)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Dibayar Oleh</label>
                    <input type="text" wire:model="editSumberDana" placeholder="Yayasan" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Tanggal Bayar</label>
                    <input type="date" wire:model="editTanggalBayar" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center" />
                </div>
            </div>

            <!-- Two-Column Breakdown: Penerimaan vs Potongan -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- A. PENERIMAAN (EARNINGS) -->
                <div class="bg-emerald-50/40 border border-emerald-200/80 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-emerald-200 pb-2">
                        <span class="text-xs font-extrabold text-emerald-900 uppercase tracking-wider">A. Penerimaan (Earnings)</span>
                        <span class="text-xs font-black text-emerald-800">Bruto: Rp {{ number_format($editTotalBruto, 0, ',', '.') }}</span>
                    </div>

                    <div class="space-y-2.5">
                        <div x-data="{
                            val: @entangle('editGajiPokok'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateEditTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">1. Gaji Pokok (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                        </div>

                        <div x-data="{
                            val: @entangle('editGajiBerkala'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateEditTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">2. Gaji Berkala (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-bold text-stone-700 mb-1">3a. Pertemuan Ekskul</label>
                                <input type="number" min="0" wire:model.live.debounce.300ms="editJumlahEkskul" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center shadow-2xs" placeholder="0" />
                            </div>
                            <div x-data="{
                                val: @entangle('editHonorEkskul'),
                                fmt: '',
                                format(v) {
                                    if (v === null || v === undefined || v === '') return '0';
                                    if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                    let s = v.toString().trim();
                                    if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                    let clean = s.replace(/[^0-9]/g, '');
                                    return clean ? Number(clean).toLocaleString('id-ID') : '0';
                                },
                                onInput(e) {
                                    let c = e.target.value.replace(/[^0-9]/g, '');
                                    this.val = c ? parseInt(c, 10) : 0;
                                    this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                    e.target.value = this.fmt;
                                    $wire.calculateEditTotal();
                                },
                                init() {
                                    this.fmt = this.format(this.val);
                                    this.$watch('val', (v) => { this.fmt = this.format(v); });
                                }
                            }">
                                <label class="block text-[11px] font-bold text-stone-700 mb-1">3b. Honor Ekskul (Rp)</label>
                                <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                            </div>
                        </div>

                        <div x-data="{
                            val: @entangle('editInsentif'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateEditTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">4. Incentive / Insentif Jabatan (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                        </div>

                        <div x-data="{
                            val: @entangle('editInsentifBpjs'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateEditTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">5. Tunjangan BPJSTK (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                        </div>
                    </div>
                </div>

                <!-- B. POTONGAN (DEDUCTIONS) -->
                <div class="bg-rose-50/40 border border-rose-200/80 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-rose-200 pb-2">
                        <span class="text-xs font-extrabold text-rose-900 uppercase tracking-wider">B. Potongan (Deductions)</span>
                        <span class="text-xs font-black text-rose-800">Total: Rp {{ number_format($editTotalPotongan, 0, ',', '.') }}</span>
                    </div>

                    <div class="space-y-2.5">
                        <div x-data="{
                            val: @entangle('editPotonganSosial'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateEditTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">1. Potongan Sosial Yayasan (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-rose-500 shadow-2xs" />
                        </div>

                        <div x-data="{
                            val: @entangle('editPotonganPinjaman'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateEditTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">2. Potongan Hutang / Kasbon Pinjaman (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-rose-500 shadow-2xs" />
                        </div>

                        <div x-data="{
                            val: @entangle('editPotonganBpjstk'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateEditTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">3. Potongan Iuran BPJSTK (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-rose-500 shadow-2xs" />
                        </div>

                        <div x-data="{
                            val: @entangle('editPotonganLainnya'),
                            fmt: '',
                            format(v) {
                                if (v === null || v === undefined || v === '') return '0';
                                if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
                                let s = v.toString().trim();
                                if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
                                let clean = s.replace(/[^0-9]/g, '');
                                return clean ? Number(clean).toLocaleString('id-ID') : '0';
                            },
                            onInput(e) {
                                let c = e.target.value.replace(/[^0-9]/g, '');
                                this.val = c ? parseInt(c, 10) : 0;
                                this.fmt = c ? Number(c).toLocaleString('id-ID') : '';
                                e.target.value = this.fmt;
                                $wire.calculateEditTotal();
                            },
                            init() {
                                this.fmt = this.format(this.val);
                                this.$watch('val', (v) => { this.fmt = this.format(v); });
                            }
                        }">
                            <label class="block text-[11px] font-bold text-stone-700 mb-1">4. Potongan Lain-lain (Rp)</label>
                            <input type="text" inputmode="numeric" x-model="fmt" @input="onInput($event)" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold text-right focus:ring-2 focus:ring-rose-500 shadow-2xs" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Total Bar -->
            <div class="p-3.5 bg-emerald-700 text-white rounded-2xl flex items-center justify-between shadow-md">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-200 block">Total Gaji Bersih (Take Home Pay)</span>
                    <span class="text-xs text-emerald-100">Formula: Penerimaan Bruto - Total Potongan</span>
                </div>
                <div class="text-right">
                    <span class="text-xl sm:text-2xl font-black tracking-tight">Rp {{ number_format($editTotalDiterima, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closeEditModal">Batal</x-button>
                <x-button variant="primary" size="md" wire:click="saveEdit" loadingTarget="saveEdit">
                    Simpan Perubahan
                </x-button>
            </div>
        </div>
    </x-floating-card>

    <!-- 4. Modal Detail Rincian Gaji Pegawai -->
    @if ($showDetailModal && $selectedSalaryDetail)
        <x-floating-card 
            :show="true" 
            title="Rincian Lengkap Honorarium Pegawai" 
            :subtitle="'Periode: ' . ($selectedSalaryDetail->bulan ?? '') . ' ' . ($selectedSalaryDetail->tahun ?? '') . ' — ' . ($selectedSalaryDetail->guru->user->nama ?? '-')" 
            badge="DETAIL RINCIAN GAJI" 
            badgeVariant="emerald" 
            icon="receipt" 
            maxWidth="max-w-5xl" 
            closeAction="closeDetailModal"
            zIndex="z-[99990]"
        >
            <div class="space-y-5 font-sans">
                <!-- Profile & Header Summary Card -->
                <div class="bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-900 p-5 rounded-2xl text-white shadow-md flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-xl font-black text-white shrink-0 shadow-inner">
                            {{ strtoupper(substr($selectedSalaryDetail->guru->user->nama ?? 'G', 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base sm:text-lg font-black text-white leading-tight">
                                    {{ $selectedSalaryDetail->guru->user->nama ?? '-' }}
                                </h3>
                                @if ($selectedSalaryDetail->status === 'dibayar')
                                    <span class="px-2.5 py-0.5 bg-emerald-500/30 border border-emerald-400/50 text-emerald-200 rounded-full text-[10px] font-extrabold uppercase">
                                        ● Dibayar
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-amber-500/30 border border-amber-400/50 text-amber-200 rounded-full text-[10px] font-extrabold uppercase">
                                        ● Draf
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-emerald-100 font-semibold mt-0.5">
                                {{ $selectedSalaryDetail->jabatan ?: ($selectedSalaryDetail->guru->jabatan ?? 'Guru / Pegawai') }} &bull; NIY: {{ $selectedSalaryDetail->guru->niy ?? ($selectedSalaryDetail->guru->nip ?? '-') }}
                            </p>
                            <p class="text-[11px] text-emerald-200/80 font-mono mt-0.5">
                                Periode: <strong class="text-white">{{ $selectedSalaryDetail->bulan }} {{ $selectedSalaryDetail->tahun }}</strong> &bull; Jam Kerja: {{ $selectedSalaryDetail->jam_kerja ?: '07.00-14.00' }} &bull; Sumber: {{ $selectedSalaryDetail->sumber_dana ?: 'Yayasan' }}
                            </p>
                        </div>
                    </div>

                    <a 
                        href="{{ route('finance.gaji-guru.detail', $selectedSalaryDetail->guru_id) }}" 
                        class="px-3.5 py-2 bg-white/15 hover:bg-white/25 text-white border border-white/30 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 shadow-xs"
                        title="Buka seluruh histori penggajian pegawai ini"
                    >
                        <x-lucide-history class="w-4 h-4 text-emerald-300" />
                        <span>Riwayat Gaji Pegawai</span>
                    </a>
                </div>

                <!-- 2-Column Earnings & Deductions Breakdown -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <!-- Column Left: Penerimaan (A) -->
                    <div class="bg-emerald-50/50 border border-emerald-200/80 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between border-b border-emerald-200 pb-2">
                            <span class="text-xs font-black text-emerald-950 uppercase tracking-wider flex items-center gap-1.5">
                                <x-lucide-plus-circle class="w-4 h-4 text-emerald-700" />
                                A. Komponen Penerimaan
                            </span>
                            <span class="text-[10px] font-extrabold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-md border border-emerald-300">
                                Bruto
                            </span>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1.5 border-b border-emerald-100">
                                <span class="text-stone-700 font-medium">1. Gaji Pokok</span>
                                <span class="font-extrabold text-stone-900">Rp {{ number_format($selectedSalaryDetail->gaji_pokok, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-emerald-100">
                                <span class="text-stone-700 font-medium">2. Gaji Berkala</span>
                                <span class="font-extrabold text-stone-900">Rp {{ number_format($selectedSalaryDetail->gaji_berkala, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-emerald-100">
                                <span class="text-stone-700 font-medium">3. Insentif Kehadiran & Tugas</span>
                                <span class="font-extrabold text-stone-900">Rp {{ number_format($selectedSalaryDetail->insentif, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-emerald-100">
                                <span class="text-stone-700 font-medium">4. Honor Ekskul ({{ $selectedSalaryDetail->jumlah_ekskul }}x pertemuan)</span>
                                <span class="font-extrabold text-cyan-800">Rp {{ number_format($selectedSalaryDetail->honor_ekskul, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-emerald-100">
                                <span class="text-stone-700 font-medium">5. Insentif BPJS Ketenagakerjaan</span>
                                <span class="font-extrabold text-indigo-800">Rp {{ number_format($selectedSalaryDetail->insentif_bpjs, 0, ',', '.') }}</span>
                            </div>
                            @if ($selectedSalaryDetail->insentif_maghrib_mengaji > 0)
                                <div class="flex justify-between py-1.5 border-b border-emerald-100">
                                    <span class="text-stone-700 font-medium">6. Insentif Maghrib Mengaji</span>
                                    <span class="font-extrabold text-stone-900">Rp {{ number_format($selectedSalaryDetail->insentif_maghrib_mengaji, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="pt-2 border-t-2 border-emerald-300 flex justify-between items-center text-xs font-black text-emerald-950">
                            <span>TOTAL PENERIMAAN (A)</span>
                            <span class="text-sm font-black text-emerald-900">
                                Rp {{ number_format($selectedSalaryDetail->total_bruto ?: ($selectedSalaryDetail->gaji_pokok + $selectedSalaryDetail->gaji_berkala + $selectedSalaryDetail->insentif + $selectedSalaryDetail->honor_ekskul + $selectedSalaryDetail->insentif_bpjs + $selectedSalaryDetail->insentif_maghrib_mengaji), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Column Right: Potongan (B) -->
                    <div class="bg-rose-50/50 border border-rose-200/80 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between border-b border-rose-200 pb-2">
                            <span class="text-xs font-black text-rose-950 uppercase tracking-wider flex items-center gap-1.5">
                                <x-lucide-minus-circle class="w-4 h-4 text-rose-700" />
                                B. Komponen Potongan
                            </span>
                            <span class="text-[10px] font-extrabold text-rose-800 bg-rose-100 px-2 py-0.5 rounded-md border border-rose-300">
                                Deduksi
                            </span>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1.5 border-b border-rose-100">
                                <span class="text-stone-700 font-medium">1. Iuran Sosial Yayasan</span>
                                <span class="font-extrabold text-stone-900">Rp {{ number_format($selectedSalaryDetail->potongan_sosial, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-rose-100">
                                <div>
                                    <span class="text-stone-700 font-medium block">2. Potongan Kasbon / Pinjaman</span>
                                    @if ($selectedSalaryDetail->potongan_peminjaman > 0)
                                        <span class="text-[10px] text-rose-600 font-semibold block">Otomatis memotong cicilan kasbon</span>
                                    @endif
                                </div>
                                <span class="font-extrabold text-rose-700">Rp {{ number_format($selectedSalaryDetail->potongan_peminjaman, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-rose-100">
                                <span class="text-stone-700 font-medium">3. Potongan BPJSTK Karyawan</span>
                                <span class="font-extrabold text-amber-800">Rp {{ number_format($selectedSalaryDetail->potongan_bpjstk, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-rose-100">
                                <span class="text-stone-700 font-medium">4. Potongan Lain-lain</span>
                                <span class="font-extrabold text-stone-900">Rp {{ number_format($selectedSalaryDetail->potongan_lainnya, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t-2 border-rose-300 flex justify-between items-center text-xs font-black text-rose-950">
                            <span>TOTAL POTONGAN (B)</span>
                            <span class="text-sm font-black text-rose-800">
                                Rp {{ number_format($selectedSalaryDetail->total_potongan ?: ($selectedSalaryDetail->potongan_sosial + $selectedSalaryDetail->potongan_peminjaman + $selectedSalaryDetail->potongan_bpjstk + $selectedSalaryDetail->potongan_lainnya), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Highlight Box Take Home Pay (A - B) -->
                <div class="p-4 bg-emerald-800 text-white rounded-2xl shadow-md space-y-1">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-300 block">
                                TOTAL DITERIMA / TAKE HOME PAY (THP)
                            </span>
                            <span class="text-xs text-emerald-100 font-medium">
                                Formula: Penerimaan Bersih = Total Komponen (A) - Total Komponen (B)
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                                Rp {{ number_format($selectedSalaryDetail->total_diterima, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-emerald-700/60 text-xs text-emerald-100 italic">
                        Terbilang: <strong>{{ \App\Http\Controllers\FinanceReportController::terbilang($selectedSalaryDetail->total_diterima) }} Rupiah</strong>
                    </div>
                </div>

                <!-- Info Pembukuan Arus Kas Keuangan -->
                <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 bg-white border border-stone-300 rounded-xl text-stone-700">
                            <x-lucide-landmark class="w-4 h-4 text-emerald-700" />
                        </div>
                        <div>
                            <div class="font-bold text-stone-900">
                                Status Kas: 
                                @if ($selectedSalaryDetail->status === 'dibayar')
                                    <span class="text-emerald-700 font-black">Tercatat di Pengeluaran Kas Yayasan</span>
                                    @if ($selectedSalaryDetail->pengeluaran_id)
                                        <span class="text-stone-500 font-mono font-normal">(Ref ID: #{{ $selectedSalaryDetail->pengeluaran_id }})</span>
                                    @endif
                                @else
                                    <span class="text-amber-700 font-black">Draf (Belum Dicairkan / Belum Mengurangi Kas)</span>
                                @endif
                            </div>
                            <div class="text-stone-500 text-[11px] mt-0.5">
                                Tanggal Pembayaran: {{ $selectedSalaryDetail->tanggal_bayar ? $selectedSalaryDetail->tanggal_bayar->format('d/m/Y') : 'Belum dibayarkan' }} &bull; Metode: Kas Utama Yayasan
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        @if ($selectedSalaryDetail->status === 'draft')
                            <x-button variant="primary" size="sm" icon="credit-card" wire:click="paySalary({{ $selectedSalaryDetail->id }})">
                                Bayar Sekarang
                            </x-button>
                        @else
                            <x-button variant="secondary" size="sm" icon="rotate-ccw" wire:click="revertToDraft({{ $selectedSalaryDetail->id }})" data-confirm="Kembalikan status gaji ini ke Draf?">
                                Batal Bayar
                            </x-button>
                        @endif
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-stone-200">
                    <div class="flex items-center gap-2">
                        <x-button variant="secondary" size="md" icon="edit" wire:click="openEditModal({{ $selectedSalaryDetail->id }})">
                            Ubah Rincian
                        </x-button>
                        
                        <x-button variant="secondary" size="md" icon="eye" wire:click="openPreview({{ $selectedSalaryDetail->id }})">
                            Pratinjau Slip
                        </x-button>

                        <a 
                            href="{{ route('finance.gaji-guru.slip', ['id' => $selectedSalaryDetail->id, 'download' => 1]) }}" 
                            target="_blank" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold bg-stone-100 hover:bg-stone-200 text-stone-800 border border-stone-300 transition shadow-2xs"
                        >
                            <x-lucide-download class="w-4 h-4 text-stone-700" />
                            <span>Unduh PDF</span>
                        </a>
                    </div>

                    <x-button variant="secondary" size="md" wire:click="closeDetailModal">
                        Tutup
                    </x-button>
                </div>
            </div>
        </x-floating-card>
    @endif

    <!-- 5. PDF Preview Modal (Rendered on top with highest z-index) -->
    @if ($showPreviewModal && $previewSalary)
        <x-floating-card 
            :show="true" 
            title="Pratinjau Dokumen Slip Gaji Digital" 
            :subtitle="'Periode: ' . $previewSalary->bulan . ' ' . $previewSalary->tahun . ' — ' . ($previewSalary->guru->user->nama ?? '-')"
            badge="SLIP GAJI RESMI"
            badgeVariant="emerald"
            icon="file-text"
            maxWidth="max-w-4xl"
            closeAction="closePreview"
            zIndex="z-[99999]"
        >
            <div class="space-y-4 font-sans">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <div class="text-xs font-bold text-stone-700">
                        Total THP: <span class="text-emerald-800 font-black">Rp {{ number_format($previewSalary->total_diterima, 0, ',', '.') }}</span>
                    </div>
                    
                    @if ($previewSalaryId)
                        <x-button variant="primary" size="sm" icon="download" href="{{ route('finance.gaji-guru.slip', ['id' => $previewSalaryId, 'download' => 1]) }}" :wireNavigate="false" target="_blank">
                            Unduh PDF Resmi
                        </x-button>
                    @endif
                </div>

                <div class="w-full bg-stone-100 rounded-2xl overflow-hidden border border-stone-300 shadow-inner h-[540px]">
                    <iframe src="{{ route('finance.gaji-guru.slip', ['id' => $previewSalaryId]) }}" class="w-full h-full border-none"></iframe>
                </div>

                <div class="flex items-center justify-end pt-2">
                    <x-button variant="secondary" size="md" wire:click="closePreview">Tutup</x-button>
                </div>
            </div>
        </x-floating-card>
    @endif

    <script>
        window.formatRupiahInput = window.formatRupiahInput || function(v) {
            if (v === null || v === undefined || v === '') return '0';
            if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
            let s = v.toString().trim();
            if (/^-?\d+\.\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
            let clean = s.replace(/[^0-9]/g, '');
            return clean ? Number(clean).toLocaleString('id-ID') : '0';
        };
    </script>
</div>
