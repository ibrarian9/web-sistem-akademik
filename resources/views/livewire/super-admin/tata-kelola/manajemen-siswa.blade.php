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
            <x-button type="button" variant="primary" size="md" icon="plus" wire:click.prevent="openCreate">
                Tambah Siswa Baru
            </x-button>
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
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari NIS, NISN, nama, atau username..." />
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

        <!-- Data Table -->
        <x-table loadingTarget="search, perPage, page">
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
                                <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click.prevent="openEdit({{ $siswa->id }})">
                                    Edit
                                </x-button>
                                <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click.prevent="delete({{ $siswa->id }})" data-confirm="Apakah Anda yakin ingin menghapus data siswa ini?">
                                    Hapus
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-stone-400">
                            <x-table.empty title="Tidak ada data siswa ditemukan" subtitle="Pastikan kata kunci pencarian benar atau tambahkan data siswa baru." />
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
        :title="$siswaId ? 'Edit Data Siswa & Dual Kelas' : 'Tambah Siswa Baru'"
        subtitle="Lengkapi biodata santri dan penempatan rombel kelas umum & tahfizh."
        badge="DATA SISWA"
        badgeVariant="emerald"
        icon="user-check"
        maxWidth="max-w-2xl"
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
            <!-- Dual Kelas Selection Box -->
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl space-y-3">
                <span class="text-xs font-extrabold text-emerald-950 uppercase block">PENETAPAN KELAS SISWA (OPSIONAL / BISA MENYESUAIKAN)</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- 1. Kelas Umum -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">1. Kelas Umum (Opsional)</label>
                        <select wire:model="kelas_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                            <option value="">-- Belum Ada / Opsional --</option>
                            @foreach ($kelasesUmum as $kls)
                                <option value="{{ $kls->id }}">Kelas {{ $kls->nama_kelas }} (Wali: {{ $kls->guruUmum->user->nama ?? 'Admin' }})</option>
                            @endforeach
                        </select>
                        @error('kelas_id') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- 2. Kelas Tahfizh -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">2. Kelas Tahfizh (Halaqah)</label>
                        <select wire:model="kelas_tahfidz_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                            <option value="">-- Pilih Kelas Tahfizh --</option>
                            @foreach ($kelasesTahfidz as $kls)
                                <option value="{{ $kls->id }}">{{ $kls->nama_kelas }} (Pengampu: {{ $kls->guruTahfidz->user->nama ?? 'Admin' }})</option>
                            @endforeach
                        </select>
                        @error('kelas_tahfidz_id') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Nama -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Nama Lengkap <span class="text-rose-600">*</span></label>
                    <input wire:model="nama" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Ahmad Fauzi" required />
                    @error('nama') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Username -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Username Login <span class="text-rose-600">*</span></label>
                    <input wire:model="username" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="fauzi1001" required />
                    @error('username') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Email (Opsional)</label>
                    <input wire:model="email" type="email" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="siswa@mail.com" />
                    @error('email') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">{{ $siswaId ? 'Ganti Password (Kosongkan jika tidak)' : 'Password Awal *' }}</label>
                    <input wire:model="password" type="password" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="••••••" />
                    @error('password') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- NIS -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">NIS <span class="text-rose-600">*</span></label>
                    <input wire:model="nis" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="1001" required />
                    @error('nis') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- NISN -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">NISN (Opsional)</label>
                    <input wire:model="nisn" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="009812345" />
                    @error('nisn') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Jenis Kelamin -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Jenis Kelamin</label>
                    <select wire:model="jenis_kelamin" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Masuk -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Masuk</label>
                    <input wire:model="tanggal_masuk" type="date" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                    @error('tanggal_masuk') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tempat Lahir -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tempat Lahir</label>
                    <input wire:model="tempat_lahir" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Yogyakarta" />
                </div>

                <!-- Tanggal Lahir -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Lahir</label>
                    <input wire:model="tanggal_lahir" type="date" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                </div>

                <!-- Nama Wali -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Nama Wali Murid</label>
                    <input wire:model="nama_wali" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Nama wali murid" />
                </div>

                <!-- No HP Wali -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">No HP Wali Murid</label>
                    <input wire:model="no_hp_wali" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="0857..." />
                </div>

                <!-- Status -->
                @if ($siswaId)
                    <div class="space-y-1 sm:col-span-2">
                        <label class="text-xs font-bold text-stone-700 uppercase">Status Keaktifan</label>
                        <select wire:model="status" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                            <option value="aktif">Aktif</option>
                            <option value="lulus">Lulus</option>
                            <option value="pindah">Pindah</option>
                            <option value="keluar">Keluar</option>
                        </select>
                    </div>
                @endif
            </div>

            <!-- Alamat -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Alamat Rumah</label>
                <textarea wire:model="alamat" rows="2" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none" placeholder="Alamat rumah lengkap..."></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3">
                <x-button type="button" variant="secondary" size="md" wire:click="$set('isFormOpen', false)">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="save">
                    Simpan Perubahan
                </x-button>
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
        maxWidth="max-w-2xl"
        closeAction="closeDetail"
    >
        @if ($selectedSiswaDetail)
            <div class="space-y-4 text-xs">
                <!-- Dual Class Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                            <div class="text-xs text-stone-400 italic pt-1">- Belum Ditempatkan di Kelas Umum -</div>
                        @endif
                    </div>

                    <!-- Kelas Tahfizh -->
                    <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl space-y-1">
                        <div class="flex items-center gap-1.5 text-xs font-extrabold text-amber-950">
                            <x-lucide-bookmark class="w-4 h-4 text-amber-700 shrink-0" />
                            <span>2. Kelas Tahfizh / Halaqah</span>
                        </div>
                        @if($selectedSiswaDetail->kelasTahfidz)
                            <div class="text-sm font-black text-amber-900 pt-0.5">
                                {{ $selectedSiswaDetail->kelasTahfidz->nama_kelas }}
                            </div>
                            <div class="text-[11px] text-amber-800 font-medium">
                                Pengampu: <strong>{{ $selectedSiswaDetail->kelasTahfidz->guruTahfidz->user->nama ?? 'Belum Ditentukan' }}</strong>
                            </div>
                        @else
                            <div class="text-xs text-stone-400 italic pt-1">- Belum Ditempatkan di Kelas Tahfizh -</div>
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
