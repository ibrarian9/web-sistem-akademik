<!-- TABLE VIEW DAFTAR SEMUA JADWAL -->
<div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
    <!-- Filters -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2 w-full sm:max-w-2xl">
            <!-- Search bar -->
            <div class="flex-1 min-w-[200px]">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari mapel atau nama guru..." />
            </div>
            
            <!-- Filter Per Kelas -->
            <select wire:model.live="filterKelasId" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Kelas</option>
                @foreach ($kelases as $k)
                    <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                @endforeach
            </select>

            <!-- Hari selector -->
            <select wire:model.live="filterHari" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Hari</option>
                <option value="senin">Senin</option>
                <option value="selasa">Selasa</option>
                <option value="rabu">Rabu</option>
                <option value="kamis">Kamis</option>
                <option value="jumat">Jumat</option>
                <option value="sabtu">Sabtu</option>
            </select>
        </div>
        
        <div class="flex items-center gap-2 shrink-0">
            <span class="text-xs font-bold text-stone-600">Tampilkan:</span>
            <select wire:model.live="perPage" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs px-3 py-2 font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="15">15 Baris</option>
                <option value="30">30 Baris</option>
                <option value="50">50 Baris</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <x-table loadingTarget="filterKelasId, filterHari, search, perPage">
        <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
            <tr>
                <x-table.th class="w-32">Hari</x-table.th>
                <x-table.th class="w-40">Jam Pelajaran</x-table.th>
                <x-table.th class="w-32">Kelas</x-table.th>
                <x-table.th class="min-w-[180px]">Mata Pelajaran</x-table.th>
                <x-table.th class="min-w-[200px]">Guru Pengampu</x-table.th>
                @if (!auth()->user()?->isSuperAdmin2())
                    <x-table.th align="center" class="w-36">Aksi</x-table.th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-200 bg-white">
            @forelse ($jadwals as $jadwal)
                <tr class="hover:bg-stone-50 transition">
                    <td class="p-3.5 font-bold text-stone-900 border-r border-stone-200 capitalize">{{ $jadwal->hari }}</td>
                    <td class="p-3.5 border-r border-stone-200">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-900 border border-emerald-300 text-xs font-extrabold">
                            <x-lucide-clock class="w-3.5 h-3.5" />
                            {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                        </span>
                    </td>
                    <td class="p-3.5 border-r border-stone-200 font-extrabold text-stone-900">Kelas {{ $jadwal->guruMapelKelas->kelas->nama_kelas ?? '-' }}</td>
                    <td class="p-3.5 border-r border-stone-200 font-extrabold text-stone-900">{{ $jadwal->guruMapelKelas->mapel->nama_mapel ?? '-' }}</td>
                    <td class="p-3.5 border-r border-stone-200 font-bold text-stone-700">{{ $jadwal->guruMapelKelas->guru->user->nama ?? '-' }}</td>
                    @if (!auth()->user()?->isSuperAdmin2())
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click="openEdit({{ $jadwal->id }})">
                                    Edit
                                </x-button>
                                <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="delete({{ $jadwal->id }})" data-confirm="Apakah Anda yakin ingin menghapus jadwal ini?">
                                    Hapus
                                </x-button>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ auth()->user()?->isSuperAdmin2() ? 5 : 6 }}" class="py-12 text-center text-stone-400">
                        <x-table.empty title="Belum ada jadwal pelajaran ditemukan" subtitle="Pilih kelas atau tambahkan slot jadwal baru di atas." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $jadwals->links() }}
    </div>
</div>
