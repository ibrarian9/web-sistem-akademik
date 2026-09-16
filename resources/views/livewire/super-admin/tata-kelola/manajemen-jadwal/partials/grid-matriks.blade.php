<!-- GRID TIMETABLE MATRIKS MINGGUAN PER KELAS -->
<div class="space-y-4">
    <div class="flex items-center justify-between bg-emerald-900 border border-emerald-950 p-4 rounded-2xl shadow-xs text-white">
        <h3 class="text-base font-black flex items-center gap-2.5">
            <span class="px-3 py-1 bg-white text-emerald-950 rounded-xl text-xs font-extrabold shadow-2xs">Kelas {{ $activeKelas->nama_kelas ?? '-' }}</span>
            <span>Jadwal Pelajaran Mingguan</span>
        </h3>
        <span class="text-xs text-emerald-100 font-semibold">Total Hari Aktif: <strong>6 Hari (Senin - Sabtu)</strong></span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        @foreach ($days as $day)
            @php
                $daySchedules = $weeklyGrid[$day] ?? collect();
            @endphp
            <div class="bg-white border border-stone-200 rounded-2xl p-4 flex flex-col justify-between space-y-4 shadow-xs hover:border-emerald-500 transition">
                <!-- Day Header -->
                <div class="flex items-center justify-between border-b border-stone-200 pb-2.5">
                    <span class="font-extrabold text-stone-900 text-xs uppercase tracking-wider flex items-center gap-2">
                        <x-lucide-calendar class="w-4 h-4 text-emerald-700 shrink-0" />
                        <span>{{ ucfirst($day) }}</span>
                    </span>

                    <!-- Badge Jumlah Jam -->
                    <x-badge :variant="count($daySchedules) > 0 ? 'emerald' : 'stone'" size="xs">
                        {{ count($daySchedules) }} Jam
                    </x-badge>
                </div>

                <!-- Schedule Slots List -->
                <div class="space-y-3 flex-1 min-h-[170px]">
                    @forelse ($daySchedules as $sched)
                        <div class="p-3 bg-emerald-50/60 border border-emerald-200 hover:border-emerald-400 rounded-xl space-y-2 transition shadow-2xs group">
                            <div class="flex items-center justify-between gap-1">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-700 text-white rounded text-[10px] font-extrabold">
                                    <x-lucide-clock class="w-3 h-3 text-current shrink-0" />
                                    {{ date('H:i', strtotime($sched->jam_mulai)) }} - {{ date('H:i', strtotime($sched->jam_selesai)) }}
                                </span>
                                @if (!auth()->user()?->isSuperAdmin2())
                                    <div class="inline-flex items-center gap-1 shrink-0">
                                        <button type="button" wire:click="openEdit({{ $sched->id }})" class="p-1 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-lg border border-stone-300 transition cursor-pointer" title="Edit">
                                            <x-lucide-edit class="w-3.5 h-3.5" />
                                        </button>
                                        <button type="button" wire:click="delete({{ $sched->id }})" data-confirm="Apakah Anda yakin ingin menghapus jadwal ini?" class="p-1 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg border border-rose-200 transition cursor-pointer" title="Hapus">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="pt-0.5">
                                <h4 class="font-extrabold text-stone-900 text-xs leading-snug group-hover:text-emerald-800 transition">
                                    {{ $sched->guruMapelKelas->mapel->nama_mapel ?? '-' }}
                                </h4>
                                <p class="text-[11px] text-stone-600 font-medium mt-1 flex items-center gap-1.5">
                                    <x-lucide-user class="w-3.5 h-3.5 text-emerald-700 shrink-0" />
                                    <span class="truncate">{{ $sched->guruMapelKelas->guru->user->nama ?? '-' }}</span>
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 px-2 text-center bg-stone-50 border border-dashed border-stone-200 rounded-xl space-y-1">
                            <x-lucide-calendar-x class="w-6 h-6 text-stone-400 mx-auto" />
                            <p class="text-stone-500 text-[11px] font-semibold">Belum Ada Jadwal</p>
                        </div>
                    @endforelse
                </div>

                <!-- Quick Add Button for this Day -->
                @if (!auth()->user()?->isSuperAdmin2())
                    <x-button type="button" variant="outline" size="sm" icon="plus" wire:click="openCreateForDay('{{ $day }}', {{ $selectedKelasId }})" class="w-full justify-center">
                        Tambah {{ ucfirst($day) }}
                    </x-button>
                @endif
            </div>
        @endforeach
    </div>
</div>
