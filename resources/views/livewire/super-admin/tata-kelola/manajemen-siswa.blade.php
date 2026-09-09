<div class="space-y-6 font-sans">
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

    <!-- Header Title Bar -->
    <x-page-header 
        title="Kelola Data Siswa & Penempatan 2 Kelas" 
        subtitle="Pencatatan biodata siswa, penempatan Kelas Umum (1-6) & Kelas Tahfizh, dan akses portal."
        badge="MANAJEMEN DATA SISWA"
        badgeVariant="emerald"
        icon="users"
    >
        <x-slot:actions>
            @if(!auth()->user()->isSuperAdmin2())
            <x-button type="button" variant="primary" size="md" icon="plus" wire:click.prevent="openCreate">
                Tambah Siswa Baru
            </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Data Siswa Aktif (Dua Kelas Per Siswa)"
        :steps="[
            ['title' => 'Tambah Siswa Baru', 'desc' => 'Klik Tambah Siswa Baru untuk mendaftarkan NIS, NISN, biodata, serta wali murid.'],
            ['title' => 'Penetapan 2 Kelas Wajib', 'desc' => 'Setiap siswa wajib memilih 1 Kelas Umum (1-6 A/B/C) dan 1 Kelas Tahfizh (Halaqah Ustadz/ah).'],
            ['title' => 'Perubahan Status', 'desc' => 'Ubah status keaktifan menjadi Lulus, Pindah, atau Keluar saat terjadi pembaruan status pendidikan.']
        ]"
        notes="Username & password otomatis dibuatkan untuk akses portal siswa dan wali murid."
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Toolbar & Filter -->
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

        <!-- Data Table -->
        <x-table loadingTarget="search, filterKelas, filterTingkat, filterKelasTahfidz, filterStatus, filterJenisKelamin, filterShadowTeacher, perPage, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-32">NIS / NISN</x-table.th>
                    <x-table.th class="min-w-[180px]">Nama Siswa</x-table.th>
                    <x-table.th class="min-w-[140px]">Kelas Umum</x-table.th>
                    <x-table.th class="min-w-[160px]">Kelas Tahfizh</x-table.th>
                    <x-table.th class="min-w-[150px]">Wali Murid</x-table.th>
                    <x-table.th align="center" class="w-28">Status</x-table.th>
                    <x-table.th align="center" class="min-w-[180px]">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($siswas as $siswa)
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3.5 font-semibold text-stone-600 border-r border-stone-200">
                            <div class="font-bold text-stone-900">{{ $siswa->nis }}</div>
                            <div class="text-[10px] text-stone-500">NISN: {{ $siswa->nisn ?: '-' }}</div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ strtoupper($siswa->user->nama ?? '-') }}</div>
                            <div class="text-[10px] text-stone-500 font-medium">User: {{ $siswa->user->username ?? '-' }}</div>
                            @if($siswa->shadowTeacher)
                                <div class="mt-1 flex items-center gap-1">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200" title="Guru Pendamping Khusus: {{ $siswa->shadowTeacher->user->nama ?? '-' }} ({{ ucfirst($siswa->shadowTeacher->jenis_guru ?? 'Pendamping') }})">
                                        GPK: {{ $siswa->shadowTeacher->user->nama ?? '-' }} ({{ $siswa->shadowTeacher->jenis_guru === 'pendamping' ? 'Pendamping' : ($siswa->shadowTeacher->jenis_guru === 'tahfidz' ? 'Tahfizh' : 'Umum') }})
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            @if($siswa->kelas)
                                <x-badge variant="emerald" size="xs">
                                    {{ $siswa->kelas->nama_kelas }}
                                </x-badge>
                            @else
                                <span class="text-stone-400 italic text-[11px]">- Belum Set -</span>
                            @endif
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            @if($siswa->kelasTahfidz)
                                <x-badge variant="amber" size="xs">
                                    {{ $siswa->kelasTahfidz->nama_kelas }}
                                </x-badge>
                            @else
                                <span class="text-stone-400 italic text-[11px]">- Belum Set -</span>
                            @endif
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="text-stone-900 font-bold">{{ $siswa->nama_wali ?: '-' }}</div>
                            <div class="text-[10px] text-stone-500 font-medium">{{ $siswa->no_hp_wali ?: '-' }}</div>
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            <x-status-badge :status="$siswa->status" />
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <x-button type="button" variant="outline" size="xs" icon="eye" wire:click.prevent="openDetail({{ $siswa->id }})">
                                    Detail
                                </x-button>
                                @if(!auth()->user()->isSuperAdmin2())
                                <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click.prevent="openEdit({{ $siswa->id }})">
                                    Edit
                                </x-button>
                                <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click.prevent="delete({{ $siswa->id }})" data-confirm="Apakah Anda yakin ingin menghapus data siswa ini?">
                                    Hapus
                                </x-button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-stone-400">
                            @if ($this->activeFilterCount > 0)
                                <div class="space-y-3 flex flex-col items-center justify-center">
                                    <x-table.empty 
                                        title="Tidak ada data siswa yang cocok dengan filter" 
                                        subtitle="Kombinasi pencarian dan filter yang Anda gunakan tidak menemukan data siswa." 
                                    />
                                    <button type="button" wire:click="resetFilters" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-stone-100 hover:bg-stone-200 text-stone-700 transition shadow-2xs cursor-pointer">
                                        <x-lucide-rotate-ccw class="w-3.5 h-3.5 text-stone-500" />
                                        <span>Reset Semua Filter ({{ $this->activeFilterCount }})</span>
                                    </button>
                                </div>
                            @else
                                <x-table.empty title="Tidak ada data siswa ditemukan" subtitle="Pastikan kata kunci pencarian benar atau tambahkan data siswa baru." />
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $siswas->links() }}
        </div>
    </div>

    <!-- Form Floating Modal -->
    <x-floating-card 
        :show="$isFormOpen ? true : false"
        :title="$siswaId ? 'Edit Data Siswa & Penempatan Rombel' : 'Tambah Santri Baru'"
        subtitle="Kelola biodata lengkap santri, penempatan rombel kelas umum & tahfizh, serta penugasan guru pendamping khusus (ABK)."
        badge="DATA SISWA"
        badgeVariant="emerald"
        icon="user-check"
        maxWidth="max-w-4xl"
        closeAction="$set('isFormOpen', false)"
    >
        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl space-y-2 text-xs shadow-2xs mb-4">
                <div class="flex items-center gap-2 font-extrabold text-rose-900">
                    <x-lucide-alert-triangle class="w-4 h-4 text-rose-600 shrink-0" />
                    <span>Mohon Perbaiki Isian Formulir Berikut:</span>
                </div>
                <ul class="list-disc list-inside text-[11px] font-bold text-rose-700 space-y-0.5 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit.prevent="save" action="javascript:void(0);" class="space-y-6 text-xs">
            
            <!-- SECTION 1: PENEMPATAN KELAS & GURU PENDAMPING -->
            <div class="bg-gradient-to-br from-stone-50 via-white to-stone-50 border border-stone-200 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3.5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-stone-200/80 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-1.5 bg-emerald-100 text-emerald-800 rounded-lg">
                            <x-lucide-layers class="w-4 h-4" />
                        </div>
                        <div>
                            <h4 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">1. Penempatan Rombel & Pembimbing</h4>
                            <p class="text-[11px] text-stone-500 font-medium">Tentukan rombel kelas reguler, kelompok halaqah tahfizh, dan guru pendamping.</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-stone-600 bg-stone-100 px-2.5 py-1 rounded-full border border-stone-200 w-fit">
                        Fleksibel / Opsional
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                    <!-- 1. Kelas Umum -->
                    <div class="p-3 bg-emerald-50/60 border border-emerald-200/80 rounded-xl space-y-2 flex flex-col justify-between">
                        <div class="space-y-0.5">
                            <label class="text-[11px] font-bold text-emerald-950 uppercase flex items-center gap-1.5">
                                <x-lucide-book-open class="w-3.5 h-3.5 text-emerald-700 shrink-0" />
                                <span>Kelas Umum (Reguler)</span>
                            </label>
                            <p class="text-[10px] text-emerald-800/80">Wali kelas & kurikulum umum</p>
                        </div>
                        <div>
                            <select wire:model="kelas_id" class="w-full px-3 py-2 bg-white border border-emerald-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                                <option value="">-- Belum Ditempatkan --</option>
                                @foreach ($kelasesUmum as $kls)
                                    <option value="{{ $kls->id }}">Kelas {{ $kls->nama_kelas }} (Wali: {{ $kls->guruUmum->user->nama ?? 'Admin' }})</option>
                                @endforeach
                            </select>
                            @error('kelas_id') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- 2. Kelas Tahfizh -->
                    <div class="p-3 bg-amber-50/60 border border-amber-200/80 rounded-xl space-y-2 flex flex-col justify-between">
                        <div class="space-y-0.5">
                            <label class="text-[11px] font-bold text-amber-950 uppercase flex items-center gap-1.5">
                                <x-lucide-bookmark class="w-3.5 h-3.5 text-amber-700 shrink-0" />
                                <span>Kelas Tahfizh (Halaqah)</span>
                            </label>
                            <p class="text-[10px] text-amber-800/80">Kelompok setoran Al-Qur'an</p>
                        </div>
                        <div>
                            <select wire:model="kelas_tahfidz_id" class="w-full px-3 py-2 bg-white border border-amber-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-amber-600 shadow-2xs">
                                <option value="">-- Belum Ditempatkan --</option>
                                @foreach ($kelasesTahfidz as $kls)
                                    <option value="{{ $kls->id }}">{{ $kls->nama_kelas }} (Pengampu: {{ $kls->guruTahfidz->user->nama ?? 'Admin' }})</option>
                                @endforeach
                            </select>
                            @error('kelas_tahfidz_id') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- 3. Guru Pendamping Khusus (Shadow Teacher) -->
                    <div class="p-3 bg-indigo-50/60 border border-indigo-200/80 rounded-xl space-y-2 flex flex-col justify-between">
                        <div class="space-y-0.5">
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-bold text-indigo-950 uppercase flex items-center gap-1.5">
                                    <x-lucide-user-check class="w-3.5 h-3.5 text-indigo-700 shrink-0" />
                                    <span>Guru Pendamping (Shadow)</span>
                                </label>
                                <span class="text-[9px] font-black uppercase tracking-wider bg-indigo-100 text-indigo-800 px-1.5 py-0.5 rounded border border-indigo-200">ABK</span>
                            </div>
                            <p class="text-[10px] text-indigo-800/80">Khusus murid berkebutuhan khusus</p>
                        </div>
                        <div>
                            <select wire:model="shadow_teacher_id" class="w-full px-3 py-2 bg-white border border-indigo-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600 shadow-2xs">
                                <option value="">-- Tanpa Guru Pendamping --</option>
                                @forelse ($shadowTeachers as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->user->nama ?? 'Guru' }} — Guru Pendamping ({{ $guru->nip ?: 'NIP -' }})</option>
                                @empty
                                    <option value="" disabled>Belum ada guru berkategori Pendamping (Shadow Teacher)</option>
                                @endforelse
                            </select>
                            @error('shadow_teacher_id') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: IDENTITAS POKOK SISWA -->
            <div class="bg-white border border-stone-200 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3.5">
                <div class="flex items-center gap-2.5 border-b border-stone-200/80 pb-3">
                    <div class="p-1.5 bg-stone-100 text-stone-700 rounded-lg">
                        <x-lucide-user class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">2. Data Pokok & Identitas Siswa</h4>
                        <p class="text-[11px] text-stone-500 font-medium">Informasi nama resmi, nomor induk, jenis kelamin, serta status keaktifan sekolah.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                    <!-- Nama Lengkap (col-span-2) -->
                    <div class="space-y-1 sm:col-span-2">
                        <label class="text-xs font-bold text-stone-700 uppercase">Nama Lengkap Siswa <span class="text-rose-600">*</span></label>
                        <input wire:model="nama" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Contoh: Muhammad Al-Fatih" required />
                        @error('nama') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Jenis Kelamin <span class="text-rose-600">*</span></label>
                        <select wire:model="jenis_kelamin" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                            <option value="L">Laki-laki (Ikhwan)</option>
                            <option value="P">Perempuan (Akhwat)</option>
                        </select>
                        @error('jenis_kelamin') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- NIS -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">NIS (Nomor Induk Siswa) <span class="text-rose-600">*</span></label>
                        <input wire:model="nis" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Contoh: 1001" required />
                        @error('nis') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- NISN -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">NISN <span class="text-stone-400 text-[10px] font-normal lowercase">(opsional)</span></label>
                        <input wire:model="nisn" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Contoh: 009812345" />
                        @error('nisn') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Masuk -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Masuk Sekolah <span class="text-rose-600">*</span></label>
                        <input wire:model="tanggal_masuk" type="date" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required />
                        @error('tanggal_masuk') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tempat Lahir -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Tempat Lahir</label>
                        <input wire:model="tempat_lahir" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Yogyakarta" />
                        @error('tempat_lahir') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Lahir</label>
                        <input wire:model="tanggal_lahir" type="date" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                        @error('tanggal_lahir') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status Keaktifan (hanya saat edit) -->
                    @if ($siswaId)
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Status Keaktifan</label>
                            <select wire:model="status" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                                <option value="aktif">Aktif Belajar</option>
                                <option value="lulus">Lulus (Alumni)</option>
                                <option value="pindah">Pindah Sekolah</option>
                                <option value="keluar">Keluar / DO</option>
                            </select>
                            @error('status') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            </div>

            <!-- SECTION 3: DATA WALI MURID & ALAMAT -->
            <div class="bg-white border border-stone-200 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3.5">
                <div class="flex items-center gap-2.5 border-b border-stone-200/80 pb-3">
                    <div class="p-1.5 bg-stone-100 text-stone-700 rounded-lg">
                        <x-lucide-home class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">3. Data Orang Tua / Wali & Alamat Tinggal</h4>
                        <p class="text-[11px] text-stone-500 font-medium">Informasi wali murid untuk notifikasi kehadiran, tagihan SPP, dan laporan akademik.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Nama Wali -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Nama Lengkap Orang Tua / Wali</label>
                        <input wire:model="nama_wali" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Contoh: Hendra Gunawan" />
                        @error('nama_wali') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- No HP Wali -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">No. WhatsApp / HP Wali Murid</label>
                        <input wire:model="no_hp_wali" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="081234567890" />
                        @error('no_hp_wali') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Alamat Rumah (col-span-2) -->
                    <div class="space-y-1 sm:col-span-2">
                        <label class="text-xs font-bold text-stone-700 uppercase">Alamat Lengkap Rumah</label>
                        <textarea wire:model="alamat" rows="2" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none" placeholder="Alamat rumah lengkap santri beserta kota/kabupaten..."></textarea>
                        @error('alamat') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 4: AKUN LOGIN PORTAL SANTRI/WALI -->
            <div class="bg-stone-50 border border-stone-200 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3.5">
                <div class="flex items-center gap-2.5 border-b border-stone-200/80 pb-3">
                    <div class="p-1.5 bg-stone-200 text-stone-800 rounded-lg">
                        <x-lucide-key-round class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">4. Akun Login Portal Siswa & Wali</h4>
                        <p class="text-[11px] text-stone-500 font-medium">Akun kredensial untuk siswa dan orang tua melihat buku rapor, absensi, dan tagihan keuangan.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <!-- Username -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Username Login <span class="text-rose-600">*</span></label>
                        <input wire:model="username" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="fauzi1001" required />
                        @error('username') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Email <span class="text-stone-400 text-[10px] font-normal lowercase">(opsional)</span></label>
                        <input wire:model="email" type="email" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="siswa@mail.com" />
                        @error('email') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">
                            {{ $siswaId ? 'Ganti Password' : 'Password Login' }}
                            @if(!$siswaId)<span class="text-rose-600">*</span>@endif
                        </label>
                        <input wire:model="password" type="password" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="{{ $siswaId ? '•••••• (Kosongkan bila tetap)' : 'Min. 6 karakter' }}" {{ !$siswaId ? 'required' : '' }} />
                        <p class="text-[10px] text-stone-500 italic">
                            {{ $siswaId ? 'Kosongkan jika tidak ingin mengubah password.' : 'Minimal 6 karakter.' }}
                        </p>
                        @error('password') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Modal Action Buttons Footer -->
            <div class="flex items-center justify-between gap-3 border-t border-stone-200 pt-4">
                <span class="text-[11px] text-stone-500 font-medium">
                    Tanda <span class="text-rose-600 font-black">*</span> wajib diisi
                </span>
                <div class="flex items-center gap-2.5">
                    <x-button type="button" variant="secondary" size="md" wire:click="$set('isFormOpen', false)">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="save">
                        {{ $siswaId ? 'Simpan Perubahan' : 'Daftarkan Siswa' }}
                    </x-button>
                </div>
            </div>
        </form>
    </x-floating-card>

    <!-- Student Detail Floating Modal -->
    <x-floating-card 
        :show="($showDetailModal && $selectedSiswaDetail) ? true : false"
        :title="$selectedSiswaDetail ? ('Biodata: ' . strtoupper($selectedSiswaDetail->user?->nama ?? '-')) : 'Detail Siswa'"
        :subtitle="$selectedSiswaDetail ? ('NIS: ' . ($selectedSiswaDetail->nis ?? '-') . ' | NISN: ' . ($selectedSiswaDetail->nisn ?: '-')) : ''"
        badge="PROFIL SISWA"
        badgeVariant="emerald"
        icon="user-check"
        maxWidth="max-w-3xl"
        closeAction="closeDetail"
    >
        @if ($selectedSiswaDetail)
            <div class="space-y-4 text-xs">
                <!-- Tri-Card: Wali Kelas, Wali Tahfizh, Shadow Teacher -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Kelas Umum -->
                    <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl space-y-1">
                        <div class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-950">
                            <x-lucide-book-open class="w-4 h-4 text-emerald-700 shrink-0" />
                            <span>1. Kelas Umum</span>
                        </div>
                        @if($selectedSiswaDetail->kelas)
                            <div class="text-sm font-black text-emerald-900 pt-0.5">
                                {{ $selectedSiswaDetail->kelas->nama_kelas }}
                            </div>
                            <div class="text-[11px] text-emerald-700 font-medium">
                                Wali Kelas: <strong>{{ $selectedSiswaDetail->kelas->guruUmum->user->nama ?? 'Belum Ditentukan' }}</strong>
                            </div>
                        @else
                            <div class="text-xs text-stone-400 italic pt-1">- Belum Ditempatkan -</div>
                        @endif
                    </div>

                    <!-- Kelas Tahfizh -->
                    <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl space-y-1">
                        <div class="flex items-center gap-1.5 text-xs font-extrabold text-amber-950">
                            <x-lucide-bookmark class="w-4 h-4 text-amber-700 shrink-0" />
                            <span>2. Kelas Tahfizh</span>
                        </div>
                        @if($selectedSiswaDetail->kelasTahfidz)
                            <div class="text-sm font-black text-amber-900 pt-0.5">
                                {{ $selectedSiswaDetail->kelasTahfidz->nama_kelas }}
                            </div>
                            <div class="text-[11px] text-amber-800 font-medium">
                                Pengampu: <strong>{{ $selectedSiswaDetail->kelasTahfidz->guruTahfidz->user->nama ?? 'Belum Ditentukan' }}</strong>
                            </div>
                        @else
                            <div class="text-xs text-stone-400 italic pt-1">- Belum Ditempatkan -</div>
                        @endif
                    </div>

                    <!-- Guru Pendamping Khusus (Shadow Teacher) -->
                    <div class="p-3.5 bg-indigo-50 border border-indigo-200 rounded-xl space-y-1">
                        <div class="flex items-center gap-1.5 text-xs font-extrabold text-indigo-950">
                            <x-lucide-user-check class="w-4 h-4 text-indigo-700 shrink-0" />
                            <span>3. Guru Pendamping</span>
                        </div>
                        @if($selectedSiswaDetail->shadowTeacher)
                            <div class="text-sm font-black text-indigo-900 pt-0.5">
                                {{ $selectedSiswaDetail->shadowTeacher->user->nama ?? '-' }}
                            </div>
                            <div class="text-[11px] text-indigo-700 font-medium">
                                Status: <strong>Shadow Teacher / GPK</strong> ({{ $selectedSiswaDetail->shadowTeacher->jenis_guru === 'pendamping' ? 'Guru Pendamping' : ($selectedSiswaDetail->shadowTeacher->jenis_guru === 'tahfidz' ? 'Guru Tahfizh' : ($selectedSiswaDetail->shadowTeacher->jenis_guru === 'keduanya' ? 'Guru Umum & Tahfizh' : 'Guru Umum')) }})
                            </div>
                        @else
                            <div class="text-xs text-stone-400 italic pt-1">- Tidak Ada Guru Pendamping -</div>
                        @endif
                    </div>
                </div>

                <!-- Detail Information Grid -->
                <div class="space-y-2">
                    <div class="text-xs font-extrabold text-stone-800 uppercase tracking-wider">Informasi Identitas & Akun Login</div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                            <div class="text-[10px] uppercase font-bold text-stone-500">NIS (Nomor Induk Siswa)</div>
                            <div class="font-mono font-bold text-stone-900 text-sm">{{ $selectedSiswaDetail->nis }}</div>
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                            <div class="text-[10px] uppercase font-bold text-stone-500">NISN (Nomor Induk Siswa Nasional)</div>
                            <div class="font-mono font-bold text-stone-900 text-sm">{{ $selectedSiswaDetail->nisn ?: '-' }}</div>
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                            <div class="text-[10px] uppercase font-bold text-stone-500">Username Portal</div>
                            <div class="font-mono font-bold text-stone-900">{{ $selectedSiswaDetail->user->username ?? '-' }}</div>
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                            <div class="text-[10px] uppercase font-bold text-stone-500">Alamat Email</div>
                            <div class="font-medium text-stone-900">{{ $selectedSiswaDetail->user->email ?: '-' }}</div>
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                            <div class="text-[10px] uppercase font-bold text-stone-500">Jenis Kelamin</div>
                            <div class="font-bold text-stone-900">
                                {{ $selectedSiswaDetail->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </div>
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                            <div class="text-[10px] uppercase font-bold text-stone-500">Tanggal Masuk Sekolah</div>
                            <div class="font-mono font-bold text-stone-900">
                                {{ $selectedSiswaDetail->tanggal_masuk ? $selectedSiswaDetail->tanggal_masuk->format('d F Y') : '-' }}
                            </div>
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5 sm:col-span-2">
                            <div class="text-[10px] uppercase font-bold text-stone-500">Tempat & Tanggal Lahir</div>
                            <div class="font-bold text-stone-900">
                                {{ $selectedSiswaDetail->tempat_lahir ?: '-' }}, {{ $selectedSiswaDetail->tanggal_lahir ? $selectedSiswaDetail->tanggal_lahir->format('d F Y') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Wali Murid & Alamat -->
                <div class="space-y-2">
                    <div class="text-xs font-extrabold text-stone-800 uppercase tracking-wider">Data Orang Tua / Wali & Alamat</div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                            <div class="text-[10px] uppercase font-bold text-stone-500">Nama Wali Murid</div>
                            <div class="font-bold text-stone-900">{{ $selectedSiswaDetail->nama_wali ?: '-' }}</div>
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                            <div class="text-[10px] uppercase font-bold text-stone-500">No HP / WhatsApp Wali</div>
                            <div class="font-mono font-bold text-emerald-800">{{ $selectedSiswaDetail->no_hp_wali ?: '-' }}</div>
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5 sm:col-span-2">
                            <div class="text-[10px] uppercase font-bold text-stone-500">Alamat Tempat Tinggal</div>
                            <div class="font-medium text-stone-800 leading-relaxed">{{ $selectedSiswaDetail->alamat ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-stone-200">
                    <x-button type="button" variant="secondary" size="md" wire:click="closeDetail">
                        Tutup Detail
                    </x-button>
                </div>
            </div>
        @endif
    </x-floating-card>
</div>
