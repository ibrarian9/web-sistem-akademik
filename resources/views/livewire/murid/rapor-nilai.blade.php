<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Rapor & Nilai Hasil Belajar</h2>
            <p class="text-xs text-slate-400">Lihat rangkuman pencapaian akademis resmi dan riwayat nilai mata pelajaran Anda.</p>
        </div>

        @if (!$hasOutstanding)
            <!-- TAB BUTTONS -->
            <div class="flex items-center gap-2 bg-slate-900 border border-slate-800 p-1.5 rounded-2xl">
                <button wire:click="$set('activeTab', 'umum')" 
                    class="px-4 py-2 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 {{ $activeTab === 'umum' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:text-white' }}">
                    <x-lucide-book-open class="w-4 h-4" />
                    <span>Rapor Umum</span>
                </button>
                <button wire:click="$set('activeTab', 'tahfidz')" 
                    class="px-4 py-2 rounded-xl text-xs font-bold transition duration-200 flex items-center gap-2 {{ $activeTab === 'tahfidz' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-400 hover:text-white' }}">
                    <x-lucide-award class="w-4 h-4" />
                    <span>Rapor Tahfizh</span>
                </button>
            </div>
        @endif
    </div>

    @if ($hasOutstanding)
        <!-- LOCK CARD (Outstanding SPP Bills per 10th) -->
        <div class="relative overflow-hidden bg-slate-900 border border-rose-500/20 rounded-3xl p-8 shadow-2xl flex flex-col items-center justify-center min-h-[400px]">
            <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-md z-10"></div>
            
            <div class="relative z-20 text-center max-w-md space-y-6 flex flex-col items-center">
                <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-full animate-bounce">
                    <x-lucide-lock class="w-10 h-10" />
                </div>
                
                <div class="space-y-2">
                    <h3 class="text-base font-extrabold text-white uppercase tracking-wider">Akses Rapor Terkunci</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Mohon maaf, Anda belum dapat melihat laporan hasil belajar karena terdapat tunggakan tagihan SPP/administrasi sekolah yang jatuh tempo per tanggal 10.
                    </p>
                </div>

                <div class="pt-4">
                    <a href="{{ route('murid.tagihan') }}" class="py-2.5 px-6 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold transition duration-200 shadow-lg shadow-rose-600/10">
                        Bayar SPP Sekarang
                    </a>
                </div>
            </div>
        </div>
    @else
        @if ($rapor)
            <!-- OFFICIAL RAPOR -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
                <div class="flex items-center justify-between border-b border-slate-850 pb-4">
                    <div class="space-y-1">
                        <span class="px-2.5 py-1 {{ $activeTab === 'tahfidz' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400' }} border rounded-lg text-[10px] font-bold uppercase tracking-wider">
                            {{ $activeTab === 'tahfidz' ? 'Rapor Tahfizh Al-Qur\'an' : 'Rapor Umum Pembelajaran' }}
                        </span>
                        <h3 class="text-base font-bold text-white mt-1">Laporan Hasil Belajar Semester</h3>
                    </div>
                    <div class="flex items-center gap-4">
                        <button wire:click="downloadPdf" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-indigo-600/10">
                            <x-lucide-download class="w-4 h-4" />
                            <span>Unduh PDF</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800">
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Mata Pelajaran</th>
                                @if ($activeTab === 'umum')
                                    <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Pengetahuan</th>
                                    <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Keterampilan</th>
                                    <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Sikap</th>
                                @else
                                    <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Keagamaan / Hafalan</th>
                                @endif
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Nilai Akhir</th>
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850">
                            @php
                                $filteredDetails = array_filter($raporDetails, function($d) use ($activeTab) {
                                    $jenis = $d['mapel']['jenis'] ?? 'umum';
                                    return $activeTab === 'tahfidz' ? $jenis === 'tahfidz' : $jenis === 'umum';
                                });
                            @endphp

                            @forelse ($filteredDetails as $detail)
                                <tr class="hover:bg-slate-950/20">
                                    <td class="py-3.5 text-xs font-bold text-white">{{ $detail['mapel']['nama_mapel'] ?? '-' }}</td>
                                    @if ($activeTab === 'umum')
                                        <td class="py-3.5 text-xs text-slate-300 text-center">{{ $detail['nilai_pengetahuan'] ? floatval($detail['nilai_pengetahuan']) : '-' }}</td>
                                        <td class="py-3.5 text-xs text-slate-300 text-center">{{ $detail['nilai_keterampilan'] ? floatval($detail['nilai_keterampilan']) : '-' }}</td>
                                        <td class="py-3.5 text-xs text-slate-300 text-center">{{ $detail['nilai_sikap'] ? floatval($detail['nilai_sikap']) : '-' }}</td>
                                    @else
                                        <td class="py-3.5 text-xs text-slate-300 text-center">{{ $detail['nilai_keagamaan'] ? floatval($detail['nilai_keagamaan']) : '-' }}</td>
                                    @endif
                                    <td class="py-3.5 text-xs font-bold text-indigo-400 text-center">{{ floatval($detail['nilai_akhir']) }}</td>
                                    <td class="py-3.5 text-xs font-extrabold text-emerald-400 text-center uppercase">{{ $detail['predikat'] ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-xs text-slate-500 italic">
                                        Tidak ada data mata pelajaran untuk kategori ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($activeTab === 'umum' && count($ekskulList) > 0)
                    <!-- EKSTRAKURIKULER SECTION -->
                    <div class="border-t border-slate-850 pt-4 space-y-3">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Kegiatan Ekstrakurikuler</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach ($ekskulList as $e)
                                <div class="p-3.5 bg-slate-950/40 border border-slate-850 rounded-2xl flex items-center justify-between">
                                    <div>
                                        <h5 class="text-xs font-bold text-white">{{ $e['ekstrakurikuler']['nama'] ?? '-' }}</h5>
                                        <p class="text-[10px] text-slate-400">Pembina: {{ $e['ekstrakurikuler']['pembina']['user']['nama'] ?? '-' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2.5 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-xs font-extrabold rounded-lg uppercase">
                                            Predikat: {{ $e['predikat'] }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Wali Kelas Comment -->
                @if ($rapor->catatan_wali_kelas)
                    <div class="p-4 bg-slate-950/40 border border-slate-850 rounded-2xl space-y-1.5">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Catatan Wali Kelas / Pembina</span>
                        <p class="text-xs text-slate-300 italic">"{{ $rapor->catatan_wali_kelas }}"</p>
                    </div>
                @endif
            </div>
        @else
            <!-- LIVE DYNAMIC GRADES (Pending official publish) -->
            <div class="space-y-6">
                <div class="bg-indigo-600/10 border border-indigo-500/20 rounded-3xl p-5 flex items-start gap-4">
                    <div class="p-2.5 bg-indigo-500/10 text-indigo-400 rounded-xl">
                        <x-lucide-info class="w-5 h-5" />
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-xs font-bold text-indigo-400 uppercase tracking-wider">
                            {{ $activeTab === 'tahfidz' ? 'Daftar Nilai Tahfizh (Belum Terbit)' : 'Daftar Nilai Umum (Belum Terbit)' }}
                        </h4>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Rapor resmi belum diterbitkan oleh wali kelas Anda. Halaman ini menampilkan nilai sementara yang diinput oleh masing-masing guru pengampu mata pelajaran.
                        </p>
                    </div>
                </div>

                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                        {{ $activeTab === 'tahfidz' ? 'Mata Pelajaran Tahfizh & Hafalan' : 'Mata Pelajaran Umum' }}
                    </h3>
                    
                    <div class="space-y-4">
                        @php
                            $filteredLive = array_filter($liveGrades, function($g) use ($activeTab) {
                                return $activeTab === 'tahfidz' ? ($g['jenis'] ?? '') === 'tahfidz' : ($g['jenis'] ?? '') === 'umum';
                            });
                        @endphp

                        @forelse ($filteredLive as $mid => $data)
                            <div class="p-4 bg-slate-950/40 border border-slate-850 rounded-2xl space-y-3">
                                <div class="flex justify-between items-center border-b border-slate-850 pb-2">
                                    <h4 class="text-xs font-bold text-white">{{ $data['nama_mapel'] }}</h4>
                                    <span class="text-xs font-bold text-indigo-400">Rata-rata: {{ $data['avg'] }}</span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    @foreach ($data['komponen'] as $k)
                                        <div class="p-2 bg-slate-900/60 border border-slate-850 rounded-xl text-center">
                                            <span class="text-[9px] text-slate-500 font-bold block uppercase tracking-wider">{{ $k['nama'] }}</span>
                                            <span class="text-xs font-black text-white block mt-0.5">{{ $k['nilai'] }}</span>
                                            @if ($k['catatan'])
                                                <span class="text-[8px] text-slate-400 block mt-0.5 italic truncate" title="{{ $k['catatan'] }}">{{ $k['catatan'] }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-slate-500 text-xs font-medium">
                                Belum ada entri nilai pelajaran untuk kategori {{ $activeTab }} semester ini.
                            </div>
                        @endforelse
                    </div>

                    @if ($activeTab === 'umum' && count($ekskulList) > 0)
                        <!-- EKSTRAKURIKULER SECTION FOR LIVE GRADES -->
                        <div class="border-t border-slate-850 pt-4 space-y-3">
                            <h4 class="text-xs font-bold text-white uppercase tracking-wider">Kegiatan Ekstrakurikuler</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach ($ekskulList as $e)
                                    <div class="p-3.5 bg-slate-950/40 border border-slate-850 rounded-2xl flex items-center justify-between">
                                        <div>
                                            <h5 class="text-xs font-bold text-white">{{ $e['ekstrakurikuler']['nama'] ?? '-' }}</h5>
                                            <p class="text-[10px] text-slate-400">Pembina: {{ $e['ekstrakurikuler']['pembina']['user']['nama'] ?? '-' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2.5 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-xs font-extrabold rounded-lg uppercase">
                                                Predikat: {{ $e['predikat'] }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>
