<div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm space-y-4">
    <div class="p-4 bg-emerald-800 border-b border-emerald-900 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xs font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                <span>MATRIKS REKAP MINGGUAN (SENIN – SABTU)</span>
            </h3>
            <p class="text-[11px] text-emerald-100 font-medium mt-0.5">
                Minggu Ke-{{ \Carbon\Carbon::parse($tanggal)->weekOfMonth }} Bulan {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('F Y') }}
            </p>
        </div>
        <div class="text-xs text-emerald-100 font-semibold bg-emerald-900/80 px-3 py-1.5 rounded-xl border border-emerald-700">
            Pilih tanggal di atas untuk berpindah minggu
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs text-stone-800">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <th class="p-3 text-center border-r border-emerald-700 w-10 text-white">NO</th>
                    <th class="p-3 border-r border-emerald-700 min-w-[200px] text-white">NAMA SANTRI</th>
                    @foreach ($weekDays as $wd)
                        <th class="p-2.5 text-center border-r border-emerald-700 min-w-[120px] text-white {{ $wd['is_selected'] ? 'bg-emerald-950 ring-2 ring-amber-400 ring-inset' : ($wd['is_today'] ? 'bg-emerald-700' : 'bg-emerald-800') }}">
                            <div class="font-extrabold text-white">{{ $wd['day_name'] }}</div>
                            <div class="text-[10px] text-emerald-100">{{ $wd['short_date'] }}</div>
                        </th>
                    @endforeach
                    <th class="p-3 text-center w-24 text-white">TOTAL SETORAN</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse($siswas as $index => $s)
                    @php
                        $sWeekly = $weeklyScores[$s->id] ?? [];
                        $totalWeeklyCount = count($sWeekly);
                    @endphp
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3 text-center font-bold text-stone-500 border-r border-stone-200">{{ $index + 1 }}</td>
                        <td class="p-3 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ strtoupper($s->user->nama ?? $s->nama_panggilan) }}</div>
                            <div class="text-[10px] text-stone-500 font-medium">NISN: {{ $s->nisn }}</div>
                        </td>

                        @foreach ($weekDays as $wd)
                            @php
                                $dRec = $sWeekly[$wd['date']] ?? null;
                            @endphp
                            <td class="p-2 border-r border-stone-200 text-center align-top {{ $wd['is_selected'] ? 'bg-amber-50/50' : '' }}">
                                @if($dRec)
                                    <div class="bg-emerald-50 border border-emerald-200 p-2 rounded-lg space-y-1">
                                        <span class="px-1.5 py-0.5 bg-emerald-700 text-white rounded text-[9px] font-bold block">Terisi</span>
                                        @if($dRec->materi_ziyadah)
                                            <div class="text-[10px] font-bold text-stone-800 truncate" title="Ziyadah: {{ $dRec->materi_ziyadah }}">
                                                Z: {{ $dRec->materi_ziyadah }}
                                            </div>
                                        @endif
                                        @if($dRec->nilai_ziyadah !== null || $dRec->nilai_tahsin !== null)
                                            <div class="text-[10px] font-black text-emerald-900">
                                                Nilai: {{ round($dRec->nilai_ziyadah ?? $dRec->nilai_tahsin) }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-stone-300 font-medium italic text-[10px] block py-3">Belum</span>
                                @endif
                            </td>
                        @endforeach

                        <td class="p-3 text-center font-black text-stone-900 text-xs bg-stone-50">
                            {{ $totalWeeklyCount }}/6 Hari
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="p-8 text-center text-stone-500 italic font-medium">Tidak ada santri ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
