<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Manajemen Pengguna</h2>
            <p class="text-xs text-slate-500">Kelola akun administrator, staf keuangan, guru, dan hak akses login sistem.</p>
        </div>
        <button wire:click="openCreate" class="py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold tracking-wide transition duration-200 flex items-center gap-1.5 shadow-lg shadow-indigo-600/10">
            <x-lucide-plus class="w-4 h-4" />
            <span>Tambah Pengguna</span>
        </button>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Table Section -->
    <div class="space-y-4">
        <!-- Filters -->
        <div class="flex items-center justify-between gap-4">
            <div class="relative max-w-md w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                    <x-lucide-search class="w-4 h-4" />
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, username, atau email..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition duration-200 text-xs" />
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">Tampilkan</span>
                <select wire:model.live="perPage" class="bg-slate-900 border border-slate-800 rounded-xl text-white text-xs px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <x-data-table>
            <x-slot:thead>
                <th class="px-6 py-3.5">Nama Lengkap</th>
                <th class="px-6 py-3.5">Username</th>
                <th class="px-6 py-3.5">Role / Hak Akses</th>
                <th class="px-6 py-3.5">Status Akun</th>
                <th class="px-6 py-3.5 text-right">Aksi</th>
            </x-slot:thead>
            <x-slot:tbody>
                @forelse ($users as $u)
                    <tr class="hover:bg-slate-905 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-white">{{ $u->nama }}</div>
                            <div class="text-xs text-slate-500">{{ $u->email ?: 'Tidak ada email' }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-350 font-semibold">@ {{ $u->username }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 uppercase tracking-wide">
                                {{ str_replace('_', ' ', $u->role->nama ?? '-') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$u->status" />
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex gap-2">
                                <button wire:click="openEdit({{ $u->id }})" class="p-1.5 bg-slate-800 border border-slate-750 text-indigo-400 hover:text-white hover:bg-indigo-600 rounded-lg transition duration-200">
                                    <x-lucide-edit class="w-3.5 h-3.5" />
                                </button>
                                @if ($u->id !== auth()->id())
                                    <button onclick="confirm('Apakah Anda yakin ingin menghapus pengguna ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $u->id }})" class="p-1.5 bg-slate-800 border border-slate-750 text-rose-400 hover:text-white hover:bg-rose-600 rounded-lg transition duration-200">
                                        <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500 font-medium">
                            Tidak ada data pengguna ditemukan
                        </td>
                    </tr>
                @endforelse
            </x-slot:tbody>
        </x-data-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Form Modal -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-850 pb-4">
                    <h3 class="text-base font-bold text-white tracking-wide">{{ $userId ? 'Edit Akun Pengguna' : 'Tambah Pengguna Baru' }}</h3>
                    <button wire:click="$set('isFormOpen', false)" class="p-1.5 bg-slate-850 hover:bg-slate-800 rounded-lg text-slate-400 hover:text-white transition duration-200">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <!-- Nama -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                        <input wire:model="nama" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Ahmad Admin" />
                        @error('nama') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Username -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Username</label>
                        <input wire:model="username" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="ahmad_adm" />
                        @error('username') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Email (Opsional)</label>
                        <input wire:model="email" type="email" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="admin@sekolah.com" />
                        @error('email') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $userId ? 'Ganti Password (Kosongkan jika tidak)' : 'Password Awal' }}</label>
                        <input wire:model="password" type="password" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="••••••" />
                        @error('password') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Role -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hak Akses / Role</label>
                        <select wire:model="role_id" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                            <option value="">Pilih Role</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->id }}">{{ ucfirst(str_replace('_', ' ', $r->nama)) }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status -->
                    @if ($userId)
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Keaktifan</label>
                            <select wire:model="status" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif / Blokir</option>
                            </select>
                            @error('status') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-3 border-t border-slate-850 pt-4 mt-6">
                        <button type="button" wire:click="$set('isFormOpen', false)" class="py-2 px-4 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded-xl text-xs font-bold transition duration-200">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition duration-200">
                            Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
