<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Rombongan Belajar (Kelas)"
        :steps="[
            ['title' => 'Buat Rombel Kelas', 'desc' => 'Klik Tambah Kelas untuk membuat ruang kelas baru beserta penetapan tingkatnya (7, 8, 9).'],
            ['title' => 'Penetapan Wali Kelas', 'desc' => 'Pilih Wali Kelas Umum (penanggung jawab kelas) dan Guru Tahfidz pendamping kelompok mengaji.'],
            ['title' => 'Integrasi Nilai Rapor', 'desc' => 'Wali Kelas Umum bertanggung jawab menyetujui cetak rapor siswa di akhir semester.']
        ]"
        notes="Satu guru dapat menjadi Wali Kelas Umum sekaligus pendamping Tahfidz sesuai penugasan dari Tata Usaha."
    />

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Manajemen Kelas</h2>
            <p class="text-xs text-slate-500">Buat kelas akademik baru dan tentukan Wali Kelas Umum &amp; Tahfidz.</p>
        </div>
        <button wire:click="openCreate" class="py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold tracking-wide transition duration-200 flex items-center gap-1.5 shadow-lg shadow-indigo-600/10">
            <x-lucide-plus class="w-4 h-4" />
            <span>Tambah Kelas</span>
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
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama kelas..."
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
                <th class="px-6 py-3.5">Nama Kelas</th>
                <th class="px-6 py-3.5">Tingkat</th>
                <th class="px-6 py-3.5">Guru Umum (Wali Kelas)</th>
                <th class="px-6 py-3.5">Guru Tahfidz</th>
                <th class="px-6 py-3.5">Jumlah Siswa</th>
                <th class="px-6 py-3.5 text-right">Aksi</th>
            </x-slot:thead>
            <x-slot:tbody>
                @forelse ($kelases as $kelas)
                    <tr class="hover:bg-slate-905 transition-colors">
                        <td class="px-6 py-4 font-semibold text-white">Kelas {{ $kelas->nama_kelas }}</td>
                        <td class="px-6 py-4 text-slate-300 font-medium">Tingkat {{ $kelas->tingkat }}</td>
                        <td class="px-6 py-4 text-indigo-400 font-semibold">
                            {{ $kelas->guruUmum->user->nama ?? 'Belum Ditentukan' }}
                        </td>
                        <td class="px-6 py-4 text-emerald-450 font-semibold">
                            {{ $kelas->guruTahfidz->user->nama ?? 'Belum Ditentukan' }}
                        </td>
                        <td class="px-6 py-4 text-slate-300 font-medium">
                            {{ $kelas->siswas()->count() }} Siswa
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex gap-2">
                                <button wire:click="openEdit({{ $kelas->id }})" class="p-1.5 bg-slate-800 border border-slate-750 text-indigo-400 hover:text-white hover:bg-indigo-600 rounded-lg transition duration-200">
                                    <x-lucide-edit class="w-3.5 h-3.5" />
                                </button>
                                <button onclick="confirm('Apakah Anda yakin ingin menghapus kelas ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $kelas->id }})" class="p-1.5 bg-slate-800 border border-slate-750 text-rose-400 hover:text-white hover:bg-rose-600 rounded-lg transition duration-200">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 font-medium">
                            Tidak ada data kelas ditemukan
                        </td>
                    </tr>
                @endforelse
            </x-slot:tbody>
        </x-data-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $kelases->links() }}
        </div>
    </div>

    <!-- Form Modal -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-850 pb-4">
                    <h3 class="text-base font-bold text-white tracking-wide">{{ $kelasId ? 'Edit Data Kelas' : 'Tambah Kelas Baru' }}</h3>
                    <button wire:click="$set('isFormOpen', false)" class="p-1.5 bg-slate-850 hover:bg-slate-800 rounded-lg text-slate-400 hover:text-white transition duration-200">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <!-- Nama Kelas -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Kelas</label>
                        <input wire:model="nama_kelas" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="7A" />
                        @error('nama_kelas') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tingkat -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tingkat</label>
                        <select wire:model="tingkat" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                            <option value="7">7 (Tujuh)</option>
                            <option value="8">8 (Delapan)</option>
                            <option value="9">9 (Sembilan)</option>
                        </select>
                        @error('tingkat') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Wali Kelas Umum -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Wali Kelas (Guru Umum)</label>
                        <select wire:model="guru_umum_id" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                            <option value="">Pilih Guru Umum (Opsional)</option>
                            @foreach ($gurusUmum as $g)
                                <option value="{{ $g->id }}">{{ $g->user->nama }}</option>
                            @endforeach
                        </select>
                        @error('guru_umum_id') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Guru Tahfidz -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Guru Tahfidz</label>
                        <select wire:model="guru_tahfidz_id" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                            <option value="">Pilih Guru Tahfidz (Opsional)</option>
                            @foreach ($gurusTahfidz as $g)
                                <option value="{{ $g->id }}">{{ $g->user->nama }}</option>
                            @endforeach
                        </select>
                        @error('guru_tahfidz_id') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-3 border-t border-slate-850 pt-4 mt-6">
                        <button type="button" wire:click="$set('isFormOpen', false)" class="py-2 px-4 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded-xl text-xs font-bold transition duration-200">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition duration-200">
                            Simpan Kelas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
