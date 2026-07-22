<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Data Siswa Aktif"
        :steps="[
            ['title' => 'Tambah Siswa', 'desc' => 'Klik Tambah Siswa untuk mendaftarkan data NIS, NISN, biodata, serta wali murid.'],
            ['title' => 'Penempatan Kelas', 'desc' => 'Tentukan kelas siswa pada form pendaftaran atau ubah jika ada perubahan ruang kelas.'],
            ['title' => 'Perubahan Status', 'desc' => 'Ubah status keaktifan menjadi Lulus, Pindah, atau Keluar saat terjadi pembaruan status pendidikan.']
        ]"
        notes="Username & password otomatis dibuatkan untuk akses portal siswa dan wali murid."
    />

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Manajemen Siswa</h2>
            <p class="text-xs text-slate-500">Kelola informasi pendaftaran dan penempatan kelas siswa.</p>
        </div>
        <button wire:click="openCreate" class="py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold tracking-wide transition duration-200 flex items-center gap-1.5 shadow-lg shadow-indigo-600/10">
            <x-lucide-plus class="w-4 h-4" />
            <span>Tambah Siswa</span>
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
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari NIS, nama, atau username..."
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
                <th class="px-6 py-3.5">NIS</th>
                <th class="px-6 py-3.5">Nama Siswa</th>
                <th class="px-6 py-3.5">Kelas</th>
                <th class="px-6 py-3.5">Wali Murid</th>
                <th class="px-6 py-3.5">Status</th>
                <th class="px-6 py-3.5 text-right">Aksi</th>
            </x-slot:thead>
            <x-slot:tbody>
                @forelse ($siswas as $siswa)
                    <tr class="hover:bg-slate-905 transition-colors">
                        <td class="px-6 py-4 font-semibold text-white">{{ $siswa->nis }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-white">{{ $siswa->user->nama ?? '-' }}</div>
                            <div class="text-xs text-slate-500">NISN: {{ $siswa->nisn ?: '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-300 font-medium">Kelas {{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="text-slate-300 font-medium">{{ $siswa->nama_wali ?: '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $siswa->no_hp_wali ?: '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$siswa->status" />
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex gap-2">
                                <button wire:click="openEdit({{ $siswa->id }})" class="p-1.5 bg-slate-800 border border-slate-750 text-indigo-400 hover:text-white hover:bg-indigo-600 rounded-lg transition duration-200">
                                    <x-lucide-edit class="w-3.5 h-3.5" />
                                </button>
                                <button onclick="confirm('Apakah Anda yakin ingin menghapus data siswa ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $siswa->id }})" class="p-1.5 bg-slate-800 border border-slate-750 text-rose-400 hover:text-white hover:bg-rose-600 rounded-lg transition duration-200">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 font-medium">
                            Tidak ada data siswa ditemukan
                        </td>
                    </tr>
                @endforelse
            </x-slot:tbody>
        </x-data-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $siswas->links() }}
        </div>
    </div>

    <!-- Form Modal -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div class="w-full max-w-2xl bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl p-6 overflow-y-auto max-h-[90vh] custom-scrollbar space-y-6">
                <div class="flex items-center justify-between border-b border-slate-850 pb-4">
                    <h3 class="text-base font-bold text-white tracking-wide">{{ $siswaId ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}</h3>
                    <button wire:click="$set('isFormOpen', false)" class="p-1.5 bg-slate-850 hover:bg-slate-800 rounded-lg text-slate-400 hover:text-white transition duration-200">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                            <input wire:model="nama" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Ahmad Fauzi" />
                            @error('nama') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Username -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Username Login</label>
                            <input wire:model="username" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="fauzi1001" />
                            @error('username') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Email (Opsional)</label>
                            <input wire:model="email" type="email" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="siswa@mail.com" />
                            @error('email') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $siswaId ? 'Ganti Password (Kosongkan jika tidak)' : 'Password Awal' }}</label>
                            <input wire:model="password" type="password" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="••••••" />
                            @error('password') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- NIS -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">NIS</label>
                            <input wire:model="nis" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="1001" />
                            @error('nis') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- NISN -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">NISN (Opsional)</label>
                            <input wire:model="nisn" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="009812345" />
                            @error('nisn') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jenis Kelamin</label>
                            <select wire:model="jenis_kelamin" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Kelas -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Penempatan Kelas</label>
                            <select wire:model="kelas_id" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                                <option value="">Pilih Kelas</option>
                                @foreach ($kelases as $kls)
                                    <option value="{{ $kls->id }}">Kelas {{ $kls->nama_kelas }}</option>
                                @endforeach
                            </select>
                            @error('kelas_id') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
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

                        <!-- Nama Wali -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Wali Murid</label>
                            <input wire:model="nama_wali" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Nama wali murid" />
                        </div>

                        <!-- No HP Wali -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">No HP Wali Murid</label>
                            <input wire:model="no_hp_wali" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="0857..." />
                        </div>

                        <!-- Tanggal Masuk -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Masuk</label>
                            <input wire:model="tanggal_masuk" type="date" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
                            @error('tanggal_masuk') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status -->
                        @if ($siswaId)
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Keaktifan</label>
                                <select wire:model="status" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                                    <option value="aktif">Aktif</option>
                                    <option value="lulus">Lulus</option>
                                    <option value="pindah">Pindah</option>
                                    <option value="keluar">Keluar</option>
                                </select>
                            </div>
                        @endif
                    </div>

                    <!-- Alamat -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Alamat Rumah</label>
                        <textarea wire:model="alamat" rows="3" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Alamat rumah lengkap..."></textarea>
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
