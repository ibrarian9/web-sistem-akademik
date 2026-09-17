<div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
    <!-- Filter Toolbar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="w-full lg:max-w-md">
            <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari siswa, NIS, catatan, atau guru..." />
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Filter Kelas -->
            <select wire:model.live="filterKelasId" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Kelas</option>
                @foreach ($kelasList as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>

            <!-- Filter Aspek -->
            <select wire:model.live="filterAspek" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Aspek</option>
                @foreach ($aspekList as $asp)
                    <option value="{{ $asp }}">{{ $asp }}</option>
                @endforeach
            </select>

            <!-- Filter Hasil Kualitatif -->
            <select wire:model.live="filterHasil" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Capaian</option>
                <option value="BB">Belum Berkembang (BB)</option>
                <option value="MB">Mulai Berkembang (MB)</option>
                <option value="BSH">Sesuai Harapan (BSH)</option>
                <option value="BSB">Sangat Baik (BSB)</option>
            </select>

            <!-- Filter Periode -->
            <select wire:model.live="filterPeriode" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="semua">Semua Periode</option>
                <option value="tengah_semester">Tengah Semester</option>
                <option value="akhir_semester">Akhir Semester</option>
            </select>

            @if ($this->hasActiveFilters)
                <x-button type="button" variant="secondary" size="sm" icon="rotate-ccw" wire:click="resetFilters" title="Bersihkan Filter">
                    Reset
                </x-button>
            @endif
        </div>
    </div>

    <!-- Table of Observation Notes -->
    <x-table loadingTarget="search, filterKelasId, filterAspek, filterHasil, filterPeriode, page">
        <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
            <tr>
                <x-table.th class="w-28">Tanggal</x-table.th>
                <x-table.th class="min-w-[180px]">Peserta Didik</x-table.th>
                <x-table.th class="w-32 text-center">Periode</x-table.th>
                <x-table.th class="w-40">Aspek Pengamatan</x-table.th>
                <x-table.th class="w-36 text-center">Capaian</x-table.th>
                <x-table.th class="min-w-[220px]">Deskripsi Catatan Pengamatan</x-table.th>
                <x-table.th class="min-w-[180px]">Rekomendasi / Tindak Lanjut</x-table.th>
                <x-table.th class="w-36">Guru Pendamping</x-table.th>
                <x-table.th class="w-24 text-center">Aksi</x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-200 bg-white">
            @forelse ($catatans as $item)
                <tr class="hover:bg-stone-50/80 transition">
                    <td class="p-3.5 border-r border-stone-200">
                        <div class="font-bold text-xs text-stone-900">{{ $item->tanggal->translatedFormat('d M Y') }}</div>
                        <div class="text-[10px] text-stone-400 font-mono">{{ $item->created_at->format('H:i') }} WIB</div>
                    </td>
                    <td class="p-3.5 border-r border-stone-200">
                        <div class="font-extrabold text-xs text-stone-900">{{ $item->siswa->user->nama ?? 'Siswa' }}</div>
                        <div class="text-[10px] text-stone-500 font-medium">
                            NIS: {{ $item->siswa->nis ?: '-' }} | Kelas: {{ $item->siswa->kelas->nama_kelas ?? '-' }}
                        </div>
                    </td>
                    <td class="p-3.5 text-center border-r border-stone-200">
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $item->periode === 'tengah_semester' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                            {{ $item->periode_label }}
                        </span>
                    </td>
                    <td class="p-3.5 font-bold text-stone-800 text-xs border-r border-stone-200">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span>
                            <span>{{ $item->aspek }}</span>
                        </div>
                    </td>
                    <td class="p-3.5 text-center border-r border-stone-200">
                        @php
                            $badgeColor = match($item->hasil_perkembangan) {
                                'BB' => 'bg-rose-100 text-rose-800 border-rose-300',
                                'MB' => 'bg-amber-100 text-amber-800 border-amber-300',
                                'BSH' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                'BSB' => 'bg-blue-100 text-blue-800 border-blue-300',
                                default => 'bg-stone-100 text-stone-800 border-stone-300',
                            };
                        @endphp
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-black border {{ $badgeColor }}" title="{{ $item->hasil_perkembangan_label }}">
                            {{ $item->hasil_perkembangan }}
                        </span>
                        <div class="text-[10px] font-semibold text-stone-500 mt-0.5">{{ $item->hasil_perkembangan_label }}</div>
                    </td>
                    <td class="p-3.5 text-xs text-stone-700 leading-relaxed font-medium border-r border-stone-200">
                        {{ $item->catatan }}
                    </td>
                    <td class="p-3.5 text-xs text-stone-600 leading-relaxed border-r border-stone-200 font-medium">
                        {{ $item->rekomendasi ?: '-' }}
                    </td>
                    <td class="p-3.5 text-xs font-bold text-stone-800 border-r border-stone-200">
                        {{ $item->guru->user->nama ?? 'Guru Pendamping' }}
                    </td>
                    <td class="p-3.5 text-center">
                        @if (!auth()->user()->isSuperAdmin2())
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" wire:click="openEditModal({{ $item->id }})" class="p-1 text-stone-500 hover:text-emerald-600 rounded-lg hover:bg-emerald-50 transition" title="Edit Catatan">
                                    <x-lucide-pencil class="w-3.5 h-3.5" />
                                </button>
                                <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 text-stone-500 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Hapus Catatan">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        @else
                            <span class="text-[10px] text-stone-400 italic">Lihat Saja</span>
                        @endif
                    </td>
                </tr>
            @empty
                <x-table.empty colspan="9" title="Belum ada catatan pendampingan" message="Gunakan tombol Tambah Catatan Baru di atas untuk mencatat observasi perkembangan siswa." />
            @endforelse
        </tbody>
    </x-table>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $catatans->links() }}
    </div>
</div>
