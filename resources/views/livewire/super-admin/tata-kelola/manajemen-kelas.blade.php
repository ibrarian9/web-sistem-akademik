<div class="space-y-6 font-sans">
    <!-- Quick Switcher Bar -->
    <div class="flex items-center gap-2 bg-white border border-stone-200 p-2 rounded-2xl overflow-x-auto shadow-2xs">
        <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.kelas') : route('super-admin.kelas') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-700 text-white shadow-2xs flex items-center gap-2 whitespace-nowrap">
            <x-lucide-layers class="w-4 h-4 text-emerald-100" />
            <span>1. Buat & Kelola Kelas (Umum & Tahfizh)</span>
        </a>
        <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.siswa') : route('super-admin.siswa') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2 whitespace-nowrap">
            <x-lucide-users class="w-4 h-4 text-emerald-600" />
            <span>2. Data Siswa</span>
        </a>
        <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.plotting-kelas') : route('super-admin.plotting-kelas') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2 whitespace-nowrap">
            <x-lucide-user-plus class="w-4 h-4 text-emerald-600" />
            <span>3. Plotting Siswa Per-Kelas</span>
        </a>
    </div>

    <!-- Header Title Bar -->
    <x-page-header 
        title="Kelola Kelas Umum & Kelompok Tahfizh" 
        subtitle="Buat rombongan belajar Kelas Umum (1-6) dan kelompok Halaqah Guru Tahfizh."
        badge="MANAJEMEN KELAS & HALAQAH"
        badgeVariant="emerald"
        icon="layers"
    >
        <x-slot:actions>
            <x-button type="button" variant="primary" size="md" icon="plus" wire:click.prevent="openCreate">
                Tambah Kelas Baru
            </x-button>
        </x-slot:actions>
    </x-page-header>

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

    <!-- Info Banner Semester Requirement -->
    <div class="p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs font-bold shadow-2xs">
        <div class="flex items-center gap-2.5">
            <x-lucide-calendar class="w-5 h-5 text-amber-600 shrink-0" />
            <div>
                <p class="font-extrabold text-amber-950">Petunjuk Semester Aktif:</p>
                <p class="text-[11px] text-amber-800 font-medium">Setiap kelas otomatis ditautkan ke Semester & Tahun Ajaran Aktif. Anda dapat mengatur Tahun Ajaran terlebih dahulu melalui menu Kalender Akademik.</p>
            </div>
        </div>
        <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.kalender-akademik') : route('super-admin.kalender-akademik') }}"
           class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-extrabold shrink-0 flex items-center gap-1.5 transition shadow-2xs">
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
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Toolbar & Filter -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama kelas..." />
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-stone-600">Jenis Kelas:</span>
                    <select wire:model.live="filterJenis" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="semua">Semua Jenis Kelas</option>
                        <option value="umum">Kelas Umum (1-6)</option>
                        <option value="tahfidz">Kelas Tahfizh (Halaqah)</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-stone-600">Tampilkan:</span>
                    <select wire:model.live="perPage" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="10">10 Baris</option>
                        <option value="25">25 Baris</option>
                        <option value="50">50 Baris</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <x-table loadingTarget="search, filterJenis, perPage">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="min-w-[180px]">Nama Kelas / Halaqah</x-table.th>
                    <x-table.th align="center" class="w-36">Jenis Kelas</x-table.th>
                    <x-table.th align="center" class="w-28">Tingkat</x-table.th>
                    <x-table.th class="min-w-[200px]">Guru Pengampu / Wali Kelas</x-table.th>
                    <x-table.th align="center" class="w-32">Total Siswa</x-table.th>
                    <x-table.th align="center" class="w-36">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($kelases as $kelas)
                    @php
                        $isTahfizh = $kelas->jenis_kelas === 'tahfidz';
                        $cntSiswa = $isTahfizh ? $kelas->siswasTahfidz()->count() : $kelas->siswas()->count();
                    @endphp
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ $kelas->nama_kelas }}</div>
                            <div class="text-[10px] text-stone-500 font-medium">Semester Active: {{ $kelas->semester->semester ?? 'Ganjil' }}</div>
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if($isTahfizh)
                                <x-badge variant="amber" size="xs">★ Tahfizh</x-badge>
                            @else
                                <x-badge variant="emerald" size="xs">📚 Umum</x-badge>
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
                            <span class="px-2.5 py-0.5 bg-stone-100 border border-stone-300 rounded-lg text-xs font-bold">
                                {{ $cntSiswa }} Santri
                            </span>
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click.prevent="openEdit({{ $kelas->id }})">
                                    Edit
                                </x-button>
                                <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click.prevent="delete({{ $kelas->id }})" data-confirm="Apakah Anda yakin ingin menghapus kelas {{ $kelas->nama_kelas }} ini?">
                                    Hapus
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-stone-400">
                            <x-table.empty title="Tidak ada data kelas ditemukan" subtitle="Gunakan tombol Tambah Kelas Baru di atas untuk membuat rombel." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $kelases->links() }}
        </div>
    </div>

    <!-- Form Floating Modal -->
    <x-floating-card 
        :show="$isFormOpen ? true : false"
        :title="$kelasId ? 'Edit Data Kelas' : 'Tambah Kelas Baru'"
        subtitle="Atur rombongan belajar umum (1-6) atau halaqah tahfizh."
        badge="KELAS & HALAQAH"
        badgeVariant="emerald"
        icon="layers"
        maxWidth="max-w-md"
        closeAction="$set('isFormOpen', false)"
    >
        @if ($errors->any())
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl space-y-1.5 text-xs shadow-2xs mb-4">
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

        <form wire:submit.prevent="save" action="javascript:void(0);" class="space-y-4 text-xs">
            <!-- Switch Jenis Kelas -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase">Jenis Kelas <span class="text-rose-600">*</span></label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center justify-center p-2.5 rounded-xl border text-xs font-bold cursor-pointer transition {{ $jenis_kelas === 'umum' ? 'bg-emerald-100 border-emerald-500 text-emerald-950 shadow-2xs' : 'bg-stone-50 border-stone-200 text-stone-600 hover:bg-stone-100' }}">
                        <input type="radio" wire:model.live="jenis_kelas" value="umum" class="sr-only">
                        <span>📚 Kelas Umum (1-6)</span>
                    </label>
                    <label class="flex items-center justify-center p-2.5 rounded-xl border text-xs font-bold cursor-pointer transition {{ $jenis_kelas === 'tahfidz' ? 'bg-amber-100 border-amber-500 text-amber-950 shadow-2xs' : 'bg-stone-50 border-stone-200 text-stone-600 hover:bg-stone-100' }}">
                        <input type="radio" wire:model.live="jenis_kelas" value="tahfidz" class="sr-only">
                        <span>★ Kelas Tahfizh</span>
                    </label>
                </div>
            </div>

            @if($jenis_kelas === 'umum')
                <!-- Tingkat Kelas Umum -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tingkat Kelas (SD 1 - 6) <span class="text-rose-600">*</span></label>
                    <select wire:model="tingkat" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required>
                        <option value="1">Kelas 1</option>
                        <option value="2">Kelas 2</option>
                        <option value="3">Kelas 3</option>
                        <option value="4">Kelas 4</option>
                        <option value="5">Kelas 5</option>
                        <option value="6">Kelas 6</option>
                    </select>
                    @error('tingkat') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Nama Kelas Umum -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Nama Kelas (Contoh: 1A, 1B, 2A) <span class="text-rose-600">*</span></label>
                    <input wire:model="nama_kelas" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="1A" required />
                    @error('nama_kelas') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Wali Kelas Umum -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Wali Kelas (Guru Umum)</label>
                    <select wire:model="guru_umum_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach ($gurusUmum as $g)
                            <option value="{{ $g->id }}">{{ $g->user->nama }}</option>
                        @endforeach
                    </select>
                    @error('guru_umum_id') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            @else
                <!-- Guru Tahfizh -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Guru Tahfizh Pengampu <span class="text-rose-600">*</span></label>
                    <select wire:model.live="guru_tahfidz_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required>
                        <option value="">-- Pilih Guru Tahfizh --</option>
                        @foreach ($gurusTahfidz as $g)
                            <option value="{{ $g->id }}">{{ $g->user->nama }}</option>
                        @endforeach
                    </select>
                    @error('guru_tahfidz_id') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Nama Kelas Tahfizh -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Nama Halaqah Tahfizh <span class="text-rose-600">*</span></label>
                    <input wire:model="nama_kelas" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Halaqah Ustadz ..." required />
                    <p class="text-[10px] text-stone-500 font-medium">Otomatis diisi berdasarkan nama Guru Tahfizh pengampu.</p>
                    @error('nama_kelas') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3">
                <x-button type="button" variant="secondary" size="md" wire:click="$set('isFormOpen', false)">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="save">
                    Simpan Kelas
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
