<div class="space-y-6">
    @if ($selectedSiswa)
        <!-- Student Overview Card -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 border border-emerald-200 flex items-center justify-center text-emerald-800 font-black text-base shadow-2xs">
                    {{ substr($selectedSiswa->user->nama ?? 'S', 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wide">
                            {{ $selectedSiswa->user->nama ?? 'Nama Siswa' }}
                        </h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-indigo-100 text-indigo-800 border border-indigo-200">
                            Anak Berkebutuhan Khusus (ABK)
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-stone-500 font-medium mt-1 flex-wrap">
                        <span>NIS: <b class="text-stone-800">{{ $selectedSiswa->nis ?: '-' }}</b></span>
                        <span>Kelas: <b class="text-stone-800">{{ $selectedSiswa->kelas->nama_kelas ?? '-' }}</b></span>
                        <span>Halaqah: <b class="text-stone-800">{{ $selectedSiswa->kelasTahfidz->nama_kelas ?? '-' }}</b></span>
                        <span>Guru Pendamping: <b class="text-emerald-700">{{ $selectedSiswa->shadowTeacher->user->nama ?? 'Belum Diplot' }}</b></span>
                    </div>
                </div>
            </div>

            <!-- Periode Indicator & Switcher -->
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-stone-600">Filter Periode:</span>
                <div class="flex items-center p-1 bg-stone-100 border border-stone-200 rounded-xl text-xs font-bold">
                    <button type="button" wire:click="$set('rekapPeriode', 'tengah_semester')" class="px-3 py-1 rounded-lg transition {{ $rekapPeriode === 'tengah_semester' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                        Tengah Semester
                    </button>
                    <button type="button" wire:click="$set('rekapPeriode', 'akhir_semester')" class="px-3 py-1 rounded-lg transition {{ $rekapPeriode === 'akhir_semester' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                        Akhir Semester
                    </button>
                    <button type="button" wire:click="$set('rekapPeriode', 'semua')" class="px-3 py-1 rounded-lg transition {{ $rekapPeriode === 'semua' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                        Semua
                    </button>
                </div>
            </div>
        </div>

        <!-- Capaian Distribution Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-2xs space-y-1">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Total Observasi</span>
                <div class="text-2xl font-black text-stone-900">{{ $rekapData['total_observasi'] ?? 0 }}</div>
                <span class="text-[10px] text-stone-400 font-medium">Sesi pengamatan</span>
            </div>

            <div class="bg-rose-50/60 border border-rose-200 rounded-2xl p-4 shadow-2xs space-y-1">
                <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider">BB (Belum Berkembang)</span>
                <div class="text-2xl font-black text-rose-800">{{ $rekapData['total_bb'] ?? 0 }}</div>
                <span class="text-[10px] text-rose-600 font-semibold">Perlu bimbingan penuh</span>
            </div>

            <div class="bg-amber-50/60 border border-amber-200 rounded-2xl p-4 shadow-2xs space-y-1">
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">MB (Mulai Berkembang)</span>
                <div class="text-2xl font-black text-amber-800">{{ $rekapData['total_mb'] ?? 0 }}</div>
                <span class="text-[10px] text-amber-600 font-semibold">Mulai tampak kemandirian</span>
            </div>

            <div class="bg-emerald-50/60 border border-emerald-200 rounded-2xl p-4 shadow-2xs space-y-1">
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">BSH (Sesuai Harapan)</span>
                <div class="text-2xl font-black text-emerald-800">{{ $rekapData['total_bsh'] ?? 0 }}</div>
                <span class="text-[10px] text-emerald-600 font-semibold">Konsisten & stabil</span>
            </div>

            <div class="bg-blue-50/60 border border-blue-200 rounded-2xl p-4 shadow-2xs space-y-1">
                <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">BSB (Sangat Baik)</span>
                <div class="text-2xl font-black text-blue-800">{{ $rekapData['total_bsb'] ?? 0 }}</div>
                <span class="text-[10px] text-blue-600 font-semibold">Mandiri & inisiatif</span>
            </div>
        </div>

        <!-- 7 Aspects Qualitative Progress Matrix -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-extrabold text-stone-800 uppercase tracking-wider flex items-center gap-2">
                    <x-lucide-award class="w-4 h-4 text-emerald-600" />
                    <span>Rekapitulasi Capaian Kualitatif 7 Aspek Pengamatan:</span>
                </h4>
                <span class="text-[11px] font-bold text-stone-400">Periode: {{ ucfirst(str_replace('_', ' ', $rekapPeriode)) }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($rekapData['aspek_breakdown'] as $aspekNama => $aData)
                    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex items-start justify-between gap-2 border-b border-stone-100 pb-3">
                                <div>
                                    <h5 class="text-xs font-black text-stone-900 uppercase tracking-wide">{{ $aspekNama }}</h5>
                                    <span class="text-[10px] text-stone-400 font-medium">{{ $aData['total_observasi'] }} kali diobservasi</span>
                                </div>
                                @if ($aData['capaian_terakhir'])
                                    @php
                                        $bColor = match($aData['capaian_terakhir']) {
                                            'BB' => 'bg-rose-100 text-rose-800 border-rose-300',
                                            'MB' => 'bg-amber-100 text-amber-800 border-amber-300',
                                            'BSH' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                            'BSB' => 'bg-blue-100 text-blue-800 border-blue-300',
                                            default => 'bg-stone-100 text-stone-800 border-stone-300',
                                        };
                                    @endphp
                                    <div class="text-right">
                                        <span class="inline-block px-2.5 py-1 rounded-xl text-xs font-black border {{ $bColor }}">
                                            {{ $aData['capaian_terakhir'] }}
                                        </span>
                                        <span class="text-[9px] font-bold text-stone-500 block mt-0.5">{{ $aData['capaian_label'] }}</span>
                                    </div>
                                @else
                                    <span class="text-[11px] text-stone-400 italic font-medium">Belum diobservasi</span>
                                @endif
                            </div>

                            <!-- Distribution pills -->
                            <div class="grid grid-cols-4 gap-1.5 pt-3 text-center">
                                <div class="bg-rose-50/80 rounded-lg p-1 text-[10px] font-bold text-rose-700">
                                    BB: <b class="font-black">{{ $aData['distribusi']['BB'] }}</b>
                                </div>
                                <div class="bg-amber-50/80 rounded-lg p-1 text-[10px] font-bold text-amber-700">
                                    MB: <b class="font-black">{{ $aData['distribusi']['MB'] }}</b>
                                </div>
                                <div class="bg-emerald-50/80 rounded-lg p-1 text-[10px] font-bold text-emerald-700">
                                    BSH: <b class="font-black">{{ $aData['distribusi']['BSH'] }}</b>
                                </div>
                                <div class="bg-blue-50/80 rounded-lg p-1 text-[10px] font-bold text-blue-700">
                                    BSB: <b class="font-black">{{ $aData['distribusi']['BSB'] }}</b>
                                </div>
                            </div>

                            <!-- Latest notes preview -->
                            <div class="pt-3">
                                <span class="text-[10px] font-extrabold text-stone-500 uppercase tracking-wider block mb-1">Catatan Terakhir:</span>
                                <p class="text-xs text-stone-700 leading-relaxed font-medium bg-stone-50 p-2.5 rounded-xl border border-stone-200">
                                    {{ $aData['catatan_terakhir'] ?: 'Belum ada catatan pada periode ini.' }}
                                </p>
                            </div>
                        </div>

                        @if ($aData['rekomendasi_terakhir'])
                            <div class="pt-2 border-t border-stone-100">
                                <span class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider block mb-0.5">Tindak Lanjut:</span>
                                <p class="text-[11px] text-stone-600 italic">"{{ $aData['rekomendasi_terakhir'] }}"</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Consolidated Recommendations Box -->
        <div class="bg-emerald-50/50 border border-emerald-200 rounded-2xl p-6 shadow-xs space-y-3">
            <h4 class="text-xs font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                <x-lucide-lightbulb class="w-4 h-4 text-emerald-700" />
                <span>Rangkuman Rekomendasi & Rencana Tindak Lanjut Guru Pendamping:</span>
            </h4>
            @if (!empty($rekapData['rekomendasi_list']))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($rekapData['rekomendasi_list'] as $rec)
                        <div class="bg-white p-3 rounded-xl border border-emerald-200 text-xs font-medium text-stone-800 shadow-2xs flex items-start gap-2">
                            <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                            <span>{{ $rec }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-stone-500 italic">Belum ada rekomendasi yang dicatat pada periode ini.</p>
            @endif
        </div>
    @else
        <x-table.empty title="Pilih Siswa Terlebih Dahulu" message="Silakan pilih siswa pada dropdown di atas untuk menampilkan rekapitulasi perkembangan." />
    @endif
</div>
