<div class="space-y-6">
    <!-- Header Banner -->
    <div class="rounded-2xl bg-emerald-800 p-5 md:p-6 text-white shadow-md border border-emerald-900">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-900 border border-emerald-600 rounded-full text-white text-[11px] font-bold tracking-wide uppercase">
                    <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>LEMBAR MUTABA'AH SETORAN TAHFIZH</span>
                </div>
                <h1 class="text-xl md:text-2xl font-bold !text-white tracking-tight">Setoran & Tanggapan Orang Tua</h1>
                <p class="text-emerald-100 text-xs md:text-sm max-w-2xl leading-relaxed">
                    Laporan perkembangan hafalan, muraja'ah, dan catatan ustadz untuk <span class="font-bold !text-white underline">{{ $siswa->user->nama ?? 'Santri' }}</span>.
                </p>
            </div>
            @if($siswa && $siswa->kelas && $siswa->kelas->guruTahfidz)
                <div class="bg-white rounded-xl p-4 min-w-[210px] text-slate-800 shadow-sm border border-emerald-600">
                    <div class="text-[11px] text-emerald-800 uppercase font-bold tracking-wider">Ustadz Pembimbing</div>
                    <div class="text-sm font-bold text-slate-900 mt-0.5">{{ $siswa->kelas->guruTahfidz->user->nama ?? 'Ustadz Tahfizh' }}</div>
                    <div class="text-xs text-slate-600 font-medium mt-0.5">Halaqah Kelas: <span class="text-emerald-800 font-bold">{{ $siswa->kelas->nama_kelas ?? '-' }}</span></div>
                </div>
            @endif
        </div>
    </div>

    <!-- Flash Message -->
    @if(session()->has('message'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-400 text-emerald-950 rounded-xl flex items-center gap-3 shadow-xs">
            <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-bold text-xs text-emerald-950">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-900 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Juz Dihafal</div>
                <div class="text-base font-bold text-slate-900 mt-0.5">Juz {{ $summary['max_juz'] }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-900 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <div>
                <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Predikat Keagamaan</div>
                <div class="text-base font-bold text-emerald-800 mt-0.5">{{ $summary['predikat'] }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-900 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Surah Terakhir</div>
                <div class="text-base font-bold text-slate-900 mt-0.5 truncate max-w-[140px]">{{ $summary['last_surah'] }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-900 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012-2M9 5a2 2 0 012 2"/></svg>
            </div>
            <div>
                <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Total Catatan</div>
                <div class="text-base font-bold text-slate-900 mt-0.5">{{ $summary['total_setoran'] }} Records</div>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Header & Filter -->
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">Lembar Mutaba'ah Setoran Tahfizh</h2>
                <p class="text-xs font-medium text-slate-500 mt-0.5">Format Mutaba'ah SD TAHFIZH F3</p>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-700">Pilih Semester:</label>
                <select wire:model.live="semester_id" class="px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-800 shadow-xs focus:ring-2 focus:ring-emerald-600">
                    @foreach($semesters as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_semester ?? ('Semester ' . $s->id) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Table View with Unified Harmonious Palette -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-800 border-collapse">
                <thead class="bg-emerald-800 text-white font-bold text-[11px] uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <th class="py-3 px-3 text-center w-12 border-r border-emerald-700">No</th>
                        <th class="py-3 px-4 border-r border-emerald-700 min-w-[160px]">Tahsin Al-Qur'an</th>
                        <th class="py-3 px-4 border-r border-emerald-700 min-w-[180px]">Muraja'ah (Bersama & Mandiri)</th>
                        <th class="py-3 px-4 border-r border-emerald-700 min-w-[150px]">Kitabah (Menulis)</th>
                        <th class="py-3 px-4 border-r border-emerald-700 min-w-[160px]">Ziyadah (Hafalan Baru)</th>
                        <th class="py-3 px-4 border-r border-emerald-700 min-w-[200px]">Catatan Ustadz</th>
                        <th class="py-3 px-4 text-center min-w-[220px]">Tanggapan Orang Tua / Wali</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($setoranList as $index => $rec)
                        <tr class="hover:bg-slate-50 transition">
                            <!-- No -->
                            <td class="py-3.5 px-3 text-center font-bold text-slate-600 border-r border-slate-200 text-xs bg-slate-50">
                                {{ $index + 1 }}
                            </td>

                            <!-- Tahsin -->
                            <td class="py-3.5 px-4 border-r border-slate-200 align-top">
                                <div class="font-bold text-slate-900 text-xs leading-relaxed">
                                    {{ $rec->materi_tahsin ?: '-' }}
                                </div>
                                <div class="mt-1.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                        <span>Nilai:</span>
                                        <span class="font-extrabold text-emerald-800">{{ $rec->nilai_tahsin !== null ? round($rec->nilai_tahsin) : '-' }}</span>
                                    </span>
                                </div>
                            </td>

                            <!-- Muraja'ah -->
                            <td class="py-3.5 px-4 border-r border-slate-200 align-top">
                                <div class="space-y-1 text-xs">
                                    <div>
                                        <span class="font-bold text-slate-500">Bersama:</span> 
                                        <span class="font-bold text-slate-900">{{ $rec->murajaah_bersama ?: '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-500">Mandiri:</span> 
                                        <span class="font-bold text-slate-900">{{ $rec->murajaah_mandiri ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="mt-1.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                        <span>Nilai:</span>
                                        <span class="font-extrabold text-emerald-800">{{ $rec->nilai_murajaah !== null ? round($rec->nilai_murajaah) : '-' }}</span>
                                    </span>
                                </div>
                            </td>

                            <!-- Kitabah -->
                            <td class="py-3.5 px-4 border-r border-slate-200 align-top">
                                <div class="font-bold text-slate-900 text-xs leading-relaxed">
                                    {{ $rec->materi_kitabah ?: '-' }}
                                </div>
                                <div class="mt-1.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                        <span>Nilai:</span>
                                        <span class="font-extrabold text-emerald-800">{{ $rec->nilai_kitabah !== null ? round($rec->nilai_kitabah) : '-' }}</span>
                                    </span>
                                </div>
                            </td>

                            <!-- Ziyadah -->
                            <td class="py-3.5 px-4 border-r border-slate-200 align-top">
                                <div class="font-bold text-slate-900 text-xs leading-relaxed">
                                    {{ $rec->materi_ziyadah ?: ($rec->surah ?: '-') }}
                                </div>
                                <div class="text-[11px] font-bold text-slate-500 mt-0.5">
                                    Juz {{ $rec->juz ?: 1 }}
                                </div>
                                <div class="mt-1.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                        <span>Nilai:</span>
                                        <span class="font-extrabold text-amber-800">{{ $rec->nilai_ziyadah !== null ? round($rec->nilai_ziyadah) : '-' }}</span>
                                    </span>
                                </div>
                            </td>

                            <!-- Catatan Ustadz -->
                            <td class="py-3.5 px-4 border-r border-slate-200 align-top">
                                <div class="p-3 bg-slate-50 border-l-4 border-emerald-600 rounded-r-xl text-xs font-semibold text-slate-800 shadow-2xs">
                                    <div class="leading-relaxed italic">
                                        "{{ $rec->catatan_ustadz ?: 'Alhamdulillah, tingkatkan hafalan dan tetap istiqamah.' }}"
                                    </div>
                                    <div class="mt-2 pt-1.5 border-t border-slate-200 flex items-center justify-between text-[11px] font-bold text-emerald-800">
                                        <span>Predikat: {{ $rec->predikat_keagamaan ?: 'Sangat Baik' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Tanggapan Orang Tua -->
                            <td class="py-3.5 px-4 align-top text-center">
                                @if($rec->tanggapan_orang_tua)
                                    <div class="p-3 bg-slate-50 border-l-4 border-amber-500 rounded-r-xl text-xs font-semibold text-slate-800 shadow-2xs text-left mb-2">
                                        <div class="leading-relaxed">
                                            "{{ $rec->tanggapan_orang_tua }}"
                                        </div>
                                        <div class="mt-1.5 pt-1.5 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-500 font-bold">
                                            <span>Oleh: {{ $rec->dikirim_oleh_nama ?: 'Orang Tua' }}</span>
                                            <span>{{ $rec->tanggal_tanggapan ? $rec->tanggal_tanggapan->diffForHumans() : '' }}</span>
                                        </div>
                                    </div>
                                    <button wire:click="openFeedbackModal({{ $rec->id }})" class="w-full py-1.5 px-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-bold text-xs shadow-xs transition flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit Tanggapan</span>
                                    </button>
                                @else
                                    <button wire:click="openFeedbackModal({{ $rec->id }})" class="w-full py-2 px-3 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-bold text-xs shadow-xs transition flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                        <span>Beri Tanggapan Orang Tua</span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <h3 class="text-sm font-bold text-slate-800">Belum ada catatan setoran mutaba'ah untuk semester ini.</h3>
                                <p class="text-xs font-medium text-slate-500 mt-0.5">Ustadz pembimbing akan memasukkan catatan hafalan harian/mingguan ananda di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Feedback Orang Tua -->
    @if($showFeedbackModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-300 overflow-hidden transform transition-all">
                <div class="px-5 py-4 bg-emerald-800 text-white flex items-center justify-between">
                    <h3 class="font-bold text-base flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Tanggapan Orang Tua / Wali Santri</span>
                    </h3>
                    <button wire:click="closeFeedbackModal" class="text-emerald-200 hover:text-white transition p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form wire:submit.prevent="submitFeedback" class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Nama Pengirim / Orang Tua <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" wire:model="dikirim_oleh_nama" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-600" placeholder="Misal: Ayahanda / Ibunda Ahmad">
                        @error('dikirim_oleh_nama') <span class="text-rose-600 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Pesan Tanggapan / Feedback Untuk Ustadz <span class="text-rose-600">*</span>
                        </label>
                        <textarea wire:model="tanggapan_orang_tua" rows="4" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:ring-2 focus:ring-emerald-600 leading-relaxed" placeholder="Tuliskan perkembangan muraja'ah di rumah, pesan khusus, atau pertanyaan kepada Ustadz pembimbing..."></textarea>
                        @error('tanggapan_orang_tua') <span class="text-rose-600 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-200">
                        <button type="button" wire:click="closeFeedbackModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold text-xs transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Kirimkan Tanggapan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
