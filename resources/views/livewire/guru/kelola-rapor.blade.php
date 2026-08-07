<div class="space-y-6 font-sans">
    <!-- Quick Module Switcher Header (Light Theme) -->
    <div class="flex items-center gap-2 bg-white border border-stone-200 p-2 rounded-2xl overflow-x-auto shadow-xs">
        @if($guruJenis === 'umum' || $guruJenis === 'keduanya' || auth()->user()->role?->nama !== 'guru')
            <a href="{{ route('guru.kurikulum-merdeka') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 01-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Setup Bab &amp; TP</span>
            </a>
            <a href="{{ route('guru.input-sumatif') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Nilai Sumatif</span>
            </a>
        @endif

        @if($guruJenis === 'tahfizh' || $guruJenis === 'keduanya' || auth()->user()->role?->nama !== 'guru')
            <a href="{{ route('guru.input-tahfidz') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Setoran Tahfizh</span>
            </a>
        @endif

        @if($guruJenis === 'umum' || $guruJenis === 'keduanya' || auth()->user()->role?->nama !== 'guru')
            <a href="{{ route('guru.penilaian-p5') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                <span>Penilaian P5</span>
            </a>
        @endif

        <a href="{{ route('guru.kelola-rapor') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-700 text-white shadow-sm flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Rapor Murid</span>
        </a>
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Lihat, Pratinjau & Terbitkan Rapor Murid"
        :steps="[
            ['title' => 'Pilih Kelas & Murid Target', 'desc' => 'Pilih kelas perwalian / halaqah dan murid yang ingin dilihat atau diterbitkan rapor hasil belajarnya.'],
            ['title' => 'Tulis Catatan Wali Kelas / Guru Tahfizh', 'desc' => 'Tuliskan evaluasi perkembangan hafalan, sikap, dan motivasi belajar siswa pada kolom catatan.'],
            ['title' => 'Pratinjau PDF & Terbitkan', 'desc' => 'Klik Pratinjau Rapor PDF (Tab Baru) untuk melihat dokumen A4 resmi, lalu terbitkan ke portal murid.']
        ]"
        notes="Tampilan rapor disesuaikan secara otomatis sesuai dengan jenis guru (Guru Umum atau Guru Tahfizh)."
    />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-stone-200 p-6 rounded-2xl shadow-sm">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                Portal Rapor Digital Guru
            </span>
            <h2 class="text-2xl font-extrabold text-stone-900 tracking-tight flex items-center gap-2">
                <span>Lihat &amp; Cetak Rapor {{ $tipeRapor === 'tahfizh' ? 'Tahfizh Al-Qur\'an' : 'Akademik Murid' }}</span>
            </h2>
            <p class="text-xs text-stone-600 font-semibold">Kalkulasi nilai akhir, pratinjau hasil rapor, cetak dokumen PDF resmi, dan terbitkan ke portal murid.</p>
        </div>

        <div class="flex items-center gap-2">
            @if ($guruJenis === 'keduanya' || auth()->user()->role?->nama !== 'guru')
                <!-- Dual-role teacher toggle -->
                <div class="p-1 bg-stone-100 border border-stone-200 rounded-xl flex items-center gap-1">
                    <button type="button" wire:click="$set('tipeRapor', 'umum')" class="px-3.5 py-2 rounded-lg text-xs font-bold transition {{ $tipeRapor === 'umum' ? 'bg-emerald-700 text-white shadow-sm' : 'text-stone-600 hover:text-stone-800' }}">
                        Rapor Umum
                    </button>
                    <button type="button" wire:click="$set('tipeRapor', 'tahfizh')" class="px-3.5 py-2 rounded-lg text-xs font-bold transition {{ $tipeRapor === 'tahfizh' ? 'bg-emerald-700 text-white shadow-sm' : 'text-stone-600 hover:text-stone-800' }}">
                        Rapor Tahfizh
                    </button>
                </div>
            @elseif ($guruJenis === 'tahfizh')
                <span class="px-3.5 py-2 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-xl text-xs font-bold flex items-center gap-2 shadow-xs">
                    <x-lucide-sparkles class="w-4 h-4 text-emerald-700" />
                    <span>Rapor Khusus Tahfizh Al-Qur'an</span>
                </span>
            @else
                <span class="px-3.5 py-2 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-xl text-xs font-bold flex items-center gap-2 shadow-xs">
                    <x-lucide-award class="w-4 h-4 text-emerald-700" />
                    <span>Rapor Umum / Akademik</span>
                </span>
            @endif
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-xl text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <x-lucide-check-circle class="w-5 h-5 text-emerald-600" />
                <span>{{ session('success') }}</span>
            </div>
            <span class="px-2.5 py-0.5 bg-emerald-200 text-emerald-900 rounded font-black text-[10px]">Tersimpan</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-300 text-rose-800 rounded-xl text-xs font-bold flex items-center gap-2 shadow-xs">
            <x-lucide-alert-triangle class="w-5 h-5 text-rose-600" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if (count($myClasses) === 0)
        <!-- Access Locked / Not a Wali Kelas -->
        <div class="bg-white border border-stone-200 rounded-2xl p-12 text-center max-w-2xl mx-auto space-y-4 shadow-sm">
            <div class="w-16 h-16 bg-rose-50 border border-rose-200 rounded-full flex items-center justify-center mx-auto text-rose-600">
                <x-lucide-shield-alert class="w-8 h-8" />
            </div>
            <div class="space-y-2">
                <h3 class="text-base font-extrabold text-stone-800 uppercase tracking-wider">Akses Kelola Rapor Terkunci</h3>
                <p class="text-xs text-stone-500 leading-relaxed">
                    Anda belum terdaftar sebagai pengampu kelas atau Wali Kelas / Guru Tahfizh pada semester aktif ini.
                </p>
            </div>
        </div>
    @else
        <!-- Selection Bar -->
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Kelas -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-stone-700 uppercase tracking-wider flex items-center gap-1.5">
                        <x-lucide-school class="w-3.5 h-3.5 text-emerald-600" />
                        <span>Kelas / Halaqah</span>
                    </label>
                    <select wire:model.live="kelasId" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                        @foreach ($myClasses as $c)
                            <option value="{{ $c->id }}">Kelas {{ $c->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Siswa -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-stone-700 uppercase tracking-wider flex items-center gap-1.5">
                        <x-lucide-user class="w-3.5 h-3.5 text-emerald-600" />
                        <span>Pilih Murid Target</span>
                    </label>
                    <select wire:model.live="siswaId" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                        @foreach ($students as $siswa)
                            <option value="{{ $siswa->id }}">{{ $siswa->user->nama ?? $siswa->nama_panggilan }} (NISN: {{ $siswa->nisn }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal Terbit -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-stone-700 uppercase tracking-wider flex items-center gap-1.5">
                        <x-lucide-calendar class="w-3.5 h-3.5 text-emerald-600" />
                        <span>Tanggal Terbit Rapor</span>
                    </label>
                    <input wire:model.live="tanggalTerbit" type="date" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                </div>
            </div>
        </div>

        @if ($siswaId && $activeSemester)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Panel: Rapor Settings & Comments -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                            <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider flex items-center gap-1.5">
                                <x-lucide-info class="w-4 h-4 text-emerald-600" />
                                <span>Status Publikasi Rapor</span>
                            </h3>
                            @if ($existingRapor)
                                <span class="px-2.5 py-1 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-lg text-[10px] font-bold uppercase tracking-wider flex items-center gap-1">
                                    <x-lucide-check-circle class="w-3 h-3 text-emerald-600" />
                                    <span>Sudah Terbit</span>
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-amber-50 border border-amber-300 text-amber-900 rounded-lg text-[10px] font-bold uppercase tracking-wider flex items-center gap-1">
                                    <x-lucide-clock class="w-3 h-3 text-amber-600" />
                                    <span>Belum Terbit</span>
                                </span>
                            @endif
                        </div>

                        <!-- Info details -->
                        <div class="space-y-2.5 text-xs">
                            <div class="flex justify-between text-stone-500">
                                <span class="flex items-center gap-1"><x-lucide-file-text class="w-3.5 h-3.5 text-stone-400" /> Jenis Rapor:</span>
                                <span class="text-stone-900 font-bold uppercase">{{ $tipeRapor === 'tahfizh' ? 'Rapor Tahfizh' : 'Rapor Umum' }}</span>
                            </div>
                            <div class="flex justify-between text-stone-500">
                                <span class="flex items-center gap-1"><x-lucide-calendar class="w-3.5 h-3.5 text-stone-400" /> Semester:</span>
                                <span class="text-stone-900 font-bold">Semester {{ ucfirst($activeSemester->semester) }}</span>
                            </div>
                            <div class="flex justify-between text-stone-500">
                                <span class="flex items-center gap-1"><x-lucide-award class="w-3.5 h-3.5 text-stone-400" /> Tahun Ajaran:</span>
                                <span class="text-stone-900 font-bold">{{ $activeSemester->tahunAjaran->nama ?? '-' }}</span>
                            </div>
                            @if ($existingRapor)
                                <div class="flex justify-between text-stone-500">
                                    <span class="flex items-center gap-1"><x-lucide-clock class="w-3.5 h-3.5 text-stone-400" /> Terbit Pertama:</span>
                                    <span class="text-stone-900 font-bold">{{ date('d-m-Y', strtotime($existingRapor->created_at)) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Catatan Wali Kelas / Ustadz Tahfizh Form -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-stone-700 uppercase tracking-wider flex items-center gap-1.5">
                                <x-lucide-pen-tool class="w-3.5 h-3.5 text-emerald-600" />
                                <span>Catatan {{ $tipeRapor === 'tahfizh' ? 'Guru Tahfizh' : 'Wali Kelas' }}</span>
                            </label>
                            <textarea wire:model="catatanWaliKelas" rows="4" 
                                class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 resize-none placeholder-stone-400" 
                                placeholder="Tulis catatan hafalan Al-Qur'an, motivasi, atau evaluasi hasil belajar ananda..."></textarea>
                            @error('catatanWaliKelas')
                                <span class="text-rose-600 text-[10px] block font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Action Buttons: Print PDF & Publish -->
                        <div class="pt-2 border-t border-stone-100 space-y-2.5">
                            @if($tipeRapor === 'tahfizh')
                                <a href="{{ route('rapor.pdf-tahfidz.preview', ['siswaId' => $siswaId]) }}" target="_blank" 
                                    class="w-full py-3 px-6 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition duration-150 shadow-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.5 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Pratinjau Rapor Tahfizh PDF (Tab Baru)</span>
                                </a>
                            @else
                                <a href="{{ route('rapor.pdf.preview', ['siswaId' => $siswaId]) }}" target="_blank" 
                                    class="w-full py-3 px-6 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition duration-150 shadow-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.5 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Pratinjau Rapor PDF (Tab Baru)</span>
                                </a>
                            @endif

                            <button wire:click="publishRapor" 
                                class="w-full py-3 px-6 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-xs font-bold transition duration-150 shadow-sm flex items-center justify-center gap-2">
                                <x-lucide-send class="w-4 h-4" />
                                <span>{{ $existingRapor ? 'Perbarui Rapor Resmi' : 'Terbitkan Rapor Resmi' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Grade Calculation Preview -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                            <div>
                                <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-1.5">
                                    <x-lucide-eye class="w-4 h-4 text-emerald-600" />
                                    <span>Pratinjau Hasil Rapor {{ $tipeRapor === 'tahfizh' ? 'Tahfizh Al-Qur\'an' : 'Akademik Murid' }}</span>
                                </h3>
                                <p class="text-[10px] text-stone-500 font-semibold">Tabel kalkulasi resmi {{ $tipeRapor === 'tahfizh' ? 'Mutaba\'ah Tahfizh' : 'Kurikulum Merdeka' }}.</p>
                            </div>
                            @if($tipeRapor === 'tahfizh')
                                <a href="{{ route('rapor.pdf-tahfidz.preview', ['siswaId' => $siswaId]) }}" target="_blank" class="px-3.5 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 border border-emerald-300 rounded-xl text-xs font-bold transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.5 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Pratinjau PDF
                                </a>
                            @else
                                <a href="{{ route('rapor.pdf.preview', ['siswaId' => $siswaId]) }}" target="_blank" class="px-3.5 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 border border-emerald-300 rounded-xl text-xs font-bold transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.5 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Pratinjau PDF
                                </a>
                            @endif
                        </div>

                        @if($tipeRapor === 'tahfizh')
                            <!-- Tahfizh Report Preview Matrix -->
                            @php
                                $tfData = $this->calculatedTahfidzPreview;
                                $tfRec = $tfData['record'] ?? null;
                                $tfDetail = $tfData['detail'] ?? null;
                            @endphp
                            <div class="space-y-4">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead>
                                            <tr class="border-b border-emerald-900 text-[10px] font-extrabold text-white uppercase tracking-wider bg-emerald-800">
                                                <th class="p-3 w-12 text-center border-r border-emerald-700">No</th>
                                                <th class="p-3 min-w-[160px] border-r border-emerald-700">Aspek Evaluasi Tahfizh</th>
                                                <th class="p-3 min-w-[220px] border-r border-emerald-700">Materi / Surah / Ayat</th>
                                                <th class="p-3 text-center w-24 bg-emerald-900 font-black text-amber-300">Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-stone-200 text-xs text-stone-800">
                                            <tr>
                                                <td class="p-3 text-center font-bold text-stone-500 border-r border-stone-200">1</td>
                                                <td class="p-3 font-bold text-stone-900 border-r border-stone-200">Tahsin Al-Qur'an</td>
                                                <td class="p-3 border-r border-stone-200">{{ $tfRec?->materi_tahsin ?? 'Al-Baqarah (4-5)' }}</td>
                                                <td class="p-3 text-center bg-emerald-50 font-black text-emerald-950">{{ ($tfRec && $tfRec->nilai_tahsin !== null) ? round($tfRec->nilai_tahsin) : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="p-3 text-center font-bold text-stone-500 border-r border-stone-200" rowspan="2">2</td>
                                                <td class="p-3 font-bold text-stone-900 border-r border-stone-200">Muraja'ah Bersama</td>
                                                <td class="p-3 border-r border-stone-200">{{ $tfRec?->murajaah_bersama ?? 'Juz 30' }}</td>
                                                <td class="p-3 text-center bg-emerald-50 font-black text-emerald-950" rowspan="2">{{ ($tfRec && $tfRec->nilai_murajaah !== null) ? round($tfRec->nilai_murajaah) : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="p-3 font-bold text-stone-900 border-r border-stone-200">Muraja'ah Mandiri</td>
                                                <td class="p-3 border-r border-stone-200">{{ $tfRec?->murajaah_mandiri ?? 'Al-Baqarah (1-30)' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="p-3 text-center font-bold text-stone-500 border-r border-stone-200">3</td>
                                                <td class="p-3 font-bold text-stone-900 border-r border-stone-200">Tahfizh - Kitabah</td>
                                                <td class="p-3 border-r border-stone-200">{{ $tfRec?->materi_kitabah ?? 'Al-Baqarah (39-40)' }}</td>
                                                <td class="p-3 text-center bg-emerald-50 font-black text-emerald-950">{{ ($tfRec && $tfRec->nilai_kitabah !== null) ? round($tfRec->nilai_kitabah) : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="p-3 text-center font-bold text-stone-500 border-r border-stone-200">4</td>
                                                <td class="p-3 font-bold text-stone-900 border-r border-stone-200">Tahfizh - Ziyadah</td>
                                                <td class="p-3 border-r border-stone-200">{{ $tfRec?->materi_ziyadah ?? 'Al-Baqarah (39-40)' }}</td>
                                                <td class="p-3 text-center bg-emerald-50 font-black text-emerald-950">{{ ($tfRec && $tfRec->nilai_ziyadah !== null) ? round($tfRec->nilai_ziyadah) : '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-1">
                                        <span class="text-[10px] font-bold text-emerald-800 uppercase block">Total Capaian Hafalan:</span>
                                        <span class="text-xs font-extrabold text-emerald-950">{{ $tfDetail->total_juz_dihafal ?? 1 }} Juz (Surah: {{ $tfDetail->daftar_surah_lulus ?? 'Al-Baqarah' }})</span>
                                    </div>
                                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-1">
                                        <span class="text-[10px] font-bold text-emerald-800 uppercase block">Predikat Keagamaan:</span>
                                        <span class="text-xs font-extrabold text-emerald-950">{{ $tfRec?->predikat_keagamaan ?? 'Sangat Baik' }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- General Kurikulum Merdeka Report Preview Matrix -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="border-b border-emerald-900 text-[10px] font-extrabold text-white uppercase tracking-wider bg-emerald-800">
                                            <th class="p-3 w-12 text-center border-r border-emerald-700">No</th>
                                            <th class="p-3 min-w-[180px] border-r border-emerald-700">Mata Pelajaran</th>
                                            <th class="p-3 text-center w-24 border-r border-emerald-700 bg-emerald-900 font-black text-amber-300">Nilai Akhir</th>
                                            <th class="p-3 min-w-[320px]">Capaian Kompetensi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-stone-200 text-xs text-stone-800">
                                        @forelse ($this->calculatedPreviewGrades as $index => $grade)
                                            <tr class="hover:bg-stone-50/50">
                                                <td class="p-3 text-center text-stone-500 font-bold border-r border-stone-200">{{ $index + 1 }}</td>
                                                <td class="p-3 font-bold text-stone-900 border-r border-stone-200">
                                                    <span>{{ $grade['nama_mapel'] }}</span>
                                                    <span class="text-[9px] text-stone-500 block font-normal uppercase">Kategori: {{ $grade['jenis_mapel'] }}</span>
                                                </td>
                                                <td class="p-3 text-center bg-emerald-50 text-emerald-950 font-black text-sm border-r border-stone-200">
                                                    {{ round($grade['nilai_akhir']) }}
                                                </td>
                                                <td class="p-3 text-stone-700 leading-relaxed text-xs">
                                                    @if(!empty($grade['deskripsi_tertinggi']))
                                                        <p class="mb-1">Ananda {{ strtolower($students->firstWhere('id', $siswaId)->user->nama ?? 'siswa') }} {{ $grade['deskripsi_tertinggi'] }}</p>
                                                    @endif
                                                    @if(!empty($grade['deskripsi_terendah']))
                                                        <p>Ananda {{ strtolower($students->firstWhere('id', $siswaId)->user->nama ?? 'siswa') }} {{ $grade['deskripsi_terendah'] }}</p>
                                                    @endif
                                                    @if(empty($grade['deskripsi_tertinggi']) && empty($grade['deskripsi_terendah']))
                                                        <p>{{ $grade['narasi_capaian_full'] ?: 'Ananda menunjukkan penguasaan kompetensi mata pelajaran dengan sangat baik.' }}</p>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="p-12 text-center text-stone-400 font-semibold text-xs">
                                                    <x-lucide-file-x class="w-8 h-8 text-stone-300 mx-auto mb-2" />
                                                    <span>Belum ada nilai terisi untuk kelompok mata pelajaran {{ $guruJenis === 'tahfizh' ? 'Tahfizh' : 'Umum' }}.</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl text-[11px] text-stone-600 space-y-1.5 leading-relaxed">
                            <p class="font-bold text-stone-800 flex items-center gap-1.5">
                                <x-lucide-lightbulb class="w-4 h-4 text-emerald-600" />
                                <span>Informasi Rapor SD TAHFIZH F3:</span>
                            </p>
                            @if($tipeRapor === 'tahfizh')
                                <p>1. Tabel Rapor Tahfizh Al-Qur'an menyajikan evaluasi Tahsin, Muraja'ah, Kitabah, Ziyadah, serta Total Capaian Hafalan.</p>
                                <p>2. Pratinjau PDF: Klik "Pratinjau Rapor Tahfizh PDF" untuk melihat lembar A4 resmi lengkap dengan tanda tangan pengesahan.</p>
                            @else
                                <p>1. Tabel Rapor Akademik menyajikan Nilai Akhir (Angka Bulat) dan Narasi Auto-Capaian Kompetensi (Tertinggi &amp; Terendah) tanpa kolom Predikat.</p>
                                <p>2. Pratinjau PDF: Klik tombol "Pratinjau Rapor PDF" untuk melihat layout A4 resmi lengkap dengan Kokurikuler P5, Ekskul, Absensi, dan Keputusan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
