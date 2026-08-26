<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
                SD Tahfizh F3 Digital System
            </span>
            <h2 class="text-xl font-extrabold text-stone-900 tracking-tight mt-1">Jadwal Remedial Saya</h2>
            <p class="text-xs text-stone-500 font-medium">Informasi jadwal sesi remedial per-TP & Mid Semester yang diberikan oleh Ustadz/Ustadzah pengampu.</p>
        </div>
    </div>

    <!-- Info Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pelaksanaan Remedial Belajar Santri"
        :steps="[
            ['title' => 'Cek Jadwal & Topik', 'desc' => 'Periksa tanggal, jam pelaksanaan, dan materi/TP remedial yang ditentukan oleh guru pengampu.'],
            ['title' => 'Persiapan Modul', 'desc' => 'Membawa buku latihan, modul pembelajaran, dan alat tulis saat sesi remedial berlangsung.'],
            ['title' => 'Kehadiran Tepat Waktu', 'desc' => 'Harap hadir di ruangan remedial 10 menit sebelum sesi dimulai.']
        ]"
        notes="Sesi remedial ini khusus untuk Mata Pelajaran Umum (Kurikulum Merdeka) sesuai arahan Guru Pengampu."
    />

    <!-- Remedial List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($remedialList as $item)
            <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-4 flex flex-col justify-between hover:border-emerald-400 transition duration-200">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2 border-b border-stone-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700">
                                <x-lucide-book-open class="w-4 h-4" />
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wide">{{ $item->mapel->nama_mapel ?? 'Mata Pelajaran' }}</h3>
                                <p class="text-[10px] text-stone-500 font-semibold">Kelas: {{ $item->kelas->nama_kelas ?? '-' }}</p>
                            </div>
                        </div>

                        <div>
                            @if ($item->status === 'dijadwalkan')
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-900 border border-amber-300 rounded-full font-black text-[10px] uppercase">
                                    Dijadwalkan
                                </span>
                            @elseif ($item->status === 'selesai')
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-full font-black text-[10px] uppercase">
                                    Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-stone-200 text-stone-700 border border-stone-300 rounded-full font-black text-[10px] uppercase">
                                    Dibatalkan
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-extrabold text-stone-900 leading-snug">{{ $item->topik_tp }}</div>
                        <div class="mt-1">
                            @if ($item->kategori === 'harian_tp')
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-900 rounded-md text-[10px] font-extrabold uppercase">Nilai Harian / Per-TP</span>
                            @elseif ($item->kategori === 'mid_sts')
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-900 rounded-md text-[10px] font-extrabold uppercase">Mid Semester (STS)</span>
                            @else
                                <span class="px-2 py-0.5 bg-stone-100 text-stone-800 rounded-md text-[10px] font-extrabold uppercase">Umum</span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-stone-100 text-xs">
                        <div class="flex items-center justify-between text-stone-600">
                            <span class="text-[11px] font-bold text-stone-500 flex items-center gap-1">
                                <x-lucide-calendar class="w-3.5 h-3.5 text-stone-400" /> Tanggal:
                            </span>
                            <span class="font-extrabold text-stone-900">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d M Y') }}</span>
                        </div>

                        <div class="flex items-center justify-between text-stone-600">
                            <span class="text-[11px] font-bold text-stone-500 flex items-center gap-1">
                                <x-lucide-clock class="w-3.5 h-3.5 text-stone-400" /> Waktu:
                            </span>
                            <span class="font-bold text-stone-900">{{ substr($item->waktu_mulai, 0, 5) }} - {{ substr($item->waktu_selesai, 0, 5) }} WIB</span>
                        </div>

                        <div class="flex items-center justify-between text-stone-600">
                            <span class="text-[11px] font-bold text-stone-500 flex items-center gap-1">
                                <x-lucide-map-pin class="w-3.5 h-3.5 text-rose-500" /> Ruangan:
                            </span>
                            <span class="font-bold text-stone-900">{{ $item->ruangan }}</span>
                        </div>

                        <div class="flex items-center justify-between text-stone-600">
                            <span class="text-[11px] font-bold text-stone-500 flex items-center gap-1">
                                <x-lucide-user class="w-3.5 h-3.5 text-emerald-600" /> Guru Pengampu:
                            </span>
                            <span class="font-bold text-stone-900 truncate max-w-[140px]">{{ $item->guru->user->nama ?? 'Guru Pengampu' }}</span>
                        </div>
                    </div>

                    @if ($item->catatan)
                        <div class="p-2.5 bg-stone-50 border border-stone-200 rounded-xl text-[11px] text-stone-600 font-medium">
                            <strong class="text-stone-800">Catatan:</strong> {{ $item->catatan }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white border border-stone-200 rounded-3xl p-10 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center mx-auto">
                    <x-lucide-check-circle class="w-6 h-6" />
                </div>
                <h3 class="text-sm font-extrabold text-stone-900 uppercase">Tidak Ada Jadwal Remedial</h3>
                <p class="text-xs text-stone-500 max-w-sm mx-auto">Alhamdulillah, saat ini Anda tidak memiliki jadwal remedial aktif. Pertahankan pencapaian belajar Anda!</p>
            </div>
        @endforelse
    </div>
</div>
