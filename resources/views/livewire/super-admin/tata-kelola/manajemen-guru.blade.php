<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Data Guru & Tenaga Pendidik"
        :steps="[
            ['title' => 'Input Profil & Login', 'desc' => 'Klik Tambah Guru untuk mendaftarkan biodata lengkap, NIP, serta membuatkan username & password login.'],
            ['title' => 'Status Kepegawaian', 'desc' => 'Tentukan status kepegawaian (PNS, GTT, atau Honorer) agar terintegrasi dengan penggajian & administrasi.'],
            ['title' => 'Manajemen Status Kerja', 'desc' => 'Ubah status mengajar menjadi Nonaktif jika guru yang bersangkutan sedang mutasi, berhenti, atau cuti.']
        ]"
        notes="NIP bersifat opsional tetapi sangat disarankan diisi lengkap demi keperluan pencetakan dokumen resmi/rapor."
    />

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Manajemen Guru &amp; Pegawai</h2>
            <p class="text-xs text-slate-500">Kelola data kepegawaian, status kerja, dan kredensial login guru.</p>
        </div>
        <button wire:click="openCreate" class="py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold tracking-wide transition duration-200 flex items-center gap-1.5 shadow-lg shadow-indigo-600/10">
            <x-lucide-plus class="w-4 h-4" />
            <span>Tambah Guru</span>
        </button>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Table Section -->
    <div class="space-y-4">
        <!-- Filters -->
        <div class="flex items-center justify-between gap-4">
            <div class="relative max-w-md w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                    <x-lucide-search class="w-4 h-4" />
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari NIP, nama, atau username..."
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
                <th class="px-6 py-3.5">NIP</th>
                <th class="px-6 py-3.5">Nama Guru</th>
                <th class="px-6 py-3.5">Status Pegawai</th>
                <th class="px-6 py-3.5">No. HP</th>
                <th class="px-6 py-3.5">Status Kerja</th>
                <th class="px-6 py-3.5 text-right">Aksi</th>
            </x-slot:thead>
            <x-slot:tbody>
                @forelse ($gurus as $guru)
                    <tr class="hover:bg-slate-905 transition-colors">
                        <td class="px-6 py-4 font-semibold text-white">{{ $guru->nip ?: '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-white">{{ $guru->user->nama ?? '-' }}</div>
                            <div class="text-xs text-slate-500">Username: {{ $guru->user->username ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-300 font-medium uppercase">{{ $guru->status_kepegawaian }}</td>
                        <td class="px-6 py-4 text-slate-300">{{ $guru->user->no_hp ?: '-' }}</td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$guru->status_aktif ? 'aktif' : 'nonaktif'" />
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center justify-end gap-2">
                                <button wire:click="openEdit({{ $guru->id }})" class="px-2.5 py-1.5 bg-amber-500/10 hover:bg-amber-500 border border-amber-500/30 hover:border-amber-500 text-amber-400 hover:text-slate-950 rounded-xl text-[11px] font-bold transition-all duration-150 inline-flex items-center gap-1.5 shadow-sm" title="Edit Guru">
                                    <x-lucide-edit class="w-3.5 h-3.5" />
                                    <span>Edit</span>
                                </button>
                                <button onclick="confirm('Apakah Anda yakin ingin menghapus data guru ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $guru->id }})" class="px-2.5 py-1.5 bg-rose-500/10 hover:bg-rose-600 border border-rose-500/30 hover:border-rose-600 text-rose-400 hover:text-white rounded-xl text-[11px] font-bold transition-all duration-150 inline-flex items-center gap-1.5 shadow-sm" title="Hapus Guru">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 font-medium">
                            Tidak ada data guru ditemukan
                        </td>
                    </tr>
                @endforelse
            </x-slot:tbody>
        </x-data-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $gurus->links() }}
        </div>
    </div>

    <!-- Form Modal -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div class="w-full max-w-2xl bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl p-6 overflow-y-auto max-h-[90vh] custom-scrollbar space-y-6">
                <div class="flex items-center justify-between border-b border-slate-850 pb-4">
                    <h3 class="text-base font-bold text-white tracking-wide">{{ $guruId ? 'Edit Data Guru' : 'Tambah Guru Baru' }}</h3>
                    <button wire:click="$set('isFormOpen', false)" class="p-1.5 bg-slate-850 hover:bg-slate-800 rounded-lg text-slate-400 hover:text-white transition duration-200">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                            <input wire:model="nama" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Ahmad Budi, S.Pd" />
                            @error('nama') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Username -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Username Login</label>
                            <input wire:model="username" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="budi_guru" />
                            @error('username') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Email (Opsional)</label>
                            <input wire:model="email" type="email" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="budi@sekolah.com" />
                            @error('email') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $guruId ? 'Ganti Password (Kosongkan jika tidak)' : 'Password Awal' }}</label>
                            <input wire:model="password" type="password" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="••••••" />
                            @error('password') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- NIP -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">NIP (Opsional)</label>
                            <input wire:model="nip" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="1987..." />
                            @error('nip') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status Kepegawaian -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Kepegawaian</label>
                            <select wire:model="status_kepegawaian" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                                <option value="pns">PNS</option>
                                <option value="gtt">GTT (Guru Tetap Yayasan)</option>
                                <option value="honorer">Honorer</option>
                            </select>
                            @error('status_kepegawaian') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tempat Lahir -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tempat Lahir</label>
                            <input wire:model="tempat_lahir" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Yogyakarta" />
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Lahir</label>
                            <input wire:model="tanggal_lahir" type="date" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
                        </div>

                        <!-- No HP -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">No. HP Aktif</label>
                            <input wire:model="no_hp" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="0812..." />
                        </div>

                        <!-- Tanggal Masuk -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mulai Tugas (Masuk)</label>
                            <input wire:model="tanggal_masuk" type="date" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
                            @error('tanggal_masuk') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status Aktif -->
                        @if ($guruId)
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Mengajar</label>
                                <select wire:model="status_aktif" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                                    <option value="1">Aktif Mengajar</option>
                                    <option value="0">Nonaktif / Cuti</option>
                                </select>
                            </div>
                        @endif
                    </div>

                    <!-- Alamat -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Alamat Lengkap</label>
                        <textarea wire:model="alamat" rows="3" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Alamat lengkap rumah..."></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-3 border-t border-slate-850 pt-4">
                        <button type="button" wire:click="$set('isFormOpen', false)" class="py-2.5 px-4 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded-xl text-xs font-bold transition duration-200">
                            Batal
                        </button>
                        <button type="submit" class="py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition duration-200">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
