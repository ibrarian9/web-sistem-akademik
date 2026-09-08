<div class="space-y-6 font-sans">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
                SD Tahfizh F3 Digital System
            </span>
            <h2 class="text-xl font-extrabold text-stone-900 tracking-tight mt-1">Nilai Akademik Mata Pelajaran Umum</h2>
            <p class="text-xs text-stone-500 font-medium">Pantau rangkuman Nilai per-Bab (Lingkup Materi), Nilai SAS, dan Rapor Hasil Belajar Kurikulum Merdeka Anda.</p>
        </div>

        @if (!$hasOutstanding)
            <!-- TAB BUTTONS (Rekap Rapor, Nilai per-Bab, & Kokurikuler P5) -->
            <div class="flex items-center gap-1.5 bg-stone-100 border border-stone-200 p-1.5 rounded-2xl overflow-x-auto shadow-xs">
                <button wire:click="setTab('rekap')" 
                    class="px-4 py-2 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'rekap' ? 'bg-emerald-600 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200/60' }}">
                    <x-lucide-award class="w-4 h-4" />
                    <span>Rekap Nilai Rapor</span>
                </button>
                <button wire:click="setTab('bab')" 
                    class="px-4 py-2 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'bab' ? 'bg-emerald-600 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200/60' }}">
                    <x-lucide-layers class="w-4 h-4" />
                    <span>Nilai per-Bab</span>
                </button>
                <button wire:click="setTab('p5')" 
                    class="px-4 py-2 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'p5' ? 'bg-cyan-600 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200/60' }}">
                    <x-lucide-star class="w-4 h-4" />
                    <span>Kokurikuler (P5)</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Transparansi Evaluasi Nilai Belajar Santri"
        :steps="[
            ['title' => 'Rekap Nilai Rapor', 'desc' => 'Menampilkan perolehan nilai mata pelajaran selaras dengan penilaian Sumatif Bab, SAS, dan Nilai Akhir.'],
            ['title' => 'Nilai per-Bab', 'desc' => 'Menyajikan capaian nilai santri pada setiap Bab / Lingkup Materi pembelajaran.'],
            ['title' => 'Capaian Kokurikuler P5', 'desc' => 'Melihat capaian kualitatif perkembangan profil pelajar Pancasila santri (BB, MB, BSH, SB).']
        ]"
        notes="Pencetakan / Penerbitan Dokumen Rapor PDF Resmi dikelola sepenuhnya oleh Pihak Sekolah (Wali Kelas / Guru / Tata Usaha)."
    />

    @if ($hasOutstanding)
        <!-- LOCK CARD (Outstanding SPP Bills per 10th) -->
        <div class="relative overflow-hidden bg-white border border-rose-200 rounded-3xl p-8 shadow-sm flex flex-col items-center justify-center min-h-[350px]">
            <div class="relative z-20 text-center max-w-md space-y-6 flex flex-col items-center">
                <div class="p-4 bg-rose-100 text-rose-600 border border-rose-200 rounded-full">
                    <x-lucide-lock class="w-10 h-10" />
                </div>
                
                <div class="space-y-2">
                    <h3 class="text-base font-extrabold text-stone-900 uppercase tracking-wider">Akses Rapor Terkunci</h3>
                    <p class="text-xs text-stone-600 font-medium leading-relaxed">
                        Mohon maaf, Anda belum dapat melihat dokumen Rapor Hasil Belajar karena terdapat tunggakan tagihan SPP/administrasi sekolah yang jatuh tempo per tanggal 10.
                    </p>
                </div>

                <div class="pt-2">
                    <a href="{{ route('murid.tagihan') }}" class="py-2.5 px-6 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition duration-200 shadow-sm">
                        Bayar SPP Sekarang
                    </a>
                </div>
            </div>
        </div>
    @else
        @if ($activeTab === 'rekap')
            <!-- TAB 1: REKAPITULASI NILAI MAPEL (RATA-RATA BAB & SAS) -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wide flex items-center gap-2">
                        <x-lucide-award class="w-4 h-4 text-emerald-600" />
                        <span>Rekapitulasi Nilai Akademik & Rapor Akhir</span>
                    </h3>
                </div>

                <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-0 text-xs text-stone-800">
                            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider select-none text-[11px]">
                                <tr>
                                    <th class="w-12 p-3 text-center border-b border-r border-emerald-700">No</th>
                                    <th class="p-3 border-b border-r border-emerald-700">Mata Pelajaran</th>
                                    <th class="w-28 p-3 text-center border-b border-r border-emerald-700">Rata-rata Bab</th>
                                    <th class="w-24 p-3 text-center bg-emerald-900 border-b border-r border-emerald-950">Nilai SAS</th>
                                    <th class="w-28 p-3 text-center bg-emerald-950 border-b border-r border-emerald-950">Nilai Akhir</th>
                                    <th class="w-24 p-3 text-center border-b border-emerald-700">Predikat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200 bg-white">
                                @forelse ($rekapRaporUtama as $idx => $r)
                                    <tr class="hover:bg-stone-50 transition">
                                        <td class="p-3 text-center font-bold text-stone-500 border-r border-stone-200">{{ $idx + 1 }}</td>
                                        <td class="p-3 font-extrabold text-stone-900 border-r border-stone-200">{{ $r['nama_mapel'] }}</td>
                                        <td class="p-3 text-center font-semibold text-stone-700 border-r border-stone-200">{{ $r['avg_bab'] !== null ? number_format($r['avg_bab'], 1) : '-' }}</td>
                                        <td class="p-3 text-center font-black text-emerald-900 bg-emerald-50/40 border-r border-stone-200">{{ $r['nilai_sas'] !== null ? number_format($r['nilai_sas'], 0) : '-' }}</td>
                                        <td class="p-3 text-center font-black text-xs sm:text-sm bg-emerald-50/70 text-emerald-950 border-r border-stone-200">
                                            {{ $r['nilai_akhir'] !== null ? number_format($r['nilai_akhir'], 1) : '-' }}
                                        </td>
                                        <td class="p-3 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black {{ in_array($r['predikat'], ['A', 'B']) ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-700' }}">
                                                {{ $r['predikat'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-stone-400 font-medium">Belum ada data nilai mata pelajaran untuk semester ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @elseif ($activeTab === 'bab')
            <!-- TAB 2: NILAI PER-BAB (LINGKUP MATERI) -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wide flex items-center gap-2">
                        <x-lucide-book-open class="w-4 h-4 text-emerald-600" />
                        <span>Rincian Capaian Nilai per-Bab (Lingkup Materi)</span>
                    </h3>
                </div>

                @forelse ($nilaiPerBab as $mapelId => $item)
                    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-4 bg-stone-50 border-b border-stone-200 flex items-center justify-between">
                            <h4 class="text-xs font-extrabold text-stone-900">{{ $item['nama_mapel'] }}</h4>
                            <div class="flex items-center gap-2">
                                @if ($item['avg_mapel'] !== null)
                                    <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-extrabold">
                                        Rata-rata: {{ $item['avg_mapel'] }}
                                    </span>
                                @endif
                                <span class="px-2.5 py-0.5 bg-stone-200 text-stone-700 rounded-full text-[10px] font-bold">
                                    {{ count($item['babs']) }} Bab
                                </span>
                            </div>
                        </div>
                        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($item['babs'] as $bab)
                                <div class="bg-stone-50/70 border border-stone-200 p-3.5 rounded-xl space-y-2 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="px-2 py-0.5 bg-emerald-600 text-white rounded-md text-[10px] font-black uppercase">
                                                Bab {{ $bab['urutan'] }}
                                            </span>
                                            @if ($bab['nilai'] !== null)
                                                <span class="text-base font-black text-emerald-800">{{ $bab['nilai'] }}</span>
                                            @else
                                                <span class="text-xs font-semibold text-stone-400">Belum Dinilai</span>
                                            @endif
                                        </div>
                                        <p class="text-xs font-bold text-stone-800 mt-2 leading-snug">{{ $bab['judul'] }}</p>
                                    </div>
                                    @if ($bab['predikat'] !== '-')
                                        <div class="text-[10px] text-stone-500 font-semibold border-t border-stone-200/80 pt-1.5 flex items-center justify-between">
                                            <span>Predikat:</span>
                                            <span class="font-black {{ in_array($bab['predikat'], ['A', 'B']) ? 'text-emerald-700' : 'text-stone-700' }}">{{ $bab['predikat'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="bg-white border border-stone-200 rounded-3xl p-10 text-center space-y-2">
                        <x-lucide-layers class="w-8 h-8 text-stone-300 mx-auto" />
                        <p class="text-xs text-stone-500 font-medium">Belum ada data Nilai per-Bab yang di-input untuk semester aktif ini.</p>
                    </div>
                @endforelse
            </div>
        @elseif ($activeTab === 'p5')
            <!-- TAB 3: CAPAIAN KOKURIKULER P5 -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wide flex items-center gap-2">
                        <x-lucide-star class="w-4 h-4 text-cyan-600" />
                        <span>Capaian Projek Penguatan Profil Pelajar Pancasila (P5)</span>
                    </h3>
                </div>

                @forelse ($nilaiP5 as $proyekName => $items)
                    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-stone-100 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-cyan-100 text-cyan-800 border border-cyan-300">
                                    PROJEK KOKURIKULER
                                </span>
                                <h4 class="text-sm font-extrabold text-stone-900">{{ $proyekName }}</h4>
                            </div>
                            <span class="text-xs font-bold text-stone-500">{{ count($items) }} Sub-dimensi Dinilai</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach ($items as $item)
                                @php
                                    $badgeVariant = match($item['nilai']) {
                                        1 => 'bg-rose-100 text-rose-800 border-rose-200',
                                        2 => 'bg-amber-100 text-amber-800 border-amber-200',
                                        3 => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        4 => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                        default => 'bg-stone-100 text-stone-700 border-stone-200'
                                    };
                                @endphp
                                <div class="p-3.5 bg-stone-50/80 rounded-xl border border-stone-200 flex items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <span class="text-[10px] font-black text-stone-500 uppercase tracking-wider block">{{ $item['dimensi'] }}</span>
                                        <h5 class="text-xs font-bold text-stone-900 leading-snug">{{ $item['subdimensi'] }}</h5>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black shrink-0 border {{ $badgeVariant }}">
                                        {{ $item['label'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="bg-white border border-stone-200 rounded-3xl p-10 text-center space-y-2">
                        <x-lucide-star class="w-8 h-8 text-stone-300 mx-auto" />
                        <p class="text-xs text-stone-500 font-medium">Belum ada data penilaian Projek P5 yang diinput untuk santri pada semester ini.</p>
                    </div>
                @endforelse
            </div>
        @endif
    @endif
</div>
