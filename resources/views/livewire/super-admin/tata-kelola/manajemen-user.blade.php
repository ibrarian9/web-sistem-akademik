<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Manajemen Pengguna & Hak Akses"
        :steps="[
            ['title' => 'Tambah User Baru', 'desc' => 'Klik + Tambah Pengguna Baru untuk membuat akun login baru serta menentukan role hak akses.'],
            ['title' => 'Reset Password & Status', 'desc' => 'Klik tombol Edit untuk memperbarui password akun atau mengubah status keaktifan user.'],
            ['title' => 'Filter & Pencarian', 'desc' => 'Gunakan kotak pencarian untuk menemukan akun berdasarkan nama, username, atau email.']
        ]"
    />

    <!-- Hero Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                MANAJEMEN PENGGUNA SISTEM
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Kelola Akun &amp; Hak Akses Pengguna</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Pengaturan kredensial administrator, guru, staf keuangan, dan akun portal.</p>
        </div>
        <button type="button" wire:click.prevent="openCreate" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition shadow-sm flex items-center gap-2">
            <x-lucide-plus class="w-4 h-4" />
            <span>+ Tambah Pengguna Baru</span>
        </button>
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
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, username, atau email..."
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
                        <th class="p-3.5 border-r border-emerald-700 min-w-[200px]">Nama Lengkap</th>
                        <th class="p-3.5 border-r border-emerald-700 w-36">Username</th>
                        <th class="p-3.5 border-r border-emerald-700 w-36">Role / Hak Akses</th>
                        <th class="p-3.5 border-r border-emerald-700 w-28 text-center">Status Akun</th>
                        <th class="p-3.5 text-center min-w-[140px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($users as $u)
                        <tr class="hover:bg-emerald-50/50 transition">
                            <td class="p-3.5 border-r border-stone-200">
                                <div class="font-extrabold text-stone-900 text-xs">{{ strtoupper($u->nama) }}</div>
                                <div class="text-[10px] text-stone-500 font-medium">{{ $u->email ?: 'Tidak ada email' }}</div>
                            </td>
                            <td class="p-3.5 text-stone-800 font-bold border-r border-stone-200">@ {{ $u->username }}</td>
                            <td class="p-3.5 border-r border-stone-200">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300 uppercase tracking-wider">
                                    {{ str_replace('_', ' ', $u->role->nama ?? '-') }}
                                </span>
                            </td>
                            <td class="p-3.5 text-center border-r border-stone-200">
                                <x-status-badge :status="$u->status" />
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" wire:click.prevent="openEdit({{ $u->id }})" class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-lg font-bold text-xs border border-amber-300 transition shadow-xs flex items-center gap-1">
                                        <x-lucide-edit class="w-3.5 h-3.5 text-amber-700" />
                                        <span>Edit</span>
                                    </button>
                                    @if ($u->id !== auth()->id())
                                        <button type="button" wire:click.prevent="delete({{ $u->id }})" data-confirm="Apakah Anda yakin ingin menghapus akun pengguna ini?" class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg font-bold text-xs border border-rose-300 transition shadow-xs flex items-center gap-1">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-600" />
                                            <span>Hapus</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-stone-500 font-semibold italic">
                                Tidak ada data pengguna ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Form Modal -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-xs p-4">
            <div class="w-full max-w-md bg-white border border-stone-200 rounded-3xl shadow-2xl p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">★</span>
                        <span>{{ $userId ? 'Edit Akun Pengguna' : 'Tambah Pengguna Baru' }}</span>
                    </h3>
                    <button type="button" wire:click="$set('isFormOpen', false)" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold">✕</button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <!-- Nama -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Nama Lengkap <span class="text-rose-600">*</span></label>
                        <input wire:model="nama" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Ahmad Admin" />
                        @error('nama') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Username -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Username <span class="text-rose-600">*</span></label>
                        <input wire:model="username" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="ahmad_adm" />
                        @error('username') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Email (Opsional)</label>
                        <input wire:model="email" type="email" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="admin@sekolah.com" />
                        @error('email') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">{{ $userId ? 'Ganti Password (Kosongkan jika tidak)' : 'Password Awal' }}</label>
                        <input wire:model="password" type="password" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="••••••" />
                        @error('password') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Role -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Hak Akses / Role <span class="text-rose-600">*</span></label>
                        <select wire:model="role_id" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                            <option value="">Pilih Role</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->id }}">{{ ucfirst(str_replace('_', ' ', $r->nama)) }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status -->
                    @if ($userId)
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Status Keaktifan</label>
                            <select wire:model="status" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif / Blokir</option>
                            </select>
                            @error('status') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3 mt-6">
                        <button type="button" wire:click="$set('isFormOpen', false)" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md">
                            Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
