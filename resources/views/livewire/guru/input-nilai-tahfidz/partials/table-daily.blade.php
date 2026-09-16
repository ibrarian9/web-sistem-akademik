<div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm space-y-4">
    <!-- Daily Toolbar Header -->
    <div class="p-4 bg-emerald-800 border-b border-emerald-900 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-xs font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                SETORAN HARIAN: {{ strtoupper(\Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y')) }}
            </span>
            <span class="px-2.5 py-0.5 bg-emerald-900 text-emerald-100 rounded-full text-[11px] font-bold">
                {{ $dailyScores->count() }}/{{ count($siswas) }} Santri Terisi
            </span>
        </div>

        <!-- Quick Live Search Input -->
        <div class="relative min-w-[240px]">
            <input 
                type="text" 
                wire:model.live.debounce.250ms="search" 
                placeholder="Cari santri / NISN..." 
                class="w-full pl-9 pr-4 py-2 bg-emerald-900/90 border border-emerald-600 rounded-xl text-white placeholder-emerald-300 text-xs font-medium focus:ring-2 focus:ring-amber-400 focus:bg-emerald-900 shadow-inner"
            />
            <svg class="w-4 h-4 text-emerald-300 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    <div class="overflow-x-auto relative custom-scrollbar">
        <table class="w-full text-left border-separate border-spacing-0 text-xs text-stone-800">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider select-none">
                <tr>
                    <th rowspan="2" class="hidden md:table-cell p-3 text-center border-b border-r border-emerald-700 w-12 min-w-[48px] sticky left-0 bg-emerald-800 text-white z-20">NO</th>
                    <th rowspan="2" class="p-2.5 md:p-3 border-b border-r border-emerald-700 min-w-[130px] max-w-[145px] md:min-w-[220px] md:max-w-none sticky left-0 md:left-12 bg-emerald-800 text-white z-20 shadow-[3px_0_5px_-2px_rgba(0,0,0,0.25)] md:shadow-none">NISN & NAMA SANTRI</th>
                    <th rowspan="2" class="p-2.5 md:p-3 text-center border-b border-r border-emerald-700 w-12 text-white">L/P</th>
                    <th colspan="2" class="p-2 text-center border-b border-r border-emerald-700 bg-emerald-900/60 text-white">TAHSIN</th>
                    <th colspan="3" class="p-2 text-center border-b border-r border-emerald-700 bg-emerald-900/80 text-white">MURAJA'AH</th>
                    <th colspan="2" class="p-2 text-center border-b border-r border-emerald-700 bg-emerald-900/60 text-white">KITABAH</th>
                    <th colspan="2" class="p-2 text-center border-b border-r border-emerald-700 bg-emerald-900/80 text-white">ZIYADAH</th>
                    <th rowspan="2" class="p-3 text-center border-b border-r border-emerald-700 min-w-[200px] bg-emerald-900/60 text-white font-extrabold">TANGGAPAN ORANG TUA / WALI</th>
                    <th rowspan="2" class="p-3 text-center border-b min-w-[130px] text-white">AKSI</th>
                </tr>

                <tr>
                    <!-- Tahsin subheaders -->
                    <th class="p-2 text-center border-b border-r border-emerald-700 min-w-[120px] font-bold text-white">Materi/Ayat</th>
                    <th class="p-2 text-center border-b border-r border-emerald-700 w-16 bg-emerald-900 font-extrabold text-white">Nilai</th>
                    
                    <!-- Muraja'ah subheaders -->
                    <th class="p-2 text-center border-b border-r border-emerald-700 min-w-[90px] font-bold text-white">Bersama</th>
                    <th class="p-2 text-center border-b border-r border-emerald-700 min-w-[120px] font-bold text-white">Mandiri</th>
                    <th class="p-2 text-center border-b border-r border-emerald-700 w-16 bg-emerald-900 font-extrabold text-white">Nilai</th>

                    <!-- Kitabah subheaders -->
                    <th class="p-2 text-center border-b border-r border-emerald-700 min-w-[120px] font-bold text-white">Materi</th>
                    <th class="p-2 text-center border-b border-r border-emerald-700 w-16 bg-emerald-900 font-extrabold text-white">Nilai</th>

                    <!-- Ziyadah subheaders -->
                    <th class="p-2 text-center border-b border-r border-emerald-700 min-w-[120px] font-bold text-white">Materi</th>
                    <th class="p-2 text-center border-b border-r border-emerald-700 w-16 bg-emerald-900 font-extrabold text-white">Nilai</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse($siswas as $index => $s)
                    @php
                        $rec = $dailyScores->get($s->id);
                        $gender = strtolower($s->jenis_kelamin ?? 'L') === 'p' ? 'P' : 'L';
                        $isFilled = $rec !== null;
                    @endphp
                    <!-- Row Clickable for User-Friendly Interaction -->
                    <tr 
                        wire:click="openScoreModal({{ $s->id }})" 
                        class="hover:bg-emerald-50/70 cursor-pointer transition group"
                        title="Klik untuk mengedit/mengisi mutaba'ah tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }} untuk {{ $s->user->nama ?? $s->nama_panggilan }}"
                    >
                        <!-- No (Desktop Only) -->
                        <td class="hidden md:table-cell p-3 text-center font-bold text-stone-500 border-b border-r border-stone-200 text-xs sticky left-0 bg-white group-hover:bg-emerald-50/90 z-10">
                            {{ $index + 1 }}
                        </td>

                        <!-- NISN & Nama Santri (Sticky Left with Status Pill + Kelas Badge) -->
                        <td class="p-2.5 md:p-3 border-b border-r-2 md:border-r border-stone-200 sticky left-0 md:left-12 bg-white group-hover:bg-emerald-50/90 z-10 shadow-[3px_0_6px_-2px_rgba(0,0,0,0.12)] md:shadow-none min-w-[130px] max-w-[145px] md:min-w-[220px] md:max-w-none">
                            <div class="font-extrabold text-stone-900 text-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                <span class="truncate">{{ strtoupper($s->user->nama ?? $s->nama_panggilan) }}</span>
                                <span class="px-1.5 py-0.5 bg-emerald-100 border border-emerald-300 text-emerald-900 text-[9px] sm:text-[10px] rounded-md font-bold shrink-0 self-start sm:self-auto">
                                    {{ $s->kelas->nama_kelas ?? 'Kelas -' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="text-[10px] text-stone-500 font-medium">NISN: {{ $s->nisn }}</span>
                                @if($isFilled)
                                    <span class="px-1.5 py-0.5 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded text-[9px] font-bold inline-flex items-center gap-1">
                                        <svg class="w-2.5 h-2.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        <span>Terisi</span>
                                    </span>
                                @else
                                    <span class="px-1.5 py-0.5 bg-amber-100 border border-amber-300 text-amber-900 rounded text-[9px] font-bold inline-flex items-center gap-1">
                                        <span>Belum</span>
                                    </span>
                                @endif
                            </div>
                        </td>

                        <td class="p-3 text-center font-bold text-stone-600 border-b border-r border-stone-200">{{ $gender }}</td>
                        
                        <!-- Tahsin -->
                        <td class="p-2 border-b border-r border-stone-200 text-stone-700 font-medium text-center">
                            {{ $rec?->materi_tahsin ?? '-' }}
                        </td>
                        <td class="p-2 text-center border-b border-r border-stone-200 bg-emerald-50/40 font-black text-emerald-950">
                            {{ ($rec && $rec->nilai_tahsin !== null) ? round($rec->nilai_tahsin) : '-' }}
                        </td>

                        <!-- Muraja'ah -->
                        <td class="p-2 border-b border-r border-stone-200 text-stone-700 font-medium text-center">
                            {{ $rec?->murajaah_bersama ?? '-' }}
                        </td>
                        <td class="p-2 border-b border-r border-stone-200 text-stone-700 font-medium text-center">
                            {{ $rec?->murajaah_mandiri ?? '-' }}
                        </td>
                        <td class="p-2 text-center border-b border-r border-stone-200 bg-emerald-50/40 font-black text-emerald-950">
                            {{ ($rec && $rec->nilai_murajaah !== null) ? round($rec->nilai_murajaah) : '-' }}
                        </td>

                        <!-- Kitabah -->
                        <td class="p-2 border-b border-r border-stone-200 text-stone-700 font-medium text-center">
                            {{ $rec?->materi_kitabah ?? '-' }}
                        </td>
                        <td class="p-2 text-center border-b border-r border-stone-200 bg-emerald-50/40 font-black text-emerald-950">
                            {{ ($rec && $rec->nilai_kitabah !== null) ? round($rec->nilai_kitabah) : '-' }}
                        </td>

                        <!-- Ziyadah -->
                        <td class="p-2 border-b border-r border-stone-200 text-stone-700 font-medium text-center">
                            {{ $rec?->materi_ziyadah ?? '-' }}
                        </td>
                        <td class="p-2 text-center border-b border-r border-stone-200 bg-emerald-50/40 font-black text-emerald-950">
                            {{ ($rec && $rec->nilai_ziyadah !== null) ? round($rec->nilai_ziyadah) : '-' }}
                        </td>

                        <!-- Tanggapan Orang Tua -->
                        <td class="p-2 border-b border-r border-stone-200 text-stone-700 text-[11px]">
                            @if($rec && $rec->tanggapan_orang_tua)
                                <div class="bg-emerald-50 p-2 rounded-lg border border-emerald-200 text-emerald-950 shadow-xs">
                                    <div class="font-medium italic text-[11px]">"{{ $rec->tanggapan_orang_tua }}"</div>
                                    <div class="text-[9px] text-emerald-800 font-bold mt-1">Oleh: {{ $rec->dikirim_oleh_nama ?: 'Orang Tua' }}</div>
                                </div>
                            @else
                                <span class="text-stone-400 italic block text-center">-</span>
                            @endif
                        </td>

                        <!-- AKSI / Edit & Delete -->
                        <td class="p-2 border-b text-center" @click.stop>
                            @if($rec)
                                <div class="flex items-center justify-center gap-1.5">
                                    <x-button type="button" variant="secondary" size="xs" icon="edit-3" wire:click.prevent="editScore({{ $rec->id }})" title="Edit Mutaba'ah">
                                        Edit
                                    </x-button>
                                    <x-button variant="danger" size="xs" icon="trash-2" wire:click.prevent="deleteScore({{ $rec->id }})" data-confirm="Apakah Anda yakin ingin menghapus data mutaba'ah santri ini?" title="Hapus Mutaba'ah">
                                        Hapus
                                    </x-button>
                                </div>
                            @else
                                <x-button variant="primary" size="xs" icon="plus" wire:click.prevent="openScoreModal({{ $s->id }})">
                                    Isi Mutaba'ah
                                </x-button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="14" title="Tidak ada santri ditemukan" message="Pastikan Anda telah memilih kelas tahfizh yang aktif atau sesuaikan kata kunci pencarian." />
                @endforelse
            </tbody>
        </table>
    </div>
</div>
