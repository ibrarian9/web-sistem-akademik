<!-- Main Navigation Tabs -->
<div class="bg-white border border-stone-200 p-2 rounded-2xl shadow-xs flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-2 overflow-x-auto">
        <button wire:click="setTab('progres_guru')" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'progres_guru' ? 'bg-emerald-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
            <x-lucide-user-check class="w-4 h-4" />
            <span>Monitoring Progres Guru</span>
        </button>
        <button wire:click="setTab('nilai')" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'nilai' ? 'bg-emerald-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
            <x-lucide-award class="w-4 h-4" />
            <span>Rekap Nilai Siswa</span>
        </button>
        <button wire:click="setTab('kurikulum')" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'kurikulum' ? 'bg-emerald-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
            <x-lucide-layers class="w-4 h-4" />
            <span>Monitoring Bab & TP</span>
        </button>
        <button wire:click="setTab('absen')" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'absen' ? 'bg-emerald-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
            <x-lucide-clipboard-check class="w-4 h-4" />
            <span>Monitoring Absensi</span>
        </button>
    </div>

    <div class="text-[11px] font-semibold text-stone-400 px-3 hidden md:block">
        Supervisi Akademik &bull; Akses Super Admin 1 & 2
    </div>
</div>
