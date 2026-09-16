<!-- Data Table of Students -->
<x-table loadingTarget="search, filterKelas, filterTingkat, filterKelasTahfidz, filterStatus, filterJenisKelamin, filterShadowTeacher, perPage, page">
    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
        <tr>
            <x-table.th class="w-32">NIS / NISN</x-table.th>
            <x-table.th class="min-w-[180px]">Nama Siswa</x-table.th>
            <x-table.th class="min-w-[140px]">Kelas Umum</x-table.th>
            <x-table.th class="min-w-[160px]">Kelas Tahfizh</x-table.th>
            <x-table.th class="min-w-[150px]">Wali Murid</x-table.th>
            <x-table.th align="center" class="w-28">Status</x-table.th>
            <x-table.th align="center" class="min-w-[180px]">Aksi</x-table.th>
        </tr>
    </thead>
    <tbody class="divide-y divide-stone-200 bg-white">
        @forelse ($siswas as $siswa)
            <tr class="hover:bg-stone-50 transition">
                <td class="p-3.5 font-semibold text-stone-600 border-r border-stone-200">
                    <div class="font-bold text-stone-900">{{ $siswa->nis }}</div>
                    <div class="text-[10px] text-stone-500">NISN: {{ $siswa->nisn ?: '-' }}</div>
                </td>
                <td class="p-3.5 border-r border-stone-200">
                    <div class="font-extrabold text-stone-900 text-xs">{{ strtoupper($siswa->user->nama ?? '-') }}</div>
                    <div class="text-[10px] text-stone-500 font-medium">User: {{ $siswa->user->username ?? '-' }}</div>
                    @if($siswa->shadowTeacher)
                        <div class="mt-1 flex items-center gap-1">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200" title="Guru Pendamping Khusus: {{ $siswa->shadowTeacher->user->nama ?? '-' }} ({{ ucfirst($siswa->shadowTeacher->jenis_guru ?? 'Pendamping') }})">
                                GPK: {{ $siswa->shadowTeacher->user->nama ?? '-' }} ({{ $siswa->shadowTeacher->jenis_guru === 'pendamping' ? 'Pendamping' : ($siswa->shadowTeacher->jenis_guru === 'tahfidz' ? 'Tahfizh' : 'Umum') }})
                            </span>
                        </div>
                    @endif
                </td>
                <td class="p-3.5 border-r border-stone-200">
                    @if($siswa->kelas)
                        <x-badge variant="emerald" size="xs">
                            {{ $siswa->kelas->nama_kelas }}
                        </x-badge>
                    @else
                        <span class="text-stone-400 italic text-[11px]">- Belum Set -</span>
                    @endif
                </td>
                <td class="p-3.5 border-r border-stone-200">
                    @if($siswa->kelasTahfidz)
                        <x-badge variant="amber" size="xs">
                            {{ $siswa->kelasTahfidz->nama_kelas }}
                        </x-badge>
                    @else
                        <span class="text-stone-400 italic text-[11px]">- Belum Set -</span>
                    @endif
                </td>
                <td class="p-3.5 border-r border-stone-200">
                    <div class="text-stone-900 font-bold">{{ $siswa->nama_wali ?: '-' }}</div>
                    <div class="text-[10px] text-stone-500 font-medium">{{ $siswa->no_hp_wali ?: '-' }}</div>
                </td>
                <td class="p-3.5 text-center border-r border-stone-200">
                    <x-status-badge :status="$siswa->status" />
                </td>
                <td class="p-3.5 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        <x-button type="button" variant="outline" size="xs" icon="eye" wire:click.prevent="openDetail({{ $siswa->id }})">
                            Detail
                        </x-button>
                        @if(!auth()->user()->isSuperAdmin2())
                        <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click.prevent="openEdit({{ $siswa->id }})">
                            Edit
                        </x-button>
                        <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click.prevent="delete({{ $siswa->id }})" data-confirm="Apakah Anda yakin ingin menghapus data siswa ini?">
                            Hapus
                        </x-button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-12 text-center text-stone-400">
                    @if ($this->activeFilterCount > 0)
                        <div class="space-y-3 flex flex-col items-center justify-center">
                            <x-table.empty 
                                title="Tidak ada data siswa yang cocok dengan filter" 
                                subtitle="Kombinasi pencarian dan filter yang Anda gunakan tidak menemukan data siswa." 
                            />
                            <button type="button" wire:click="resetFilters" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-stone-100 hover:bg-stone-200 text-stone-700 transition shadow-2xs cursor-pointer">
                                <x-lucide-rotate-ccw class="w-3.5 h-3.5 text-stone-500" />
                                <span>Reset Semua Filter ({{ $this->activeFilterCount }})</span>
                            </button>
                        </div>
                    @else
                        <x-table.empty title="Tidak ada data siswa ditemukan" subtitle="Pastikan kata kunci pencarian benar atau tambahkan data siswa baru." />
                    @endif
                </td>
            </tr>
        @endforelse
    </tbody>
</x-table>

<!-- Pagination -->
<div class="pt-2">
    {{ $siswas->links() }}
</div>
