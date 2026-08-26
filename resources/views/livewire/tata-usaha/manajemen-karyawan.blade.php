<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Direktori Karyawan & Tenaga Kependidikan" 
        subtitle="Kelola data kepegawaian staf Guru, TU, Finance, Pengawas, dan Kepala Sekolah."
        badge="MANAJEMEN KARYAWAN & STAF"
        badgeVariant="emerald"
        icon="users"
    >
        <x-slot:actions>
            <x-button variant="primary" size="md" icon="plus-circle" wire:click.prevent="openCreate">
                Tambah Karyawan Baru
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Direktori & Kelola Karyawan / Staff"
        :steps="[
            ['title' => 'Tambah Data & Akun', 'desc' => 'Klik Tambah Karyawan Baru untuk meregistrasikan pegawai/guru baru beserta akun login.'],
            ['title' => 'Pencarian & Filter Role', 'desc' => 'Gunakan kotak pencarian atau dropdown role untuk memfilter staf Guru, TU, Finance, Koordinator, atau Kepala Sekolah.'],
            ['title' => 'Edit & Status Akun', 'desc' => 'Gunakan tombol Edit pada kartu pegawai untuk memperbarui detail NIP, peranan (role), email, password, atau menonaktifkan akun.']
        ]"
        notes="Tata Usaha berhak mengelola data kepegawaian dan akun staf/guru secara terpusat."
    />

    <!-- Session Flash Notifications -->
    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Content Toolbar Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div class="flex-1 max-w-md">
            <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama / NIP / email..." />
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-stone-600">Filter Role:</span>
            <select wire:model.live="filterRole" class="bg-white border border-stone-300 text-stone-900 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
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
            <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-4 flex flex-col justify-between hover:border-emerald-400 hover:shadow-md transition">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 border border-emerald-300 text-emerald-950 flex items-center justify-center font-black text-sm select-none shadow-2xs">
                                {{ strtoupper(substr($k->nama, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold text-stone-900 leading-snug">{{ strtoupper($k->nama) }}</h3>
                                <p class="text-[10px] text-stone-500 font-medium">@ {{ $k->username }}</p>
                            </div>
                        </div>
                        <x-badge variant="emerald" size="xs">
                            {{ str_replace('_', ' ', $k->role->nama ?? '-') }}
                        </x-badge>
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
                        <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click.prevent="openEdit({{ $k->id }})" title="Edit Karyawan">
                            Edit
                        </x-button>
                        @if ($k->id !== auth()->id() && $k->role?->nama !== 'super_admin')
                            <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click.prevent="delete({{ $k->id }})" data-confirm="Apakah Anda yakin ingin menghapus akun karyawan {{ $k->nama }}?" title="Hapus Karyawan">
                                Hapus
                            </x-button>
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

    <!-- Floating Modal Form: Create & Edit Karyawan -->
    <x-floating-card 
        :show="$isFormOpen ? true : false"
        :title="$karyawanId ? 'Edit Data Karyawan & Akun' : 'Tambah Karyawan & Akun Baru'"
        subtitle="Lengkapi biodata staf dan kredensial login akun SIAKAD."
        badge="DATA KEPEGAWAIAN"
        badgeVariant="emerald"
        icon="users"
        maxWidth="max-w-xl"
        closeAction="$set('isFormOpen', false)"
    >
        @if ($errors->any())
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl space-y-1.5 text-xs shadow-2xs">
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
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Nama Lengkap -->
                <div class="sm:col-span-2 space-y-1">
                    <label class="block text-xs font-bold text-stone-700 uppercase">Nama Lengkap <span class="text-rose-600">*</span></label>
                    <input type="text" wire:model="nama" placeholder="Masukkan nama lengkap"
                        class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                    @error('nama') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Username -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-stone-700 uppercase">Username <span class="text-rose-600">*</span></label>
                    <input type="text" wire:model="username" placeholder="Username login"
                        class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                    @error('username') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Role / Peranan -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-stone-700 uppercase">Role Hak Akses <span class="text-rose-600">*</span></label>
                    <select wire:model="role_id" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
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
                        class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                    @error('email') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-stone-700 uppercase">
                        Password {{ $karyawanId ? '(Kosongkan jika tak diubah)' : '*' }}
                    </label>
                    <input type="password" wire:model="password" placeholder="Minimal 6 karakter"
                        class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                    @error('password') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- NIP / ID Staff -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-stone-700 uppercase">NIP / ID Staff (Opsional)</label>
                    <input type="text" wire:model="nip" placeholder="Contoh: 198501102010011005"
                        class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                    @error('nip') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- No HP -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-stone-700 uppercase">No. Telepon / WhatsApp</label>
                    <input type="text" wire:model="no_hp" placeholder="081234567890"
                        class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                    @error('no_hp') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Status Kepegawaian -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-stone-700 uppercase">Status Kepegawaian</label>
                    <select wire:model="status_kepegawaian" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
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
                    <select wire:model="status" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                    @error('status') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Alamat -->
                <div class="sm:col-span-2 space-y-1">
                    <label class="block text-xs font-bold text-stone-700 uppercase">Alamat Tempat Tinggal</label>
                    <textarea wire:model="alamat" rows="2" placeholder="Alamat domisili karyawan..."
                        class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-xs font-medium text-stone-900 focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"></textarea>
                    @error('alamat') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button type="button" variant="secondary" size="md" wire:click="$set('isFormOpen', false)">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="save">
                    Simpan Data
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
