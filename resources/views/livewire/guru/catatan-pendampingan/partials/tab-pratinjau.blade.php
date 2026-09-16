<div class="space-y-6">
    @if ($selectedSiswa)
        <!-- Action Bar -->
        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-2 text-xs font-bold text-stone-700">
                <x-lucide-printer class="w-4 h-4 text-emerald-600" />
                <span>Pratinjau Lembar Laporan Pendampingan: <b class="text-stone-900">{{ $selectedSiswa->user->nama ?? 'Siswa' }}</b> ({{ ucfirst(str_replace('_', ' ', $rekapPeriode)) }})</span>
            </div>

            <div class="flex items-center gap-2">
                <a 
                    href="{{ route('pendampingan.cetak', ['siswaId' => $selectedSiswa->id, 'periode' => $rekapPeriode]) }}" 
                    target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm"
                >
                    <x-lucide-download class="w-3.5 h-3.5" />
                    <span>Cetak / Unduh PDF</span>
                </a>
            </div>
        </div>

        <!-- Live Report Document Frame -->
        <div class="max-w-4xl mx-auto bg-white border border-stone-300 rounded-3xl p-8 sm:p-12 shadow-md space-y-6 text-stone-900">
            <!-- Kop Laporan -->
            <div class="text-center border-b-2 border-emerald-800 pb-4 space-y-1">
                <h2 class="text-base sm:text-lg font-black tracking-wide text-emerald-950 uppercase">
                    PONDOK PESANTREN & SEKOLAH ISLAM TERPADU
                </h2>
                <h3 class="text-xs sm:text-sm font-extrabold text-stone-800 uppercase tracking-wider">
                    LAPORAN CAPAIAN PERKEMBANGAN PENDAMPINGAN KHUSUS (INKLUSI)
                </h3>
                <p class="text-[11px] text-stone-500 font-medium">
                    Periode: {{ $rekapPeriode === 'tengah_semester' ? 'Tengah Semester (PTS)' : ($rekapPeriode === 'akhir_semester' ? 'Akhir Semester (PAS/PAT)' : 'Seluruh Periode') }}
                </p>
            </div>

            <!-- Meta Data Siswa & Pendamping -->
            <div class="grid grid-cols-2 gap-y-2 gap-x-8 text-xs bg-stone-50/60 p-4 rounded-xl border border-stone-200">
                <div>
                    <span class="text-stone-500 font-bold block">Nama Peserta Didik:</span>
                    <span class="font-black text-stone-900 text-sm">{{ strtoupper($selectedSiswa->user->nama ?? '-') }}</span>
                </div>
                <div>
                    <span class="text-stone-500 font-bold block">Kelas / Rombel:</span>
                    <span class="font-extrabold text-stone-900">{{ $selectedSiswa->kelas->nama_kelas ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-stone-500 font-bold block">NIS / NISN:</span>
                    <span class="font-bold text-stone-800">{{ $selectedSiswa->nis ?: '-' }} / {{ $selectedSiswa->nisn ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-stone-500 font-bold block">Guru Pendamping Khusus:</span>
                    <span class="font-black text-emerald-800">{{ $selectedSiswa->shadowTeacher->user->nama ?? 'Guru Pendamping' }}</span>
                </div>
            </div>

            <!-- Kriteria Capaian Legend -->
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-[11px] text-emerald-950 font-medium">
                <b class="font-extrabold uppercase block mb-1">Skala Capaian Kualitatif:</b>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <span><b>BB</b> : Belum Berkembang</span>
                    <span><b>MB</b> : Mulai Berkembang</span>
                    <span><b>BSH</b> : Sesuai Harapan</span>
                    <span><b>BSB</b> : Sangat Baik</span>
                </div>
            </div>

            <!-- Rekapitulasi 7 Aspek Tabel -->
            <div class="border border-stone-300 rounded-xl overflow-hidden">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-stone-100 text-stone-900 font-extrabold uppercase border-b border-stone-300">
                        <tr>
                            <th class="p-3 w-12 text-center border-r border-stone-300">No</th>
                            <th class="p-3 w-48 border-r border-stone-300">Aspek Pengamatan</th>
                            <th class="p-3 w-32 text-center border-r border-stone-300">Capaian</th>
                            <th class="p-3">Deskripsi & Catatan Pengamatan Guru</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-300">
                        @php $n = 1; @endphp
                        @foreach ($rekapData['aspek_breakdown'] as $aName => $aRow)
                            <tr>
                                <td class="p-3 text-center font-bold border-r border-stone-300">{{ $n++ }}</td>
                                <td class="p-3 font-extrabold text-stone-900 border-r border-stone-300">{{ $aName }}</td>
                                <td class="p-3 text-center border-r border-stone-300">
                                    @if ($aRow['capaian_terakhir'])
                                        @php
                                            $pill = match($aRow['capaian_terakhir']) {
                                                'BB' => 'bg-rose-100 text-rose-800 border-rose-300',
                                                'MB' => 'bg-amber-100 text-amber-800 border-amber-300',
                                                'BSH' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                                'BSB' => 'bg-blue-100 text-blue-800 border-blue-300',
                                                default => 'bg-stone-100 text-stone-800',
                                            };
                                        @endphp
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-black border {{ $pill }}">
                                            {{ $aRow['capaian_terakhir'] }}
                                        </span>
                                        <span class="text-[10px] text-stone-500 block mt-0.5">{{ $aRow['capaian_label'] }}</span>
                                    @else
                                        <span class="text-stone-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="p-3 text-stone-700 leading-relaxed">
                                    {{ $aRow['catatan_terakhir'] ?: 'Belum ada catatan pada periode ini.' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Rekomendasi Box -->
            <div class="p-4 bg-stone-50 border border-stone-300 rounded-xl space-y-2 text-xs">
                <b class="font-extrabold uppercase text-stone-900 block">Rekomendasi & Rencana Tindak Lanjut:</b>
                @if (!empty($rekapData['rekomendasi_list']))
                    <ul class="list-disc pl-5 space-y-1 text-stone-700 font-medium">
                        @foreach ($rekapData['rekomendasi_list'] as $rec)
                            <li>{{ $rec }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-stone-500 italic">Pertahankan pendampingan intensif bersama guru dan orang tua.</p>
                @endif
            </div>

            <!-- Tanda Tangan Footer -->
            <div class="grid grid-cols-3 gap-4 text-center text-xs pt-8 border-t border-stone-200">
                <div>
                    <span>Orang Tua / Wali Siswa,</span>
                    <div class="h-16"></div>
                    <b class="border-t border-stone-400 pt-1 block">( ........................................ )</b>
                </div>
                <div>
                    <span>Guru Pendamping Khusus,</span>
                    <div class="h-16"></div>
                    <b class="border-t border-stone-400 pt-1 block text-emerald-900">{{ $selectedSiswa->shadowTeacher->user->nama ?? 'Guru Pendamping' }}</b>
                </div>
                <div>
                    <span>Kepala Sekolah,</span>
                    <div class="h-16"></div>
                    <b class="border-t border-stone-400 pt-1 block">Ustadz Pembina, M.Pd</b>
                </div>
            </div>
        </div>
    @endif
</div>
