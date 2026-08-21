<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Manajemen Mata Pelajaran" 
        subtitle="Kelola daftar kurikulum mata pelajaran umum, keagamaan, tahfizh, dan muatan lokal."
        badge="KURIKULUM &amp; MAPEL"
        badgeVariant="emerald"
        icon="book-open"
    >
        <x-slot:actions>
            <x-button type="button" variant="primary" size="md" icon="plus" wire:click="openCreate">
                Tambah Mapel
            </x-button>
        </x-slot:actions>
    </x-page-header>

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

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Filters -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari kode atau nama mapel..." />
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

        <!-- Table -->
        <x-table loadingTarget="search, perPage">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-36">Kode Mapel</x-table.th>
                    <x-table.th class="min-w-[200px]">Nama Mata Pelajaran</x-table.th>
                    <x-table.th class="w-48">Kelompok Kurikulum</x-table.th>
                    <x-table.th align="center" class="w-36">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($mapels as $mapel)
                    @php
                        $kelompokVariant = match($mapel->kelompok) {
                            'tahfidz' => 'emerald',
                            'keagamaan' => 'amber',
                            'umum' => 'blue',
                            default => 'stone',
                        };
                    @endphp
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3.5 font-mono font-bold text-stone-800 border-r border-stone-200 text-xs">
                            {{ $mapel->kode_mapel }}
                        </td>
                        <td class="p-3.5 font-extrabold text-stone-900 border-r border-stone-200 text-xs">
                            {{ $mapel->nama_mapel }}
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <x-badge :variant="$kelompokVariant" size="xs">
                                {{ ucfirst($mapel->kelompok) }}
                            </x-badge>
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click="openEdit({{ $mapel->id }})">
                                    Edit
                                </x-button>
                                <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="delete({{ $mapel->id }})" data-confirm="Apakah Anda yakin ingin menghapus mata pelajaran ini?">
                                    Hapus
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-stone-400">
                            <x-table.empty title="Tidak ada data mata pelajaran ditemukan" subtitle="Gunakan tombol Tambah Mapel di atas untuk membuat mata pelajaran kurikulum." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $mapels->links() }}
        </div>
    </div>

    <!-- Form Floating Modal -->
    <x-floating-card 
        :show="$isFormOpen ? true : false"
        :title="$mapelId ? 'Edit Mata Pelajaran' : 'Tambah Mapel Baru'"
        subtitle="Lengkapi kode, nama mapel, dan kelompok kurikulum."
        badge="MAPEL"
        badgeVariant="emerald"
        icon="book-open"
        maxWidth="max-w-md"
        closeAction="$set('isFormOpen', false)"
    >
        <form wire:submit.prevent="save" class="space-y-4 text-xs">
            <!-- Kode Mapel -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Kode Mapel <span class="text-rose-600">*</span></label>
                <input wire:model="kode_mapel" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="PAI-7" required />
                @error('kode_mapel') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Nama Mapel -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Nama Mata Pelajaran <span class="text-rose-600">*</span></label>
                <input wire:model="nama_mapel" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Pendidikan Agama Islam" required />
                @error('nama_mapel') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Kelompok -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Kelompok Kurikulum <span class="text-rose-600">*</span></label>
                <select wire:model="kelompok" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required>
                    <option value="umum">Umum</option>
                    <option value="keagamaan">Keagamaan</option>
                    <option value="tahfidz">Tahfizh</option>
                    <option value="mulok">Muatan Lokal (Mulok)</option>
                </select>
                @error('kelompok') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3">
                <x-button type="button" variant="secondary" size="md" wire:click="$set('isFormOpen', false)">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="save">
                    Simpan Mapel
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
