<!-- Quick Switcher Bar -->
<div class="flex items-center gap-2 bg-white border border-stone-200 p-2 rounded-2xl overflow-x-auto shadow-2xs">
    <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.kelas') : route('super-admin.kelas') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2 whitespace-nowrap">
        <x-lucide-layers class="w-4 h-4 text-emerald-600" />
        <span>1. Buat & Kelola Kelas (Umum & Tahfizh)</span>
    </a>
    <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.siswa') : route('super-admin.siswa') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-700 text-white shadow-2xs flex items-center gap-2 whitespace-nowrap">
        <x-lucide-users class="w-4 h-4 text-emerald-100" />
        <span>2. Data Siswa</span>
    </a>
    <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.plotting-kelas') : route('super-admin.plotting-kelas') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2 whitespace-nowrap">
        <x-lucide-user-plus class="w-4 h-4 text-emerald-600" />
        <span>3. Plotting Siswa Per-Kelas</span>
    </a>
</div>
