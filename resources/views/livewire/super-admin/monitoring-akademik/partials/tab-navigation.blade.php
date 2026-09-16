<!-- Main Navigation Tabs -->
<div class="bg-white border border-stone-200 p-2 rounded-2xl shadow-xs flex flex-wrap items-center justify-between gap-3">
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:inline-flex items-center gap-1.5 w-full lg:w-auto">
        <button wire:click="setTab('progres_guru')" 
            class="px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl text-xs font-bold transition duration-200 flex items-center justify-center sm:justify-start gap-1.5 sm:gap-2 cursor-pointer text-center sm:text-left {{ $activeTab === 'progres_guru' ? 'bg-emerald-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
            <x-lucide-user-check class="w-4 h-4 shrink-0" />
            <span class="hidden sm:inline whitespace-nowrap">Monitoring Progres Guru</span>
            <span class="sm:hidden text-[11px] truncate">Progres Guru</span>
        </button>
        <button wire:click="setTab('nilai')" 
            class="px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl text-xs font-bold transition duration-200 flex items-center justify-center sm:justify-start gap-1.5 sm:gap-2 cursor-pointer text-center sm:text-left {{ $activeTab === 'nilai' ? 'bg-emerald-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
            <x-lucide-award class="w-4 h-4 shrink-0" />
            <span class="hidden sm:inline whitespace-nowrap">Rekap Nilai Siswa</span>
            <span class="sm:hidden text-[11px] truncate">Rekap Nilai</span>
        </button>
        <button wire:click="setTab('kurikulum')" 
            class="px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl text-xs font-bold transition duration-200 flex items-center justify-center sm:justify-start gap-1.5 sm:gap-2 cursor-pointer text-center sm:text-left {{ $activeTab === 'kurikulum' ? 'bg-emerald-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
            <x-lucide-layers class="w-4 h-4 shrink-0" />
            <span class="hidden sm:inline whitespace-nowrap">Monitoring Bab & TP</span>
            <span class="sm:hidden text-[11px] truncate">Bab & TP</span>
        </button>
        <button wire:click="setTab('absen')" 
            class="px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl text-xs font-bold transition duration-200 flex items-center justify-center sm:justify-start gap-1.5 sm:gap-2 cursor-pointer text-center sm:text-left {{ $activeTab === 'absen' ? 'bg-emerald-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
            <x-lucide-clipboard-check class="w-4 h-4 shrink-0" />
            <span class="hidden sm:inline whitespace-nowrap">Monitoring Absensi</span>
            <span class="sm:hidden text-[11px] truncate">Absensi</span>
        </button>
    </div>

    <div class="text-[11px] font-semibold text-stone-400 px-3 hidden md:block">
        Supervisi Akademik &bull; Akses Super Admin 1 & 2
    </div>
</div>
