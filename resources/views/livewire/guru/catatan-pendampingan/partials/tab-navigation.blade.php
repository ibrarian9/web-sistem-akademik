<!-- TAB SELECTOR NAVIGATION -->
<div class="flex items-center justify-between gap-4 border-b border-stone-200 pb-3 flex-wrap">
    <div class="grid grid-cols-3 sm:inline-flex items-center gap-1.5 w-full sm:w-auto">
        <button 
            type="button" 
            wire:click="selectTab('daftar')" 
            class="px-2.5 sm:px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center sm:justify-start gap-1 sm:gap-2 cursor-pointer text-center sm:text-left {{ $tab === 'daftar' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}"
        >
            <x-lucide-clipboard-list class="w-4 h-4 shrink-0 {{ $tab === 'daftar' ? 'text-emerald-700' : 'text-stone-400' }}" />
            <span class="hidden sm:inline">Jurnal Observasi Berkala</span>
            <span class="sm:hidden text-[11px] truncate">Observasi</span>
            <span class="hidden md:inline-block px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $tab === 'daftar' ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-200 text-stone-600' }}">
                {{ $catatans->total() }}
            </span>
        </button>

        <button 
            type="button" 
            wire:click="selectTab('rekap')" 
            class="px-2.5 sm:px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center sm:justify-start gap-1 sm:gap-2 cursor-pointer text-center sm:text-left {{ $tab === 'rekap' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}"
        >
            <x-lucide-bar-chart-2 class="w-4 h-4 shrink-0 {{ $tab === 'rekap' ? 'text-emerald-700' : 'text-stone-400' }}" />
            <span class="hidden sm:inline">Rekapitulasi Perkembangan</span>
            <span class="sm:hidden text-[11px] truncate">Rekap</span>
        </button>

        <button 
            type="button" 
            wire:click="selectTab('pratinjau')" 
            class="px-2.5 sm:px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center sm:justify-start gap-1 sm:gap-2 cursor-pointer text-center sm:text-left {{ $tab === 'pratinjau' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}"
        >
            <x-lucide-printer class="w-4 h-4 shrink-0 {{ $tab === 'pratinjau' ? 'text-emerald-700' : 'text-stone-400' }}" />
            <span class="hidden sm:inline">Pratinjau & Cetak Laporan</span>
            <span class="sm:hidden text-[11px] truncate">Laporan</span>
        </button>
    </div>

    @if ($tab === 'rekap' || $tab === 'pratinjau')
        <!-- Student & Period Selector for Rekap / Pratinjau -->
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-1.5">
                <label class="text-xs font-bold text-stone-500 uppercase tracking-wider">Siswa:</label>
                <select wire:model.live="selectedSiswaId" class="px-3 py-1.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    @foreach ($allStudents as $s)
                        <option value="{{ $s->id }}">
                            {{ $s->user->nama ?? 'Siswa' }} ({{ $s->kelas->nama_kelas ?? 'Tanpa Kelas' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-1.5">
                <label class="text-xs font-bold text-stone-500 uppercase tracking-wider">Periode:</label>
                <select wire:model.live="rekapPeriode" class="px-3 py-1.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="tengah_semester">Tengah Semester</option>
                    <option value="akhir_semester">Akhir Semester</option>
                    <option value="semua">Semua Periode</option>
                </select>
            </div>
        </div>
    @endif
</div>
