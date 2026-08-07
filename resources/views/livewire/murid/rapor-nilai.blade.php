<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-900 tracking-tight">Rapor & Nilai Hasil Belajar</h2>
            <p class="text-xs text-stone-500">Lihat rangkuman pencapaian akademis resmi, capaian Tahfizh, dan Projek P5 Anda.</p>
        </div>

        @if (!$hasOutstanding)
            <!-- TAB BUTTONS (KM, Tahfizh, P5) -->
            <div class="flex items-center gap-1.5 bg-stone-100 border border-stone-200 p-1.5 rounded-2xl overflow-x-auto">
                <button wire:click="setTab('km')" 
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-1.5 whitespace-nowrap {{ $activeTab === 'km' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-stone-600 hover:text-stone-900' }}">
                    <x-lucide-book-open class="w-4 h-4" />
                    <span>Kurikulum Merdeka</span>
                </button>
                <button wire:click="setTab('tahfidz')" 
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-1.5 whitespace-nowrap {{ $activeTab === 'tahfidz' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-stone-600 hover:text-stone-900' }}">
                    <x-lucide-award class="w-4 h-4" />
                    <span>Rapor Tahfizh</span>
                </button>
                <button wire:click="setTab('p5')" 
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-1.5 whitespace-nowrap {{ $activeTab === 'p5' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-stone-600 hover:text-stone-900' }}">
                    <x-lucide-sparkles class="w-4 h-4" />
                    <span>Kokurikuler P5</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Penilaian & Ketentuan Rapor Digital"
        :steps="[
            ['title' => 'Tab Navigasi', 'desc' => 'Gunakan tombol beralih di kanan atas untuk memantau Rapor Kurikulum Merdeka, Rapor Tahfizh, atau Rapor P5.'],
            ['title' => 'Auto-Narasi', 'desc' => 'Deskripsi capaian rapor Kurikulum Merdeka di-generate secara otomatis berdasarkan skor TP tertinggi & terendah.'],
            ['title' => 'Verifikasi QR Code', 'desc' => 'Seluruh rapor digital yang diterbitkan dilengkapi QR Code keabsahan tanpa perlu tanda tangan basah.']
        ]"
        notes="Akses rapor akan otomatis terkunci jika terdapat tunggakan SPP yang belum diselesaikan per tanggal 10."
    />

    @if ($hasOutstanding)
        <!-- LOCK CARD (Outstanding SPP Bills per 10th) -->
        <div class="relative overflow-hidden bg-white border border-rose-200 rounded-2xl p-8 shadow-sm flex flex-col items-center justify-center min-h-[350px]">
            <div class="relative z-20 text-center max-w-md space-y-6 flex flex-col items-center">
                <div class="p-4 bg-rose-100 text-rose-600 border border-rose-200 rounded-full">
                    <x-lucide-lock class="w-10 h-10" />
                </div>
                
                <div class="space-y-2">
                    <h3 class="text-base font-bold text-stone-900 uppercase tracking-wider">Akses Rapor Terkunci</h3>
                    <p class="text-xs text-stone-600 font-medium leading-relaxed">
                        Mohon maaf, Anda belum dapat melihat laporan hasil belajar karena terdapat tunggakan tagihan SPP/administrasi sekolah yang jatuh tempo per tanggal 10.
                    </p>
                </div>

                <div class="pt-2">
                    <a href="{{ route('murid.tagihan') }}" class="py-2.5 px-6 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition duration-200 shadow-md">
                        Bayar SPP Sekarang
                    </a>
                </div>
            </div>
        </div>
    @else
        @if ($rapor)
            <!-- OFFICIAL RAPOR -->
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-stone-200 pb-4">
                    <div class="space-y-1">
                        <span class="px-2.5 py-1 bg-emerald-100 border-emerald-300 text-emerald-800 border rounded-lg text-[10px] font-bold uppercase tracking-wider">
                            @if($activeTab === 'km') Rapor Kurikulum Merdeka
                            @elseif($activeTab === 'tahfidz') Rapor Tahfizh Al-Qur'an
                            @else Rapor Kokurikuler P5
                            @endif
                        </span>
                        <h3 class="text-base font-bold text-stone-900 mt-1">Laporan Hasil Belajar Digital</h3>
                    </div>
                    <div class="flex items-center gap-4">
                        <button wire:click="downloadPdf" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-md">
                            <x-lucide-download class="w-4 h-4" />
                            <span>Unduh PDF</span>
                        </button>
                    </div>
                </div>

                @if($activeTab === 'km')
                    <!-- TAB 1: KURIKULUM MERDEKA ACADEMIC REPORT -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-stone-200 bg-stone-50 text-stone-600 text-xs font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4">Mata Pelajaran</th>
                                    <th class="py-3 px-4 text-center">Nilai Akhir</th>
                                    <th class="py-3 px-4 text-center">Predikat</th>
                                    <th class="py-3 px-4">Deskripsi Capaian Pembelajaran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200">
                                @forelse ($raporDetails as $detail)
                                    <tr class="hover:bg-stone-50 transition">
                                        <td class="py-3.5 px-4 text-xs font-bold text-stone-900">
                                            {{ $detail['mapel']['nama_mapel'] ?? '-' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-xs font-black text-emerald-700 text-center">
                                            {{ floatval($detail['nilai_akhir']) }}
                                        </td>
                                        <td class="py-3.5 px-4 text-xs font-black text-emerald-800 text-center uppercase">
                                            {{ $detail['predikat'] ?: '-' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-xs text-stone-700 leading-relaxed">
                                            @if(!empty($detail['narasi_capaian_full']))
                                                {{ $detail['narasi_capaian_full'] }}
                                            @else
                                                <span class="text-stone-400 italic">Deskripsi capaian otomatis belum di-generate.</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-xs text-stone-500 italic">
                                            Belum ada data mata pelajaran Kurikulum Merdeka.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (count($ekskulList) > 0)
                        <!-- EKSTRAKURIKULER -->
                        <div class="border-t border-stone-200 pt-4 space-y-3">
                            <h4 class="text-xs font-bold text-stone-900 uppercase tracking-wider">Kegiatan Ekstrakurikuler</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach ($ekskulList as $e)
                                    <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-xl flex items-center justify-between">
                                        <div>
                                            <h5 class="text-xs font-bold text-stone-900">{{ $e['ekstrakurikuler']['nama'] ?? '-' }}</h5>
                                            <p class="text-[10px] text-stone-500 font-medium">Pembina: {{ $e['ekstrakurikuler']['pembina']['user']['nama'] ?? '-' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-extrabold rounded-lg uppercase">
                                                Predikat: {{ $e['predikat'] }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                @elseif($activeTab === 'tahfidz')
                    <!-- TAB 2: TAHFIZH REPORT -->
                    <div class="space-y-6">
                        @if(!empty($raporTahfidz['summary']))
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 block">Total Juz Dihafal</span>
                                    <span class="text-2xl font-black text-emerald-900 mt-1 block">{{ $raporTahfidz['summary']->total_juz_dihafal }} Juz</span>
                                </div>
                                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 block">Rata-rata Tajwid</span>
                                    <span class="text-2xl font-black text-emerald-900 mt-1 block">{{ number_format($raporTahfidz['summary']->nilai_tajwid_rata, 1) }}</span>
                                </div>
                                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 block">Predikat Tahfizh</span>
                                    <span class="text-xl font-black text-emerald-900 mt-1 block uppercase">{{ $raporTahfidz['summary']->predikat_tahfidz }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-stone-200 bg-stone-50 text-stone-600 text-xs font-bold uppercase tracking-wider">
                                        <th class="py-3 px-4">Surah & Juz</th>
                                        <th class="py-3 px-4 text-center">Kelancaran</th>
                                        <th class="py-3 px-4 text-center">Tajwid</th>
                                        <th class="py-3 px-4">Predikat Keagamaan</th>
                                        <th class="py-3 px-4">Catatan Ustadz</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-200">
                                    @forelse ($raporTahfidz['scores'] as $sc)
                                        <tr class="hover:bg-stone-50 transition">
                                            <td class="py-3.5 px-4 text-xs font-bold text-stone-900">
                                                <span class="text-emerald-700 font-bold block">{{ $sc->surah }}</span>
                                                <span class="text-[10px] text-stone-500 font-normal">Juz {{ $sc->juz }}</span>
                                            </td>
                                            <td class="py-3.5 px-4 text-xs font-bold text-center text-stone-800">{{ number_format($sc->nilai_kelancaran, 1) }}</td>
                                            <td class="py-3.5 px-4 text-xs font-bold text-center text-stone-800">{{ number_format($sc->nilai_tajwid, 1) }}</td>
                                            <td class="py-3.5 px-4 text-xs font-bold text-emerald-800">{{ $sc->predikat_keagamaan }}</td>
                                            <td class="py-3.5 px-4 text-xs text-stone-600 italic">{{ $sc->catatan_ustadz ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-xs text-stone-500 italic">
                                                Belum ada entri catatan Tahfizh untuk semester ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                @else
                    <!-- TAB 3: PROJEK P5 KOKURIKULER -->
                    <div class="space-y-4">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-stone-200 bg-stone-50 text-stone-600 text-xs font-bold uppercase tracking-wider">
                                        <th class="py-3 px-4">Proyek P5</th>
                                        <th class="py-3 px-4">Dimensi & Sub-Dimensi</th>
                                        <th class="py-3 px-4 text-center">Titik Sumatif</th>
                                        <th class="py-3 px-4 text-center">Capaian Kualitatif</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-200">
                                    @forelse ($nilaiP5List as $p5)
                                        <tr class="hover:bg-stone-50 transition">
                                            <td class="py-3.5 px-4 text-xs font-bold text-stone-900">
                                                {{ $p5['proyek']['nama_proyek'] ?? '-' }}
                                            </td>
                                            <td class="py-3.5 px-4 text-xs text-stone-800">
                                                <span class="font-bold text-emerald-700 block">{{ $p5['subdimensi_p5']['dimensi']['nama_dimensi'] ?? '-' }}</span>
                                                <span class="text-[11px] text-stone-600 block">{{ $p5['subdimensi_p5']['nama_subdimensi'] ?? '-' }}</span>
                                            </td>
                                            <td class="py-3.5 px-4 text-xs font-bold text-center text-stone-700">
                                                Sumatif {{ $p5['titik_sumatif'] }}
                                            </td>
                                            <td class="py-3.5 px-4 text-xs font-bold text-center">
                                                @php
                                                    $labels = [1 => 'BB (Belum Berkembang)', 2 => 'MB (Mulai Berkembang)', 3 => 'BSH (Berkembang Sesuai Harapan)', 4 => 'SB (Sangat Berkembang)'];
                                                @endphp
                                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-lg text-xs font-bold border border-emerald-200">
                                                    {{ $labels[$p5['nilai']] ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-6 text-center text-xs text-stone-500 italic">
                                                Belum ada entri penilaian Kokurikuler P5 semester ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Catatan Wali Kelas -->
                @if ($rapor->catatan_wali_kelas)
                    <div class="p-4 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                        <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Catatan Wali Kelas / Pembina</span>
                        <p class="text-xs text-stone-800 italic font-semibold">"{{ $rapor->catatan_wali_kelas }}"</p>
                    </div>
                @endif

                <!-- QR CODE VERIFICATION BLOCK (Tanpa Tanda Tangan Basah) -->
                @if($rapor->qr_code_hash)
                    <div class="border-t border-stone-200 pt-6">
                        <x-ttd-elektronik 
                            role="kepala_sekolah" 
                            docType="RAP" 
                            :docId="$rapor->id" 
                            location="Sleman" 
                        />
                    </div>
                @endif
            </div>
        @else
            <!-- LIVE DYNAMIC GRADES (Pending official publish) -->
            <div class="space-y-6">
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-start gap-4 shadow-sm">
                    <div class="p-2 bg-emerald-600 text-white rounded-xl">
                        <x-lucide-info class="w-5 h-5" />
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-xs font-bold text-emerald-900 uppercase tracking-wider">
                            Rapor Resmi Belum Terbit
                        </h4>
                        <p class="text-xs text-emerald-800 font-medium leading-relaxed">
                            Wali kelas Anda sedang dalam proses finalisasi rapor. Di bawah ini adalah ringkasan nilai sementara yang sudah diinput oleh guru pengampu.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
