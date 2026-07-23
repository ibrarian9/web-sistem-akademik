<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Mata Pelajaran Kurikulum"
        :steps="[
            ['title' => 'Daftarkan Mapel', 'desc' => 'Klik Tambah Mapel untuk membuat kode dan nama mata pelajaran baru.'],
            ['title' => 'Pengelompokan Kurikulum', 'desc' => 'Tentukan kelompok mata pelajaran (Umum, Keagamaan, Tahfidz, atau Mulok) agar terkelompokkan dengan tepat di Rapor.'],
            ['title' => 'Komponen Nilai Terkait', 'desc' => 'Mata pelajaran dengan kelompok Tahfidz akan otomatis menggunakan komponen nilai khusus Tahfidz saat penginputan nilai.']
        ]"
        notes="Kode mapel harus unik dan disarankan menggunakan singkatan standar (contoh: PAI-7, MTK-8, THF-1)."
    />

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Manajemen Mata Pelajaran</h2>
            <p class="text-xs text-slate-500">Kelola daftar kurikulum mata pelajaran umum, keagamaan, tahfidz, dan muatan lokal.</p>
        </div>
        <button wire:click="openCreate" class="py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold tracking-wide transition duration-200 flex items-center gap-1.5 shadow-lg shadow-indigo-600/10">
            <x-lucide-plus class="w-4 h-4" />
            <span>Tambah Mapel</span>
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
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kode atau nama mapel..."
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
                <th class="px-6 py-3.5">Kode Mapel</th>
                <th class="px-6 py-3.5">Nama Mata Pelajaran</th>
                <th class="px-6 py-3.5">Kelompok Kurikulum</th>
                <th class="px-6 py-3.5 text-right">Aksi</th>
            </x-slot:thead>
            <x-slot:tbody>
                @forelse ($mapels as $mapel)
                    <tr class="hover:bg-slate-905 transition-colors">
                        <td class="px-6 py-4 font-semibold text-indigo-400">{{ $mapel->kode_mapel }}</td>
                        <td class="px-6 py-4 font-medium text-white">{{ $mapel->nama_mapel }}</td>
                        <td class="px-6 py-4 capitalize font-medium">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $mapel->kelompok === 'tahfidz' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                {{ $mapel->kelompok === 'keagamaan' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                {{ $mapel->kelompok === 'umum' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : '' }}
                                {{ $mapel->kelompok === 'mulok' ? 'bg-slate-500/10 text-slate-400 border border-slate-500/20' : '' }}
                            ">
                                {{ $mapel->kelompok }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center justify-end gap-2">
                                <button wire:click="openEdit({{ $mapel->id }})" class="px-2.5 py-1.5 bg-amber-500/10 hover:bg-amber-500 border border-amber-500/30 hover:border-amber-500 text-amber-400 hover:text-slate-950 rounded-xl text-[11px] font-bold transition-all duration-150 inline-flex items-center gap-1.5 shadow-sm" title="Edit Mapel">
                                    <x-lucide-edit class="w-3.5 h-3.5" />
                                    <span>Edit</span>
                                </button>
                                <button onclick="confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $mapel->id }})" class="px-2.5 py-1.5 bg-rose-500/10 hover:bg-rose-600 border border-rose-500/30 hover:border-rose-600 text-rose-400 hover:text-white rounded-xl text-[11px] font-bold transition-all duration-150 inline-flex items-center gap-1.5 shadow-sm" title="Hapus Mapel">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500 font-medium">
                            Tidak ada data mata pelajaran ditemukan
                        </td>
                    </tr>
                @endforelse
            </x-slot:tbody>
        </x-data-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $mapels->links() }}
        </div>
    </div>

    <!-- Form Modal -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-850 pb-4">
                    <h3 class="text-base font-bold text-white tracking-wide">{{ $mapelId ? 'Edit Mata Pelajaran' : 'Tambah Mapel Baru' }}</h3>
                    <button wire:click="$set('isFormOpen', false)" class="p-1.5 bg-slate-850 hover:bg-slate-800 rounded-lg text-slate-400 hover:text-white transition duration-200">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <!-- Kode Mapel -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kode Mapel</label>
                        <input wire:model="kode_mapel" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="PAI-7" />
                        @error('kode_mapel') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nama Mapel -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Mata Pelajaran</label>
                        <input wire:model="nama_mapel" type="text" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Pendidikan Agama Islam" />
                        @error('nama_mapel') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kelompok -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kelompok Kurikulum</label>
                        <select wire:model="kelompok" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                            <option value="umum">Umum</option>
                            <option value="keagamaan">Keagamaan</option>
                            <option value="tahfidz">Tahfizh</option>
                            <option value="mulok">Muatan Lokal (Mulok)</option>
                        </select>
                        @error('kelompok') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-3 border-t border-slate-850 pt-4 mt-6">
                        <button type="button" wire:click="$set('isFormOpen', false)" class="py-2 px-4 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded-xl text-xs font-bold transition duration-200">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition duration-200">
                            Simpan Mapel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
