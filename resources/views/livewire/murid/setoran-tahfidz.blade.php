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
        <x-table loadingTarget="semester_id">
            <thead class="bg-emerald-800 text-white font-extrabold text-[11px] uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th align="center" class="w-12">No</x-table.th>
                    <x-table.th class="min-w-[160px]">Tahsin Al-Qur'an</x-table.th>
                    <x-table.th class="min-w-[180px]">Muraja'ah (Bersama &amp; Mandiri)</x-table.th>
                    <x-table.th class="min-w-[150px]">Kitabah (Menulis)</x-table.th>
                    <x-table.th class="min-w-[160px]">Ziyadah (Hafalan Baru)</x-table.th>
                    <x-table.th class="min-w-[200px]">Catatan Ustadz</x-table.th>
                    <x-table.th align="center" class="min-w-[220px]">Tanggapan Orang Tua / Wali</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($setoranList as $index => $rec)
                    <tr class="hover:bg-slate-50 transition">
                        <!-- No -->
                        <td class="p-3.5 text-center font-bold text-slate-600 border-r border-slate-200 text-xs bg-slate-50">
                            {{ $index + 1 }}
                        </td>

                        <!-- Tahsin -->
                        <td class="p-3.5 border-r border-slate-200 align-top">
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
                        <td class="p-3.5 border-r border-slate-200 align-top">
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
                        <td class="p-3.5 border-r border-slate-200 align-top">
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
                        <td class="p-3.5 border-r border-slate-200 align-top">
                            <div class="font-bold text-slate-900 text-xs leading-relaxed">
                                {{ $rec->materi_ziyadah ?: '-' }}
                            </div>
                            <div class="mt-1.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                    <span>Nilai:</span>
                                    <span class="font-extrabold text-amber-800">{{ $rec->nilai_ziyadah !== null ? round($rec->nilai_ziyadah) : '-' }}</span>
                                </span>
                            </div>
                        </td>

                        <!-- Catatan Ustadz -->
                        <td class="p-3.5 border-r border-slate-200 align-top">
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
                        <td class="p-3.5 align-top text-center">
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
                                <x-button variant="outline" size="xs" icon="edit" wire:click="openFeedbackModal({{ $rec->id }})" class="w-full">
                                    Edit Tanggapan
                                </x-button>
                            @else
                                <x-button variant="primary" size="xs" icon="message-square" wire:click="openFeedbackModal({{ $rec->id }})" class="w-full">
                                    Beri Tanggapan
                                </x-button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="7" title="Belum ada catatan setoran mutaba'ah" message="Ustadz pembimbing akan memasukkan catatan hafalan harian/mingguan ananda di sini." />
                @endforelse
            </tbody>
        </x-table>
    </div>

    <!-- Modal Feedback Orang Tua -->
    <x-floating-card 
        :show="$showFeedbackModal"
        title="Tanggapan Orang Tua / Wali Santri"
        subtitle="Berikan feedback atau catatan perkembangan hafalan ananda di rumah."
        badge="MUTABA'AH"
        badgeVariant="emerald"
        icon="message-square"
        maxWidth="max-w-md"
        closeAction="closeFeedbackModal"
    >
        <form wire:submit.prevent="submitFeedback" class="space-y-4 text-xs">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                    Nama Pengirim / Orang Tua <span class="text-rose-600">*</span>
                </label>
                <input type="text" wire:model="dikirim_oleh_nama" class="w-full px-3.5 py-2 bg-stone-50 border border-stone-300 rounded-xl text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-xs" placeholder="Misal: Ayahanda / Ibunda Ahmad">
                @error('dikirim_oleh_nama') <span class="text-rose-600 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                    Pesan Tanggapan / Feedback Untuk Ustadz <span class="text-rose-600">*</span>
                </label>
                <textarea wire:model="tanggapan_orang_tua" rows="4" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-xs font-medium text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white leading-relaxed shadow-xs" placeholder="Tuliskan perkembangan muraja'ah di rumah, pesan khusus, atau pertanyaan kepada Ustadz pembimbing..."></textarea>
                @error('tanggapan_orang_tua') <span class="text-rose-600 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-stone-200">
                <x-button type="button" variant="secondary" size="md" wire:click="closeFeedbackModal">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="send" loadingTarget="submitFeedback">
                    Kirimkan Tanggapan
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
