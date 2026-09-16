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
                            @if (!auth()->user()->isSuperAdmin2())
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
                            @endif

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
