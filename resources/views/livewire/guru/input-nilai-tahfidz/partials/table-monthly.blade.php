<div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm space-y-4">
    <div class="p-4 bg-emerald-800 border-b border-emerald-900 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xs font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                <span>REKAPITULASI CAPAIAN BULANAN ({{ strtoupper(\Carbon\Carbon::parse(($selectedMonth ?: date('Y-m')) . '-01')->translatedFormat('F Y')) }})</span>
            </h3>
            <p class="text-[11px] text-emerald-100 font-medium mt-0.5">
                Ringkasan total frekuensi setoran harian, nilai rata-rata, dan capaian surah terakhir santri.
            </p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs text-stone-800">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <th class="p-3 text-center border-r border-emerald-700 w-10 text-white">NO</th>
                    <th class="p-3 border-r border-emerald-700 min-w-[220px] text-white">NAMA SANTRI</th>
                    <th class="p-3 text-center border-r border-emerald-700 w-36 text-white">FREKUENSI SETORAN</th>
                    <th class="p-3 text-center border-r border-emerald-700 w-32 text-white">RATA-RATA NILAI</th>
                    <th class="p-3 border-r border-emerald-700 min-w-[180px] text-white">SURAH TERAKHIR / ZIYADAH</th>
                    <th class="p-3 text-center border-r border-emerald-700 w-24 text-white">JUZ TERTINGGI</th>
                    <th class="p-3 text-center w-28 text-white">PREDIKAT</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse($siswas as $index => $s)
                    @php
                        $mSummary = $monthlyScores[$s->id] ?? [
                            'total_entries' => 0,
                            'avg_score' => '-',
                            'latest_ziyadah' => '-',
                            'max_juz' => 1,
                        ];
                        $avgVal = is_numeric($mSummary['avg_score']) ? (float)$mSummary['avg_score'] : null;
                        $pred = $avgVal ? ($avgVal >= 85 ? 'Sangat Baik' : ($avgVal >= 75 ? 'Baik' : 'Cukup')) : '-';
                    @endphp
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3 text-center font-bold text-stone-500 border-r border-stone-200">{{ $index + 1 }}</td>
                        <td class="p-3 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ strtoupper($s->user->nama ?? $s->nama_panggilan) }}</div>
                            <div class="text-[10px] text-stone-500 font-medium">NISN: {{ $s->nisn }}</div>
                        </td>
                        <td class="p-3 text-center border-r border-stone-200">
                            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full font-extrabold text-xs">
                                {{ $mSummary['total_entries'] }} Kali Setoran
                            </span>
                        </td>
                        <td class="p-3 text-center font-black text-emerald-950 border-r border-stone-200 text-xs">
                            {{ $mSummary['avg_score'] }}
                        </td>
                        <td class="p-3 border-r border-stone-200 font-bold text-stone-800">
                            {{ $mSummary['latest_ziyadah'] }}
                        </td>
                        <td class="p-3 text-center border-r border-stone-200 font-extrabold text-stone-700">
                            Juz {{ $mSummary['max_juz'] }}
                        </td>
                        <td class="p-3 text-center">
                            @if($pred === 'Sangat Baik')
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-full font-bold text-[10px]">Sangat Baik</span>
                            @elseif($pred === 'Baik')
                                <span class="px-2.5 py-1 bg-blue-100 text-blue-900 border border-blue-300 rounded-full font-bold text-[10px]">Baik</span>
                            @elseif($pred === 'Cukup')
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-900 border border-amber-300 rounded-full font-bold text-[10px]">Cukup</span>
                            @else
                                <span class="text-stone-400 font-medium text-[11px] italic">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-stone-500 italic font-medium">Tidak ada santri ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
