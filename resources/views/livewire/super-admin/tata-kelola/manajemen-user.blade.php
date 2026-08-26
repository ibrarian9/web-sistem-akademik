<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Kelola Akun & Hak Akses Pengguna" 
        subtitle="Pengaturan kredensial administrator, guru, staf keuangan, dan akun portal."
        badge="MANAJEMEN PENGGUNA"
        badgeVariant="emerald"
        icon="users"
    >
        <x-slot:actions>
            <x-button type="button" variant="primary" size="md" icon="plus" wire:click.prevent="openCreate">
                Tambah Pengguna Baru
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Manajemen Pengguna & Hak Akses"
        :steps="[
            ['title' => 'Tambah User Baru', 'desc' => 'Klik Tambah Pengguna Baru untuk membuat akun login baru serta menentukan role hak akses.'],
            ['title' => 'Reset Password & Status', 'desc' => 'Klik tombol Edit untuk memperbarui password akun atau mengubah status keaktifan user.'],
            ['title' => 'Filter & Pencarian', 'desc' => 'Gunakan kotak pencarian untuk menemukan akun berdasarkan nama, username, atau email.']
        ]"
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
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama, username, atau email..." />
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
        <x-table loadingTarget="search, perPage">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="min-w-[200px]">Nama Lengkap</x-table.th>
                    <x-table.th class="w-40">Username</x-table.th>
                    <x-table.th class="w-40">Role / Hak Akses</x-table.th>
                    <x-table.th align="center" class="w-32">Status Akun</x-table.th>
                    <x-table.th align="center" class="w-36">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($users as $u)
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ strtoupper($u->nama) }}</div>
                            <div class="text-[10px] text-stone-500 font-medium">{{ $u->email ?: 'Tidak ada email' }}</div>
                        </td>
                        <td class="p-3.5 text-stone-800 font-bold border-r border-stone-200 font-mono text-xs">@ {{ $u->username }}</td>
                        <td class="p-3.5 border-r border-stone-200">
                            <x-badge variant="emerald" size="xs">
                                {{ str_replace('_', ' ', $u->role->nama ?? '-') }}
                            </x-badge>
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            <x-status-badge :status="$u->status" />
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click.prevent="openEdit({{ $u->id }})">
                                    Edit
                                </x-button>
                                @if ($u->id !== auth()->id())
                                    <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click.prevent="delete({{ $u->id }})" data-confirm="Apakah Anda yakin ingin menghapus akun pengguna ini?">
                                        Hapus
                                    </x-button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-stone-400">
                            <x-table.empty title="Tidak ada data pengguna ditemukan" subtitle="Gunakan tombol Tambah Pengguna di atas untuk membuat akun baru." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Form Floating Modal -->
    <x-floating-card 
        :show="$isFormOpen ? true : false"
        :title="$userId ? 'Edit Akun Pengguna' : 'Tambah Pengguna Baru'"
        subtitle="Lengkapi data pengguna dan hak akses akun SIAKAD."
        badge="PENGGUNA"
        badgeVariant="emerald"
        icon="users"
        maxWidth="max-w-md"
        closeAction="$set('isFormOpen', false)"
    >
        <form wire:submit.prevent="save" class="space-y-4 text-xs">
            <!-- Nama -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Nama Lengkap <span class="text-rose-600">*</span></label>
                <input wire:model="nama" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Ahmad Admin" required />
                @error('nama') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Username -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Username <span class="text-rose-600">*</span></label>
                <input wire:model="username" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="ahmad_adm" required />
                @error('username') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Email (Opsional)</label>
                <input wire:model="email" type="email" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="admin@sekolah.com" />
                @error('email') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">{{ $userId ? 'Ganti Password (Kosongkan jika tidak)' : 'Password Awal *' }}</label>
                <input wire:model="password" type="password" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="••••••" />
                @error('password') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Role -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Hak Akses / Role <span class="text-rose-600">*</span></label>
                <select wire:model="role_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required>
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->id }}">{{ ucfirst(str_replace('_', ' ', $r->nama)) }}</option>
                    @endforeach
                </select>
                @error('role_id') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Status -->
            @if ($userId)
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Status Keaktifan</label>
                    <select wire:model="status" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif / Blokir</option>
                    </select>
                    @error('status') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3">
                <x-button type="button" variant="secondary" size="md" wire:click="$set('isFormOpen', false)">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="save">
                    Simpan Pengguna
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
