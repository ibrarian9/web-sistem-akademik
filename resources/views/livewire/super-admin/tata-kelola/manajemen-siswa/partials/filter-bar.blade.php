<!-- Toolbar & Filter Controls -->
<div class="space-y-3">
    <!-- Row 1: Search & PerPage -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div class="max-w-md w-full">
            <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari NIS, NISN, nama, username, wali murid..." />
        </div>
        
        <div class="flex items-center gap-2 self-end sm:self-auto">
            <span class="text-xs font-bold text-stone-600">Tampilkan:</span>
            <select wire:model.live="perPage" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="10">10 Baris</option>
                <option value="25">25 Baris</option>
                <option value="50">50 Baris</option>
                <option value="100">100 Baris</option>
            </select>
        </div>
    </div>

    <!-- Row 2: Filter Controls Bar -->
    <div class="pt-2 border-t border-stone-100 flex flex-wrap items-center gap-2.5">
        <!-- Filter Tingkat -->
        <div class="flex items-center gap-1.5">
            <span class="text-[11px] font-extrabold text-stone-500 uppercase tracking-wider">Tingkat:</span>
            <select wire:model.live="filterTingkat" class="bg-stone-50 border border-stone-300 rounded-xl px-2.5 py-1.5 text-stone-700 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                <option value="">Semua Tingkat</option>
                @foreach ($tingkatOptions as $tOpt)
                    <option value="{{ $tOpt }}">Kelas {{ $tOpt }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Kelas Umum -->
        <div class="flex items-center gap-1.5">
            <span class="text-[11px] font-extrabold text-stone-500 uppercase tracking-wider">Kelas Umum:</span>
            <select wire:model.live="filterKelas" class="bg-stone-50 border border-stone-300 rounded-xl px-2.5 py-1.5 text-stone-700 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                <option value="">Semua Kelas</option>
                <option value="belum_set">Belum Masuk Kelas</option>
                @foreach ($kelasesUmum as $ku)
                    @if(empty($filterTingkat) || $ku->tingkat == $filterTingkat)
                        <option value="{{ $ku->id }}">{{ $ku->nama_kelas }} (Tingkat {{ $ku->tingkat }})</option>
                    @endif
                @endforeach
            </select>
        </div>

        <!-- Filter Kelas Tahfizh -->
        <div class="flex items-center gap-1.5">
            <span class="text-[11px] font-extrabold text-stone-500 uppercase tracking-wider">Tahfizh:</span>
            <select wire:model.live="filterKelasTahfidz" class="bg-stone-50 border border-stone-300 rounded-xl px-2.5 py-1.5 text-stone-700 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                <option value="">Semua Halaqah</option>
                <option value="belum_set">Belum Masuk Halaqah</option>
                @foreach ($kelasesTahfidz as $kt)
                    <option value="{{ $kt->id }}">{{ $kt->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Status Siswa -->
        <div class="flex items-center gap-1.5">
            <span class="text-[11px] font-extrabold text-stone-500 uppercase tracking-wider">Status:</span>
            <select wire:model.live="filterStatus" class="bg-stone-50 border border-stone-300 rounded-xl px-2.5 py-1.5 text-stone-700 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="lulus">Lulus</option>
                <option value="pindah">Pindah</option>
                <option value="keluar">Keluar</option>
            </select>
        </div>

        <!-- Filter Jenis Kelamin -->
        <div class="flex items-center gap-1.5">
            <span class="text-[11px] font-extrabold text-stone-500 uppercase tracking-wider">Gender:</span>
            <select wire:model.live="filterJenisKelamin" class="bg-stone-50 border border-stone-300 rounded-xl px-2.5 py-1.5 text-stone-700 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                <option value="">Semua Gender</option>
                <option value="L">Laki-laki (L)</option>
                <option value="P">Perempuan (P)</option>
            </select>
        </div>

        <!-- Filter Shadow Teacher / Inklusi -->
        <div class="flex items-center gap-1.5">
            <span class="text-[11px] font-extrabold text-indigo-700 uppercase tracking-wider">Inklusi:</span>
            <select wire:model.live="filterShadowTeacher" class="bg-indigo-50/70 border border-indigo-200 rounded-xl px-2.5 py-1.5 text-indigo-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600 focus:bg-white transition shadow-2xs">
                <option value="">Semua Siswa</option>
                <option value="inklusi">Siswa Inklusi (Ada GPK)</option>
                <option value="reguler">Siswa Reguler (Tanpa GPK)</option>
                @foreach ($allShadowTeachers as $st)
                    <option value="{{ $st->id }}">GPK: {{ $st->user->nama ?? '-' }}</option>
                @endforeach
            </select>
        </div>

        <!-- Reset Filter Button -->
        @if ($this->activeFilterCount > 0)
            <button type="button" wire:click="resetFilters" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 active:bg-rose-200 transition shadow-2xs cursor-pointer ml-auto">
                <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                <span>Reset Filter</span>
                <span class="px-1.5 py-0.2 rounded-full bg-rose-600 text-white text-[10px] font-black">{{ $this->activeFilterCount }}</span>
            </button>
        @endif
    </div>

    <!-- Row 3: Active Filter Chips (if any) -->
    @if ($this->activeFilterCount > 0)
        <div class="pt-2 flex items-center gap-2 flex-wrap text-xs">
            <span class="text-[11px] font-bold text-stone-500">Filter Aktif:</span>

            @if(!empty($search))
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
                    <span>Kata Kunci: "<strong>{{ $search }}</strong>"</span>
                    <button type="button" wire:click="resetFilter('search')" class="hover:text-emerald-950">&times;</button>
                </span>
            @endif

            @if(!empty($filterTingkat))
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-stone-100 text-stone-800 border border-stone-200 text-xs font-semibold">
                    <span>Tingkat: <strong>Kelas {{ $filterTingkat }}</strong></span>
                    <button type="button" wire:click="resetFilter('filterTingkat')" class="hover:text-stone-950">&times;</button>
                </span>
            @endif

            @if(!empty($filterKelas))
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
                    <span>Kelas: <strong>{{ $filterKelas === 'belum_set' ? 'Belum Masuk Kelas' : ($kelasesUmum->firstWhere('id', $filterKelas)?->nama_kelas ?? $filterKelas) }}</strong></span>
                    <button type="button" wire:click="resetFilter('filterKelas')" class="hover:text-emerald-950">&times;</button>
                </span>
            @endif

            @if(!empty($filterKelasTahfidz))
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold">
                    <span>Tahfizh: <strong>{{ $filterKelasTahfidz === 'belum_set' ? 'Belum Masuk Halaqah' : ($kelasesTahfidz->firstWhere('id', $filterKelasTahfidz)?->nama_kelas ?? $filterKelasTahfidz) }}</strong></span>
                    <button type="button" wire:click="resetFilter('filterKelasTahfidz')" class="hover:text-amber-950">&times;</button>
                </span>
            @endif

            @if(!empty($filterStatus))
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-stone-100 text-stone-800 border border-stone-200 text-xs font-semibold">
                    <span>Status: <strong>{{ ucfirst($filterStatus) }}</strong></span>
                    <button type="button" wire:click="resetFilter('filterStatus')" class="hover:text-stone-950">&times;</button>
                </span>
            @endif

            @if(!empty($filterJenisKelamin))
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-stone-100 text-stone-800 border border-stone-200 text-xs font-semibold">
                    <span>Gender: <strong>{{ $filterJenisKelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</strong></span>
                    <button type="button" wire:click="resetFilter('filterJenisKelamin')" class="hover:text-stone-950">&times;</button>
                </span>
            @endif

            @if(!empty($filterShadowTeacher))
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-800 border border-indigo-200 text-xs font-semibold">
                    <span>Inklusi: <strong>{{ $filterShadowTeacher === 'inklusi' ? 'Ada GPK' : ($filterShadowTeacher === 'reguler' ? 'Tanpa GPK' : ($allShadowTeachers->firstWhere('id', $filterShadowTeacher)?->user?->nama ?? 'Khusus')) }}</strong></span>
                    <button type="button" wire:click="resetFilter('filterShadowTeacher')" class="hover:text-indigo-950">&times;</button>
                </span>
            @endif

            <button type="button" wire:click="resetFilters" class="text-[11px] font-bold text-rose-600 hover:text-rose-800 hover:underline">
                Hapus Semua
            </button>
        </div>
    @endif
</div>
