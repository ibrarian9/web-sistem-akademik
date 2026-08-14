<div class="space-y-6 font-sans">
    <!-- Quick Switcher Bar -->
    <div class="flex items-center gap-2 bg-white border border-stone-200 p-2 rounded-2xl overflow-x-auto shadow-xs">
        <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.kelas') : route('super-admin.kelas') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-700 text-white shadow-sm flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 01-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span>1. Buat &amp; Kelola Kelas (Umum &amp; Tahfizh)</span>
        </a>
        <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.siswa') : route('super-admin.siswa') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>2. Data Siswa</span>
        </a>
        <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.plotting-kelas') : route('super-admin.plotting-kelas') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>3. Plotting Siswa Per-Kelas</span>
        </a>
    </div>


    <!-- Info & Tutorial Box -->

    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Rombongan Belajar (Kelas Umum & Kelas Tahfizh)"
        :steps="[
            ['title' => 'Pilih Jenis Kelas', 'desc' => 'Tentukan jenis kelas yang dibuat: Kelas Umum (1-6 + Abjad) atau Kelas Tahfizh (Halaqah Ustadz/ah).'],
            ['title' => 'Penetapan Pengampu', 'desc' => 'Untuk Kelas Umum pilih Wali Kelas Guru Umum. Untuk Kelas Tahfizh pilih Guru Tahfizh pengampu halaqah.'],
            ['title' => 'Wajib 2 Kelas Per Siswa', 'desc' => 'Setiap siswa diwajibkan terdaftar pada 1 Kelas Umum dan 1 Kelas Tahfizh.']
        ]"
        notes="Penamaan Kelas Tahfizh otomatis menggunakan nama Guru Tahfizh pengampu halaqah (contoh: Halaqah Ustadz Abdullah)."
    />

    <!-- Hero Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                MANAJEMEN KELAS &amp; HALAQAH TAHFIZH
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Kelola Kelas Umum &amp; Kelompok Tahfizh</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Buat rombongan belajar Kelas Umum (1-6) dan kelompok Halaqah Guru Tahfizh.</p>
        </div>
        <button type="button" wire:click.prevent="openCreate" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition shadow-sm flex items-center gap-2">
            <x-lucide-plus class="w-4 h-4" />
            <span>Tambah Kelas Baru</span>
        </button>
    </div>

    <!-- Info Banner Semester Requirement -->
    <div class="p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs font-bold shadow-xs">
        <div class="flex items-center gap-2.5">
            <x-lucide-calendar class="w-5 h-5 text-amber-600 shrink-0" />
            <div>
                <p class="font-extrabold text-amber-950">Petunjuk Semester Active:</p>
                <p class="text-[11px] text-amber-800 font-medium">Setiap kelas otomatis ditautkan ke Semester & Tahun Ajaran Aktif. Anda dapat mengatur atau menambah Tahun Ajaran baru terlebih dahulu melalui menu Kalender Akademik.</p>
            </div>
        </div>
        <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.kalender-akademik') : route('super-admin.kalender-akademik') }}"
           class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-extrabold shrink-0 flex items-center gap-1.5 transition shadow-xs">
            <x-lucide-settings class="w-3.5 h-3.5" />
            <span>Kelola Tahun Ajaran</span>
        </a>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <!-- Toolbar & Filter -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="relative max-w-md w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                    <x-lucide-search class="w-4 h-4" />
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama kelas..."
                    class="w-full pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 placeholder-stone-400 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-xs" />
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-stone-600">Jenis Kelas:</span>
                    <select wire:model.live="filterJenis" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-1.5 focus:ring-2 focus:ring-emerald-600 shadow-xs">
                        <option value="semua">Semua Jenis Kelas</option>
                        <option value="umum">Kelas Umum (1-6)</option>
                        <option value="tahfidz">Kelas Tahfizh (Halaqah)</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-stone-600">Tampilkan:</span>
                    <select wire:model.live="perPage" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-1.5 focus:ring-2 focus:ring-emerald-600 shadow-xs">
                        <option value="10">10 Baris</option>
                        <option value="25">25 Baris</option>
                        <option value="50">50 Baris</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs text-stone-800">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[180px]">Nama Kelas / Halaqah</th>
                        <th class="p-3.5 border-r border-emerald-700 w-32 text-center">Jenis Kelas</th>
                        <th class="p-3.5 border-r border-emerald-700 w-24 text-center">Tingkat</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[200px]">Guru Pengampu / Wali Kelas</th>
                        <th class="p-3.5 border-r border-emerald-700 w-28 text-center">Total Siswa</th>
                        <th class="p-3.5 text-center min-w-[140px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($kelases as $kelas)
                        @php
                            $isTahfizh = $kelas->jenis_kelas === 'tahfidz';
                            $cntSiswa = $isTahfizh ? $kelas->siswasTahfidz()->count() : $kelas->siswas()->count();
                        @endphp
                        <tr class="hover:bg-emerald-50/50 transition">
                            <td class="p-3.5 border-r border-stone-200">
                                <div class="font-extrabold text-stone-900 text-xs">{{ $kelas->nama_kelas }}</div>
                                <div class="text-[10px] text-stone-500 font-medium">Semester Active: {{ $kelas->semester->semester ?? 'Ganjil' }}</div>
                            </td>
                            <td class="p-3.5 text-center border-r border-stone-200">
                                @if($isTahfizh)
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-900 border border-amber-300 rounded-full text-[10px] font-extrabold uppercase inline-block">
                                        ★ Tahfizh
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-full text-[10px] font-extrabold uppercase inline-block">
                                        📚 Umum
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center font-bold text-stone-700 border-r border-stone-200">
                                {{ $isTahfizh ? '-' : 'Kelas ' . $kelas->tingkat }}
                            </td>
                            <td class="p-3.5 border-r border-stone-200">
                                @if($isTahfizh)
                                    <div class="font-extrabold text-emerald-900">{{ $kelas->guruTahfidz->user->nama ?? 'Belum Ditentukan' }}</div>
                                    <div class="text-[10px] text-emerald-700 font-semibold">Pengampu Halaqah Tahfizh</div>
                                @else
                                    <div class="font-extrabold text-stone-900">{{ $kelas->guruUmum->user->nama ?? 'Belum Ditentukan' }}</div>
                                    <div class="text-[10px] text-stone-500 font-medium">Wali Kelas Umum</div>
                                @endif
                            </td>
                            <td class="p-3.5 text-center font-black text-stone-900 border-r border-stone-200">
                                <span class="px-2 py-0.5 bg-stone-100 border border-stone-300 rounded font-extrabold">
                                    {{ $cntSiswa }} Santri
                                </span>
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" wire:click.prevent="openEdit({{ $kelas->id }})" class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-lg font-bold text-xs border border-amber-300 transition shadow-xs flex items-center gap-1">
                                        <x-lucide-edit class="w-3.5 h-3.5 text-amber-700" />
                                        <span>Edit</span>
                                    </button>
                                    <button type="button" wire:click.prevent="delete({{ $kelas->id }})" data-confirm="Apakah Anda yakin ingin menghapus kelas {{ $kelas->nama_kelas }} ini?" class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg font-bold text-xs border border-rose-300 transition shadow-xs flex items-center gap-1">
                                        <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-600" />
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-stone-500 font-semibold italic">
                                Tidak ada data kelas ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $kelases->links() }}
        </div>
    </div>

    <!-- Form Modal -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-xs p-4">
            <div class="w-full max-w-md bg-white border border-stone-200 rounded-3xl shadow-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">★</span>
                        <span>{{ $kelasId ? 'Edit Data Kelas' : 'Tambah Kelas Baru' }}</span>
                    </h3>
                    <button type="button" wire:click="$set('isFormOpen', false)" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold">✕</button>
                </div>

                <!-- Validation & Session Error Banner Inside Modal -->
                @if (session()->has('error'))
                    <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center gap-2 text-xs font-extrabold shadow-xs">
                        <x-lucide-alert-triangle class="w-4 h-4 text-rose-600 shrink-0" />
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl space-y-1.5 text-xs shadow-xs">
                        <div class="flex items-center gap-2 font-extrabold text-rose-900">
                            <x-lucide-alert-triangle class="w-4 h-4 text-rose-600 shrink-0" />
                            <span>Mohon Perbaiki Isian Formulir:</span>
                        </div>
                        <ul class="list-disc list-inside text-[11px] font-bold text-rose-700 space-y-0.5 pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form wire:submit.prevent="save" action="javascript:void(0);" class="space-y-4">
                    <!-- Switch Jenis Kelas -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-stone-700 uppercase">Jenis Kelas <span class="text-rose-600">*</span></label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center p-2.5 rounded-xl border text-xs font-bold cursor-pointer transition {{ $jenis_kelas === 'umum' ? 'bg-emerald-100 border-emerald-500 text-emerald-950' : 'bg-stone-50 border-stone-200 text-stone-600 hover:bg-stone-100' }}">
                                <input type="radio" wire:model.live="jenis_kelas" value="umum" class="sr-only">
                                <span>📚 Kelas Umum (1-6)</span>
                            </label>
                            <label class="flex items-center justify-center p-2.5 rounded-xl border text-xs font-bold cursor-pointer transition {{ $jenis_kelas === 'tahfidz' ? 'bg-amber-100 border-amber-500 text-amber-950' : 'bg-stone-50 border-stone-200 text-stone-600 hover:bg-stone-100' }}">
                                <input type="radio" wire:model.live="jenis_kelas" value="tahfidz" class="sr-only">
                                <span>★ Kelas Tahfizh</span>
                            </label>
                        </div>
                    </div>

                    @if($jenis_kelas === 'umum')
                        <!-- Tingkat Kelas Umum -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Tingkat Kelas (SD 1 - 6) <span class="text-rose-600">*</span></label>
                            <select wire:model="tingkat" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="1">Kelas 1</option>
                                <option value="2">Kelas 2</option>
                                <option value="3">Kelas 3</option>
                                <option value="4">Kelas 4</option>
                                <option value="5">Kelas 5</option>
                                <option value="6">Kelas 6</option>
                            </select>
                            @error('tingkat') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nama Kelas Umum -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Nama Kelas (Contoh: 1A, 1B, 2A) <span class="text-rose-600">*</span></label>
                            <input wire:model="nama_kelas" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="1A" />
                            @error('nama_kelas') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Wali Kelas Umum -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Wali Kelas (Guru Umum)</label>
                            <select wire:model="guru_umum_id" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="">-- Pilih Wali Kelas --</option>
                                @foreach ($gurusUmum as $g)
                                    <option value="{{ $g->id }}">{{ $g->user->nama }}</option>
                                @endforeach
                            </select>
                            @error('guru_umum_id') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>
                    @else
                        <!-- Guru Tahfizh -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Guru Tahfizh Pengampu <span class="text-rose-600">*</span></label>
                            <select wire:model.live="guru_tahfidz_id" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="">-- Pilih Guru Tahfizh --</option>
                                @foreach ($gurusTahfidz as $g)
                                    <option value="{{ $g->id }}">{{ $g->user->nama }}</option>
                                @endforeach
                            </select>
                            @error('guru_tahfidz_id') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nama Kelas Tahfizh -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Nama Halaqah Tahfizh <span class="text-rose-600">*</span></label>
                            <input wire:model="nama_kelas" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Halaqah Ustadz ..." />
                            <p class="text-[10px] text-stone-500 font-medium">Otomatis diisi berdasarkan nama Guru Tahfizh pengampu.</p>
                            @error('nama_kelas') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3 mt-4">
                        <button type="button" wire:click="$set('isFormOpen', false)" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md">
                            Simpan Kelas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
