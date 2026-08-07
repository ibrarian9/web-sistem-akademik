<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Direktori & Kelola Karyawan / Staff"
        :steps="[
            ['title' => 'Tambah Data & Akun', 'desc' => 'Klik + Tambah Karyawan Baru untuk meregistrasikan pegawai/guru baru beserta akun login.'],
            ['title' => 'Pencarian & Filter Role', 'desc' => 'Gunakan kotak pencarian atau dropdown role untuk memfilter staf Guru, TU, Finance, Koordinator, atau Kepala Sekolah.'],
            ['title' => 'Edit & Status Akun', 'desc' => 'Gunakan tombol Edit pada kartu pegawai untuk memperbarui detail NIP, peranan (role), email, password, atau menonaktifkan akun.']
        ]"
        notes="Tata Usaha berhak mengelola data kepegawaian dan akun staf/guru secara terpusat."
    />

    <!-- Hero Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                MANAJEMEN KARYAWAN &amp; STAF
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Direktori Karyawan &amp; Tenaga Kependidikan</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Kelola data kepegawaian staf Guru, TU, Finance, Pengawas, dan Kepala Sekolah.</p>
        </div>
        <button type="button" wire:click.prevent="openCreate" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition shadow-sm flex items-center gap-2">
            <x-lucide-plus-circle class="w-4 h-4" />
            <span>+ Tambah Karyawan Baru</span>
        </button>
    </div>

    <!-- Session Flash Notifications -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-700 shrink-0" />
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-extrabold">✕</button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-300 text-rose-900 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2">
                <x-lucide-alert-circle class="w-4 h-4 text-rose-700 shrink-0" />
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-extrabold">✕</button>
        </div>
    @endif

    <!-- Content Toolbar Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama / NIP / email..." 
                class="w-full bg-white border border-stone-300 text-stone-900 placeholder-stone-400 rounded-xl pl-9 pr-4 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-xs" />
            <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3 top-2.5 pointer-events-none" />
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-stone-600">Filter Role:</span>
            <select wire:model.live="filterRole" class="bg-white border border-stone-300 text-stone-900 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                <option value="semua">Semua Role</option>
                <option value="guru">Guru</option>
                <option value="tata_usaha">Tata Usaha</option>
                <option value="finance">Finance</option>
                <option value="pengawas">Pengawas / Koordinator</option>
                <option value="kepala_sekolah">Kepala Sekolah</option>
                <option value="super_admin">Super Admin</option>
            </select>
        </div>
    </div>

    <!-- Employee Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($karyawanList as $k)
            <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-4 flex flex-col justify-between hover:border-emerald-400 hover:shadow-md transition">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 border border-emerald-300 text-emerald-950 flex items-center justify-center font-black text-sm select-none shadow-xs">
                                {{ strtoupper(substr($k->nama, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold text-stone-900 leading-snug">{{ strtoupper($k->nama) }}</h3>
                                <p class="text-[10px] text-stone-500 font-medium">@ {{ $k->username }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-lg text-[10px] font-extrabold uppercase">
                            {{ str_replace('_', ' ', $k->role->nama ?? '-') }}
                        </span>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-stone-100 text-xs">
                        <div class="flex justify-between text-stone-500">
                            <span class="text-[10px] font-medium">NIP / ID Staff:</span>
                            <span class="text-stone-900 font-extrabold text-[10px]">{{ $k->guru->nip ?? ($k->nip ?? '-') }}</span>
                        </div>
                        @if ($k->guru)
                            <div class="flex justify-between text-stone-500">
                                <span class="text-[10px] font-medium">Jenis Guru:</span>
                                <span class="text-emerald-800 font-extrabold text-[10px] uppercase">{{ $k->guru->jenis_guru }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-stone-500">
                            <span class="text-[10px] font-medium">Email:</span>
                            <span class="text-stone-800 font-bold text-[10px] truncate max-w-[140px]">{{ $k->email ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between text-stone-500">
                            <span class="text-[10px] font-medium">No. Telepon:</span>
                            <span class="text-stone-800 font-bold text-[10px]">{{ $k->no_hp ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-stone-100 flex justify-between items-center text-[10px]">
                    <div class="flex items-center gap-1.5">
                        <span class="text-stone-500 font-bold">Status:</span>
                        <x-status-badge :status="$k->status" />
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button type="button" wire:click.prevent="openEdit({{ $k->id }})" class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-lg font-bold text-xs border border-amber-300 transition shadow-xs flex items-center gap-1" title="Edit Karyawan">
                            <x-lucide-edit-3 class="w-3.5 h-3.5 text-amber-700" />
                            <span>Edit</span>
                        </button>
                        @if ($k->id !== auth()->id() && $k->role?->nama !== 'super_admin')
                            <button type="button" wire:click.prevent="delete({{ $k->id }})" data-confirm="Apakah Anda yakin ingin menghapus akun karyawan {{ $k->nama }}?" class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg font-bold text-xs border border-rose-300 transition shadow-xs flex items-center gap-1" title="Hapus Karyawan">
                                <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-600" />
                                <span>Hapus</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-stone-500 font-semibold italic text-xs bg-white border border-stone-200 rounded-2xl">
                Tidak ada data karyawan yang sesuai kriteria pencarian.
            </div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $karyawanList->links() }}
    </div>

    <!-- Modal Form Create & Edit Karyawan -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-xs p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-stone-200 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center border-b border-stone-200 pb-3">
                    <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">★</span>
                        <span>{{ $karyawanId ? 'Edit Data Karyawan & Akun' : 'Tambah Karyawan & Akun Baru' }}</span>
                    </h3>
                    <button type="button" wire:click="$set('isFormOpen', false)" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold">✕</button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Lengkap -->
                        <div class="sm:col-span-2 space-y-1">
                            <label class="block text-xs font-bold text-stone-700 uppercase">Nama Lengkap <span class="text-rose-600">*</span></label>
                            <input type="text" wire:model="nama" placeholder="Masukkan nama lengkap"
                                class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600" />
                            @error('nama') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Username -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-stone-700 uppercase">Username <span class="text-rose-600">*</span></label>
                            <input type="text" wire:model="username" placeholder="Username login"
                                class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600" />
                            @error('username') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Role / Peranan -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-stone-700 uppercase">Role Hak Akses <span class="text-rose-600">*</span></label>
                            <select wire:model="role_id" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($selectableRoles as $r)
                                    <option value="{{ $r->id }}">{{ ucwords(str_replace('_', ' ', $r->nama)) }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-stone-700 uppercase">Email (Opsional)</label>
                            <input type="email" wire:model="email" placeholder="karyawan@sekolah.sch.id"
                                class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600" />
                            @error('email') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-stone-700 uppercase">
                                Password {{ $karyawanId ? '(Kosongkan jika tak diubah)' : '*' }}
                            </label>
                            <input type="password" wire:model="password" placeholder="Minimal 6 karakter"
                                class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600" />
                            @error('password') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- NIP / ID Staff -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-stone-700 uppercase">NIP / ID Staff (Opsional)</label>
                            <input type="text" wire:model="nip" placeholder="Contoh: 198501102010011005"
                                class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600" />
                            @error('nip') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- No HP -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-stone-700 uppercase">No. Telepon / WhatsApp</label>
                            <input type="text" wire:model="no_hp" placeholder="081234567890"
                                class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600" />
                            @error('no_hp') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status Kepegawaian -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-stone-700 uppercase">Status Kepegawaian</label>
                            <select wire:model="status_kepegawaian" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600">
                                <option value="pns">PNS / ASN</option>
                                <option value="gtt">GTT / Guru Kontrak</option>
                                <option value="honorer">Honorer</option>
                                <option value="tetap_yayasan">Tetap Yayasan</option>
                            </select>
                            @error('status_kepegawaian') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status Akun -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-stone-700 uppercase">Status Akun</label>
                            <select wire:model="status" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                            @error('status') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Alamat -->
                        <div class="sm:col-span-2 space-y-1">
                            <label class="block text-xs font-bold text-stone-700 uppercase">Alamat Tempat Tinggal</label>
                            <textarea wire:model="alamat" rows="2" placeholder="Alamat domisili karyawan..."
                                class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-xs font-medium text-stone-900 focus:ring-2 focus:ring-emerald-600 resize-none"></textarea>
                            @error('alamat') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                        <button type="button" wire:click="$set('isFormOpen', false)" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md flex items-center gap-2">
                            <x-lucide-save class="w-4 h-4" />
                            <span>Simpan Data</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
