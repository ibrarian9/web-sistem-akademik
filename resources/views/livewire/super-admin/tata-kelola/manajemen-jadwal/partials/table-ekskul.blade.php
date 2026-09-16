<!-- JADWAL EKSTRAKURIKULER (ADMIN) -->
<div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-stone-200">
        <div>
            <h3 class="font-extrabold text-stone-900 text-sm">Jadwal & Ruang Kegiatan Ekstrakurikuler</h3>
            <p class="text-xs text-stone-500 mt-0.5">Admin mengelola hari, jam mulai, jam selesai, dan tempat/ruangan untuk setiap ekstrakurikuler.</p>
        </div>
    </div>

    <x-table>
        <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
            <tr>
                <x-table.th class="min-w-[200px]">Ekstrakurikuler</x-table.th>
                <x-table.th class="min-w-[220px]">Guru Pembina (Staf TU)</x-table.th>
                <x-table.th class="w-32 text-center">Anggota</x-table.th>
                <x-table.th class="min-w-[180px]">Hari & Waktu Pelaksanaan</x-table.th>
                <x-table.th class="min-w-[180px]">Tempat / Ruangan</x-table.th>
                <x-table.th align="center" class="w-36">Aksi (Admin)</x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-200 bg-white">
            @forelse ($ekskuls as $ek)
                <tr class="hover:bg-stone-50 transition">
                    <td class="p-3.5 border-r border-stone-200">
                        <div class="font-extrabold text-stone-900 text-xs flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-black">
                                <x-lucide-award class="w-4 h-4" />
                            </span>
                            <span>{{ $ek->nama }}</span>
                        </div>
                    </td>
                    <td class="p-3.5 border-r border-stone-200">
                        <div class="font-bold text-stone-900 text-xs">{{ $ek->pembina->user->nama ?? 'Belum Ditugaskan' }}</div>
                        <div class="text-[10px] text-stone-500">NIP/NIY: {{ $ek->pembina->nip ?? ($ek->pembina->niy ?? '-') }}</div>
                    </td>
                    <td class="p-3.5 border-r border-stone-200 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-stone-100 text-stone-800">
                            {{ $ek->siswa_ekskul_count }} / {{ $ek->kuota }} Santri
                        </span>
                    </td>
                    <td class="p-3.5 border-r border-stone-200">
                        @if ($ek->hari)
                            <div class="font-extrabold text-stone-900 text-xs flex items-center gap-1.5">
                                <x-lucide-clock class="w-3.5 h-3.5 text-emerald-700" />
                                <span>{{ ucfirst($ek->hari) }}, {{ $ek->jam_mulai }} - {{ $ek->jam_selesai }} WIB</span>
                            </div>
                        @else
                            <span class="text-xs text-amber-600 font-bold bg-amber-50 border border-amber-200 px-2 py-0.5 rounded">
                                Belum Dijadwalkan
                            </span>
                        @endif
                    </td>
                    <td class="p-3.5 border-r border-stone-200">
                        <div class="font-bold text-stone-800 text-xs flex items-center gap-1.5">
                            <x-lucide-map-pin class="w-3.5 h-3.5 text-stone-400" />
                            <span>{{ $ek->tempat ?: 'Belum ada ruangan' }}</span>
                        </div>
                    </td>
                    <td class="p-3.5 text-center">
                        @if (!auth()->user()?->isSuperAdmin2())
                            <button wire:click="openEditEkskulSchedule({{ $ek->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-extrabold text-xs border border-emerald-300 transition">
                                <x-lucide-calendar class="w-3.5 h-3.5" />
                                <span>Atur Jadwal</span>
                            </button>
                        @else
                            <span class="text-[11px] text-stone-400 font-semibold italic">Lihat Saja</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-stone-400">
                        Belum ada data ekstrakurikuler.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>
</div>
