<div class="space-y-6 font-sans">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
                SD Tahfizh F3 Digital System
            </span>
            <h2 class="text-xl font-extrabold text-stone-900 tracking-tight mt-1">Nilai Akademik Mata Pelajaran Umum</h2>
            <p class="text-xs text-stone-500 font-medium">Pantau rangkuman Nilai Harian per-Tujuan Pembelajaran (TP) dan Evaluasi Mid Semester (STS) Kurikulum Merdeka Anda.</p>
        </div>

        @if (!$hasOutstanding)
            <!-- TAB BUTTONS (Nilai Harian Per-TP & Mid Semester STS) -->
            <div class="flex items-center gap-1.5 bg-stone-100 border border-stone-200 p-1.5 rounded-2xl overflow-x-auto shadow-xs">
                <button wire:click="setTab('tp')" 
                    class="px-4 py-2 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'tp' ? 'bg-emerald-600 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200/60' }}">
                    <x-lucide-layers class="w-4 h-4" />
                    <span>Nilai Harian Per-TP</span>
                </button>
                <button wire:click="setTab('mid')" 
                    class="px-4 py-2 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'mid' ? 'bg-emerald-600 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200/60' }}">
                    <x-lucide-award class="w-4 h-4" />
                    <span>Mid Semester (STS)</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Transparansi Evaluasi Nilai Belajar Santri"
        :steps="[
            ['title' => 'Nilai Harian Per-TP', 'desc' => 'Menyajikan perolehan nilai formatif santri pada setiap Tujuan Pembelajaran (TP) dan Lingkup Materi.'],
            ['title' => 'Evaluasi Mid Semester (STS)', 'desc' => 'Menampilkan hasil nilai Sumatif Tengah Semester untuk mengukur pemahaman materi setengah semester.'],
            ['title' => 'Evaluasi Tahfizh Dipisahkan', 'desc' => 'Untuk mengecek hafalan &amp; mutabaah Al-Qur\'an, silakan membuka menu Evaluasi Tahfizh di bilah navigasi.']
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
        @if ($activeTab === 'tp')
            <!-- TAB 1: NILAI HARIAN PER-TP (FORMATIF) -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wide flex items-center gap-2">
                        <x-lucide-book-open class="w-4 h-4 text-emerald-600" />
                        <span>Rincian Nilai Harian per-Tujuan Pembelajaran (TP)</span>
                    </h3>
                </div>

                @forelse ($nilaiHarianTp as $mapelId => $item)
                    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-4 bg-stone-50 border-b border-stone-200 flex items-center justify-between">
                            <h4 class="text-xs font-extrabold text-stone-900">{{ $item['nama_mapel'] }}</h4>
                            <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-extrabold">
                                {{ count($item['items']) }} TP Evaluasi
                            </span>
                        </div>
                        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($item['items'] as $tp)
                                <div class="bg-stone-50/70 border border-stone-200 p-3.5 rounded-xl space-y-2 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="px-2 py-0.5 bg-emerald-600 text-white rounded-md text-[10px] font-black uppercase">{{ $tp['kode_tp'] }}</span>
                                            <span class="text-base font-black text-emerald-800">{{ $tp['nilai'] }}</span>
                                        </div>
                                        <p class="text-xs font-bold text-stone-800 mt-2 leading-snug">{{ $tp['deskripsi'] }}</p>
                                    </div>
                                    <div class="text-[10px] text-stone-500 font-semibold border-t border-stone-200/80 pt-1.5">
                                        Lingkup: {{ $tp['lingkup'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <!-- Fallback / Rekap Mapel Umum -->
                    @forelse ($rekapMapelUmum as $mapelId => $m)
                        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-3">
                            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                                <h4 class="text-xs font-extrabold text-stone-900">{{ $m['nama_mapel'] }}</h4>
                                <span class="text-xs font-black text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                    Rata-rata: {{ $m['avg'] }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @forelse ($m['harian'] as $h)
                                    <div class="p-3 bg-stone-50 rounded-xl border border-stone-200 flex items-center justify-between text-xs">
                                        <span class="font-bold text-stone-700">{{ $h['komponen'] }}</span>
                                        <span class="font-black text-emerald-800">{{ $h['nilai'] }}</span>
                                    </div>
                                @empty
                                    <p class="text-[11px] text-stone-400 font-medium">Belum ada rincian nilai harian.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="bg-white border border-stone-200 rounded-3xl p-10 text-center space-y-2">
                            <x-lucide-layers class="w-8 h-8 text-stone-300 mx-auto" />
                            <p class="text-xs text-stone-500 font-medium">Belum ada data Nilai Harian per-TP yang di-input untuk semester aktif ini.</p>
                        </div>
                    @endforelse
                @endforelse
            </div>
        @else
            <!-- TAB 2: MID SEMESTER (STS) -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wide flex items-center gap-2">
                        <x-lucide-award class="w-4 h-4 text-emerald-600" />
                        <span>Hasil Nilai Sumatif Tengah Semester (Mid Semester / STS)</span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse ($nilaiMidSts as $mapelId => $item)
                        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-3">
                            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                                <h4 class="text-xs font-extrabold text-stone-900">{{ $item['nama_mapel'] }}</h4>
                                <span class="px-2.5 py-1 bg-amber-100 border border-amber-300 text-amber-900 rounded-full font-black text-[10px] uppercase">
                                    Mid Semester (STS)
                                </span>
                            </div>

                            <div class="space-y-2">
                                @foreach ($item['items'] as $sts)
                                    <div class="p-3.5 bg-amber-50/60 border border-amber-200/80 rounded-xl flex items-center justify-between">
                                        <div>
                                            <span class="text-xs font-bold text-stone-800 block">{{ $sts['komponen'] }}</span>
                                            @if ($sts['catatan'])
                                                <span class="text-[10px] text-stone-500 italic block mt-0.5">{{ $sts['catatan'] }}</span>
                                            @endif
                                        </div>
                                        <div class="text-xl font-black text-amber-900">{{ $sts['nilai'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <!-- Fallback / Rekap Mid -->
                        @forelse ($rekapMapelUmum as $mapelId => $m)
                            @if (!empty($m['mid']))
                                <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-3">
                                    <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                                        <h4 class="text-xs font-extrabold text-stone-900">{{ $m['nama_mapel'] }}</h4>
                                        <span class="px-2.5 py-1 bg-amber-100 border border-amber-300 text-amber-900 rounded-full font-black text-[10px] uppercase">
                                            Mid STS
                                        </span>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach ($m['mid'] as $sts)
                                            <div class="p-3.5 bg-amber-50/60 border border-amber-200/80 rounded-xl flex items-center justify-between">
                                                <span class="text-xs font-bold text-stone-800">{{ $sts['komponen'] }}</span>
                                                <span class="text-xl font-black text-amber-900">{{ $sts['nilai'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="col-span-full bg-white border border-stone-200 rounded-3xl p-10 text-center space-y-2">
                                <x-lucide-award class="w-8 h-8 text-stone-300 mx-auto" />
                                <p class="text-xs text-stone-500 font-medium">Belum ada data Nilai Mid Semester (STS) yang diterbitkan.</p>
                            </div>
                        @endforelse
                    @endforelse
                </div>
            </div>
        @endif
    @endif
</div>
