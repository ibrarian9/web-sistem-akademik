<div class="space-y-6 font-sans">
    <!-- Quick Switcher Bar -->
    <div class="flex items-center gap-2 bg-white border border-stone-200 p-2 rounded-2xl overflow-x-auto shadow-xs">
        <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.kelas') : route('super-admin.kelas') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 01-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span>1. Buat &amp; Kelola Kelas (Umum &amp; Tahfizh)</span>
        </a>
        <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.siswa') : route('super-admin.siswa') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-700 text-white shadow-sm flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>2. Data Siswa</span>
        </a>
        <a href="{{ auth()->user()->role?->nama === 'tata_usaha' ? route('tata-usaha.plotting-kelas') : route('super-admin.plotting-kelas') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>3. Plotting Siswa Per-Kelas</span>
        </a>
    </div>


    <!-- Info & Tutorial Box -->

    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Data Siswa Aktif (Dua Kelas Per Siswa)"
        :steps="[
            ['title' => 'Tambah Siswa Baru', 'desc' => 'Klik + Tambah Siswa Baru untuk mendaftarkan NIS, NISN, biodata, serta wali murid.'],
            ['title' => 'Penetapan 2 Kelas Wajib', 'desc' => 'Setiap siswa wajib memilih 1 Kelas Umum (1-6 A/B/C) dan 1 Kelas Tahfizh (Halaqah Ustadz/ah).'],
            ['title' => 'Perubahan Status', 'desc' => 'Ubah status keaktifan menjadi Lulus, Pindah, atau Keluar saat terjadi pembaruan status pendidikan.']
        ]"
        notes="Username & password otomatis dibuatkan untuk akses portal siswa dan wali murid."
    />

    <!-- Hero Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                MANAJEMEN DATA SISWA
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Kelola Data Siswa &amp; Penempatan 2 Kelas</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Pencatatan biodata siswa, penempatan Kelas Umum (1-6) &amp; Kelas Tahfizh, dan akses portal.</p>
        </div>
        <button type="button" wire:click.prevent="openCreate" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition shadow-sm flex items-center gap-2">
            <x-lucide-plus class="w-4 h-4" />
            <span>Tambah Siswa Baru</span>
        </button>
    </div>

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <!-- Toolbar & Filter -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="relative max-w-md w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                    <x-lucide-search class="w-4 h-4" />
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari NIS, NISN, nama, atau username..."
                    class="w-full pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 placeholder-stone-400 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-xs" />
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

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs text-stone-800">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <th class="p-3.5 border-r border-emerald-700 w-28">NIS / NISN</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[180px]">Nama Siswa</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[140px]">Kelas Umum</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[160px]">Kelas Tahfizh</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[150px]">Wali Murid</th>
                        <th class="p-3.5 border-r border-emerald-700 w-24 text-center">Status</th>
                        <th class="p-3.5 text-center min-w-[140px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($siswas as $siswa)
                        <tr class="hover:bg-emerald-50/50 transition">
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
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-lg text-xs font-bold inline-flex items-center gap-1">
                                        <x-lucide-book-open class="w-3.5 h-3.5 text-emerald-700 shrink-0" />
                                        <span>{{ $siswa->kelas->nama_kelas }}</span>
                                    </span>
                                @else
                                    <span class="text-stone-400 italic text-[11px]">- Belum Set -</span>
                                @endif
                            </td>
                            <td class="p-3.5 border-r border-stone-200">
                                @if($siswa->kelasTahfidz)
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-900 border border-amber-300 rounded-lg text-xs font-bold inline-flex items-center gap-1">
                                        <x-lucide-bookmark class="w-3.5 h-3.5 text-amber-700 shrink-0" />
                                        <span>{{ $siswa->kelasTahfidz->nama_kelas }}</span>
                                    </span>
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
                                    <button type="button" wire:click.prevent="openDetail({{ $siswa->id }})" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-900 rounded-lg font-bold text-xs border border-emerald-300 transition shadow-xs flex items-center gap-1">
                                        <x-lucide-eye class="w-3.5 h-3.5 text-emerald-700" />
                                        <span>Detail</span>
                                    </button>
                                    <button type="button" wire:click.prevent="openEdit({{ $siswa->id }})" class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-lg font-bold text-xs border border-amber-300 transition shadow-xs flex items-center gap-1">
                                        <x-lucide-edit class="w-3.5 h-3.5 text-amber-700" />
                                        <span>Edit</span>
                                    </button>
                                    <button type="button" wire:click.prevent="delete({{ $siswa->id }})" data-confirm="Apakah Anda yakin ingin menghapus data siswa ini?" class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg font-bold text-xs border border-rose-300 transition shadow-xs flex items-center gap-1">
                                        <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-600" />
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-stone-500 font-semibold italic">
                                Tidak ada data siswa ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $siswas->links() }}
        </div>
    </div>

    <!-- Form Modal -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-[99990] flex items-center justify-center bg-stone-950/65 backdrop-blur-xs p-4 sm:p-6 pt-20 sm:pt-8 pb-8 overflow-y-auto">
            <div class="w-full max-w-2xl bg-white border border-stone-200 rounded-3xl shadow-2xl p-6 space-y-4 max-h-[85vh] my-auto overflow-y-auto shadow-stone-950/30">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3 sticky -top-6 bg-white z-20 pt-1">
                    <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">
                            <x-lucide-user-check class="w-3.5 h-3.5 text-emerald-900" />
                        </span>
                        <span>{{ $siswaId ? 'Edit Data Siswa & Dual Kelas' : 'Tambah Siswa Baru' }}</span>
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
                    <!-- Dual Kelas Selection Box -->
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl space-y-3">
                        <span class="text-xs font-black text-emerald-950 uppercase block">PENETAPAN KELAS SISWA (OPSIONAL / BISA MENYESUAIKAN)</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- 1. Kelas Umum -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-stone-700 uppercase">1. Kelas Umum (Opsional)</label>
                                <select wire:model="kelas_id" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                    <option value="">-- Belum Ada / Opsional --</option>
                                    @foreach ($kelasesUmum as $kls)
                                        <option value="{{ $kls->id }}">Kelas {{ $kls->nama_kelas }} (Wali: {{ $kls->guruUmum->user->nama ?? 'Admin' }})</option>
                                    @endforeach
                                </select>
                                @error('kelas_id') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                            </div>

                            <!-- 2. Kelas Tahfizh -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-stone-700 uppercase">2. Kelas Tahfizh (Halaqah)</label>
                                <select wire:model="kelas_tahfidz_id" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                    <option value="">-- Pilih Kelas Tahfizh --</option>
                                    @foreach ($kelasesTahfidz as $kls)
                                        <option value="{{ $kls->id }}">{{ $kls->nama_kelas }} (Pengampu: {{ $kls->guruTahfidz->user->nama ?? 'Admin' }})</option>
                                    @endforeach
                                </select>
                                @error('kelas_tahfidz_id') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Nama Lengkap <span class="text-rose-600">*</span></label>
                            <input wire:model="nama" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Ahmad Fauzi" />
                            @error('nama') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Username -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Username Login <span class="text-rose-600">*</span></label>
                            <input wire:model="username" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="fauzi1001" />
                            @error('username') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Email (Opsional)</label>
                            <input wire:model="email" type="email" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="siswa@mail.com" />
                            @error('email') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">{{ $siswaId ? 'Ganti Password (Kosongkan jika tidak)' : 'Password Awal' }}</label>
                            <input wire:model="password" type="password" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="••••••" />
                            @error('password') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- NIS -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">NIS <span class="text-rose-600">*</span></label>
                            <input wire:model="nis" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="1001" />
                            @error('nis') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- NISN -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">NISN (Opsional)</label>
                            <input wire:model="nisn" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="009812345" />
                            @error('nisn') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Jenis Kelamin</label>
                            <select wire:model="jenis_kelamin" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tanggal Masuk -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Masuk</label>
                            <input wire:model="tanggal_masuk" type="date" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                            @error('tanggal_masuk') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tempat Lahir -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Tempat Lahir</label>
                            <input wire:model="tempat_lahir" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Yogyakarta" />
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Lahir</label>
                            <input wire:model="tanggal_lahir" type="date" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                        </div>

                        <!-- Nama Wali -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Nama Wali Murid</label>
                            <input wire:model="nama_wali" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Nama wali murid" />
                        </div>

                        <!-- No HP Wali -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">No HP Wali Murid</label>
                            <input wire:model="no_hp_wali" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="0857..." />
                        </div>

                        <!-- Status -->
                        @if ($siswaId)
                            <div class="space-y-1 sm:col-span-2">
                                <label class="text-xs font-bold text-stone-700 uppercase">Status Keaktifan</label>
                                <select wire:model="status" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
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
                        <textarea wire:model="alamat" rows="2" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 resize-none" placeholder="Alamat rumah lengkap..."></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3">
                        <button type="button" wire:click="$set('isFormOpen', false)" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Student Detail Modal -->
    @if ($showDetailModal && $selectedSiswaDetail)
        <div class="fixed inset-0 z-[99990] flex items-center justify-center bg-stone-950/65 backdrop-blur-xs p-4 sm:p-6 pt-20 sm:pt-8 pb-8 overflow-y-auto">
            <div class="bg-white border border-stone-200 rounded-none max-w-2xl w-full shadow-2xl overflow-hidden relative animate-[fadeIn_0.2s_ease-out]">
                <!-- Top Accent Bar -->
                <div class="h-1.5 bg-emerald-600"></div>

                <div class="p-6 space-y-5 max-h-[85vh] overflow-y-auto">
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between gap-4 border-b border-stone-200 pb-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-none bg-emerald-700 text-white font-black text-lg flex items-center justify-center shrink-0 shadow-md shadow-emerald-700/30">
                                {{ strtoupper(substr($selectedSiswaDetail->user->nama ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-none text-[10px] font-black uppercase tracking-wider border bg-emerald-100 text-emerald-950 border-emerald-300">
                                        PROFIL SISWA
                                    </span>
                                    <x-status-badge :status="$selectedSiswaDetail->status" />
                                </div>
                                <h3 class="text-lg font-black text-stone-900 tracking-tight mt-0.5">
                                    {{ strtoupper($selectedSiswaDetail->user->nama ?? '-') }}
                                </h3>
                            </div>
                        </div>

                        <button type="button" wire:click="closeDetail" class="p-1.5 rounded-none text-stone-400 hover:text-stone-800 hover:bg-stone-100 transition text-sm font-bold">
                            ✕
                        </button>
                    </div>

                    <!-- Dual Class Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Kelas Umum -->
                        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-none space-y-1">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-950">
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
                        <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-none space-y-1">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-amber-950">
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
                        <div class="text-xs font-bold text-stone-800 uppercase tracking-wider">Informasi Identitas &amp; Akun Login</div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-0.5">
                                <div class="text-[10px] uppercase font-bold text-stone-400">NIS (Nomor Induk Siswa)</div>
                                <div class="font-mono font-bold text-stone-900 text-sm">{{ $selectedSiswaDetail->nis }}</div>
                            </div>

                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-0.5">
                                <div class="text-[10px] uppercase font-bold text-stone-400">NISN (Nomor Induk Siswa Nasional)</div>
                                <div class="font-mono font-bold text-stone-900 text-sm">{{ $selectedSiswaDetail->nisn ?: '-' }}</div>
                            </div>

                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-0.5">
                                <div class="text-[10px] uppercase font-bold text-stone-400">Username Portal</div>
                                <div class="font-mono font-bold text-stone-900">{{ $selectedSiswaDetail->user->username ?? '-' }}</div>
                            </div>

                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-0.5">
                                <div class="text-[10px] uppercase font-bold text-stone-400">Alamat Email</div>
                                <div class="font-medium text-stone-900">{{ $selectedSiswaDetail->user->email ?: '-' }}</div>
                            </div>

                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-0.5">
                                <div class="text-[10px] uppercase font-bold text-stone-400">Jenis Kelamin</div>
                                <div class="font-bold text-stone-900">
                                    {{ $selectedSiswaDetail->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </div>
                            </div>

                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-0.5">
                                <div class="text-[10px] uppercase font-bold text-stone-400">Tanggal Masuk Sekolah</div>
                                <div class="font-mono font-bold text-stone-900">
                                    {{ $selectedSiswaDetail->tanggal_masuk ? $selectedSiswaDetail->tanggal_masuk->format('d F Y') : '-' }}
                                </div>
                            </div>

                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-0.5 sm:col-span-2">
                                <div class="text-[10px] uppercase font-bold text-stone-400">Tempat &amp; Tanggal Lahir</div>
                                <div class="font-bold text-stone-900">
                                    {{ $selectedSiswaDetail->tempat_lahir ?: '-' }}, {{ $selectedSiswaDetail->tanggal_lahir ? $selectedSiswaDetail->tanggal_lahir->format('d F Y') : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Wali Murid & Alamat -->
                    <div class="space-y-2">
                        <div class="text-xs font-bold text-stone-800 uppercase tracking-wider">Data Orang Tua / Wali &amp; Alamat</div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-0.5">
                                <div class="text-[10px] uppercase font-bold text-stone-400">Nama Wali Murid</div>
                                <div class="font-bold text-stone-900">{{ $selectedSiswaDetail->nama_wali ?: '-' }}</div>
                            </div>

                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-0.5">
                                <div class="text-[10px] uppercase font-bold text-stone-400">No HP / WhatsApp Wali</div>
                                <div class="font-mono font-bold text-emerald-800">{{ $selectedSiswaDetail->no_hp_wali ?: '-' }}</div>
                            </div>

                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-0.5 sm:col-span-2">
                                <div class="text-[10px] uppercase font-bold text-stone-400">Alamat Tempat Tinggal</div>
                                <div class="font-medium text-stone-800 leading-relaxed">{{ $selectedSiswaDetail->alamat ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-stone-50 border-t border-stone-200 flex justify-end">
                    <button type="button" wire:click="closeDetail" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-none text-xs font-bold transition">
                        Tutup Detail
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
