<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Direktori & Kelola Karyawan / Staff"
        :steps="[
            ['title' => 'Tambah Data & Akun', 'desc' => 'Klik tombol Tambah Karyawan Baru untuk meregistrasikan pegawai/guru baru beserta pembuat akun login.'],
            ['title' => 'Pencarian & Filter Role', 'desc' => 'Gunakan kotak pencarian atau dropdown role untuk memfilter staf Guru, TU, Finance, Koordinator, atau Kepala Sekolah.'],
            ['title' => 'Edit & Status Akun', 'desc' => 'Gunakan tombol Edit pada kartu pegawai untuk memperbarui detail NIP, peranan (role), email, password, atau menonaktifkan akun.']
        ]"
        notes="Tata Usaha berhak mengelola data kepegawaian dan akun staf/guru secara terpusat."
    />

    <!-- Session Flash Notifications -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-xs shadow-sm">
            <div class="flex items-center gap-2">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-600 shrink-0" />
                <span class="font-medium">{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center justify-between text-xs shadow-sm">
            <div class="flex items-center gap-2">
                <x-lucide-alert-circle class="w-4 h-4 text-rose-600 shrink-0" />
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
    @endif

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Direktori Karyawan &amp; Staff</h2>
            <p class="text-xs text-stone-500">Kelola data tenaga pendidik (guru) dan kependidikan (TU, Finance, Koordinator, Kepala Sekolah).</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button wire:click="openCreate" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-sm transition">
                <x-lucide-plus-circle class="w-4 h-4" />
                <span>Tambah Karyawan Baru</span>
            </button>

            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama / NIP / email..." 
                    class="bg-stone-50 border border-stone-300 text-stone-800 placeholder-stone-400 rounded-xl pl-9 pr-4 py-2 text-xs focus:outline-none focus:border-indigo-500 w-56 sm:w-64" />
                <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" />
            </div>

            <select wire:model.live="filterRole" class="bg-stone-50 border border-stone-300 text-stone-800 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:border-indigo-500">
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
            <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-4 flex flex-col justify-between hover:border-indigo-300 transition">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm select-none">
                                {{ strtoupper(substr($k->nama, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-stone-800 leading-snug">{{ $k->nama }}</h3>
                                <p class="text-[10px] text-stone-500">@ {{ $k->username }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-stone-100 text-stone-700 border border-stone-200 rounded-lg text-[10px] font-extrabold uppercase">
                            {{ str_replace('_', ' ', $k->role->nama ?? '-') }}
                        </span>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-stone-100 text-xs">
                        <div class="flex justify-between text-stone-500">
                            <span class="text-[10px]">NIP / ID Staff</span>
                            <span class="text-stone-800 font-semibold text-[10px]">{{ $k->guru->nip ?? ($k->nip ?? '-') }}</span>
                        </div>
                        @if ($k->guru)
                            <div class="flex justify-between text-stone-500">
                                <span class="text-[10px]">Jenis Guru</span>
                                <span class="text-indigo-600 font-semibold text-[10px] uppercase">{{ $k->guru->jenis_guru }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-stone-500">
                            <span class="text-[10px]">Email</span>
                            <span class="text-stone-700 text-[10px] truncate max-w-[140px]">{{ $k->email ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between text-stone-500">
                            <span class="text-[10px]">No. Telepon</span>
                            <span class="text-stone-700 text-[10px]">{{ $k->no_hp ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-stone-100 flex justify-between items-center text-[10px]">
                    <div class="flex items-center gap-1.5">
                        <span class="text-stone-500">Status:</span>
                        <span class="px-2 py-0.5 rounded-md font-extrabold uppercase {{ $k->status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                            {{ $k->status }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button wire:click="openEdit({{ $k->id }})" class="p-1.5 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition" title="Edit Karyawan & Akun">
                            <x-lucide-edit-3 class="w-4 h-4" />
                        </button>
                        @if ($k->id !== auth()->id() && $k->role?->nama !== 'super_admin')
                            <button wire:confirm="Apakah Anda yakin ingin menghapus akun karyawan {{ $k->nama }}?" wire:click="delete({{ $k->id }})" class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition" title="Hapus Karyawan">
                                <x-lucide-trash-2 class="w-4 h-4" />
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-stone-400 text-xs">
                Tidak ada data karyawan yang sesuai kriteria pencarian.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $karyawanList->links() }}
    </div>

    <!-- Modal Form Create & Edit Karyawan -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-stone-200 space-y-6 animate-in fade-in zoom-in duration-150">
                <div class="flex justify-between items-center border-b border-stone-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-stone-800">
                            {{ $karyawanId ? 'Edit Data Karyawan & Akun' : 'Tambah Karyawan & Akun Baru' }}
                        </h3>
                        <p class="text-xs text-stone-500">Lengkapi data pribadi dan informasi akun login pegawai.</p>
                    </div>
                    <button wire:click="$set('isFormOpen', false)" class="text-stone-400 hover:text-stone-600 p-1 rounded-xl hover:bg-stone-100">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Lengkap -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="nama" placeholder="Masukkan nama lengkap"
                                class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                            @error('nama') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Username -->
                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Username <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="username" placeholder="Username login"
                                class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                            @error('username') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Role / Peranan -->
                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Role Hak Akses <span class="text-rose-500">*</span></label>
                            <select wire:model="role_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($selectableRoles as $r)
                                    <option value="{{ $r->id }}">{{ ucwords(str_replace('_', ' ', $r->nama)) }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Email (Opsional)</label>
                            <input type="email" wire:model="email" placeholder="karyawan@sekolah.sch.id"
                                class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                            @error('email') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">
                                Password {{ $karyawanId ? '(Kosongkan jika tak diubah)' : '*' }}
                            </label>
                            <input type="password" wire:model="password" placeholder="Minimal 6 karakter"
                                class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                            @error('password') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- NIP / ID Staff -->
                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">NIP / ID Staff (Opsional)</label>
                            <input type="text" wire:model="nip" placeholder="Contoh: 198501102010011005"
                                class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                            @error('nip') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- No HP -->
                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">No. Telepon / WhatsApp</label>
                            <input type="text" wire:model="no_hp" placeholder="081234567890"
                                class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                            @error('no_hp') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status Kepegawaian -->
                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Status Kepegawaian</label>
                            <select wire:model="status_kepegawaian" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                <option value="pns">PNS / ASN</option>
                                <option value="gtt">GTT / Guru Kontrak</option>
                                <option value="honorer">Honorer</option>
                                <option value="tetap_yayasan">Tetap Yayasan</option>
                            </select>
                            @error('status_kepegawaian') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status Akun -->
                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Status Akun</label>
                            <select wire:model="status" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                            @error('status') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Alamat -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Alamat Tempat Tinggal</label>
                            <textarea wire:model="alamat" rows="2" placeholder="Alamat domisili karyawan..."
                                class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                            @error('alamat') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-stone-100">
                        <button type="button" wire:click="$set('isFormOpen', false)" class="px-4 py-2 text-xs font-bold text-stone-600 hover:text-stone-800 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition flex items-center gap-2">
                            <x-lucide-save class="w-4 h-4" />
                            <span>Simpan Data</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
