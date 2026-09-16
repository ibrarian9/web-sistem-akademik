<!-- TAB SELECTOR NAVIGATION -->
<div class="flex items-center justify-between gap-4 border-b border-stone-200 pb-3 flex-wrap">
    <div class="flex items-center p-1 bg-stone-100 border border-stone-200 rounded-xl overflow-x-auto shadow-2xs">
        <button 
            type="button" 
            wire:click="selectTab('daftar')" 
            class="px-4 py-2 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-2 {{ $tab === 'daftar' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}"
        >
            <x-lucide-clipboard-list class="w-4 h-4 {{ $tab === 'daftar' ? 'text-emerald-700' : 'text-stone-400' }}" />
            <span>Jurnal Observasi Berkala</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $tab === 'daftar' ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-200 text-stone-600' }}">
                {{ $catatans->total() }}
            </span>
        </button>

        <button 
            type="button" 
            wire:click="selectTab('rekap')" 
            class="px-4 py-2 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-2 {{ $tab === 'rekap' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}"
        >
            <x-lucide-bar-chart-2 class="w-4 h-4 {{ $tab === 'rekap' ? 'text-emerald-700' : 'text-stone-400' }}" />
            <span>Rekapitulasi Perkembangan</span>
        </button>

        <button 
            type="button" 
            wire:click="selectTab('pratinjau')" 
            class="px-4 py-2 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-2 {{ $tab === 'pratinjau' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}"
        >
            <x-lucide-printer class="w-4 h-4 {{ $tab === 'pratinjau' ? 'text-emerald-700' : 'text-stone-400' }}" />
            <span>Pratinjau & Cetak Laporan</span>
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
