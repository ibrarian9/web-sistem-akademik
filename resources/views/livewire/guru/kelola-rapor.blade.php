<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Terbitkan & Kelola Rapor</h2>
        <p class="text-xs text-slate-500">Kalkulasi nilai akhir, tulis catatan wali kelas, dan terbitkan rapor hasil belajar siswa.</p>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-start gap-3">
            <x-lucide-check-circle class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" />
            <div>
                <h4 class="text-xs font-bold text-emerald-400">Berhasil</h4>
                <p class="text-[11px] text-slate-300 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl flex items-start gap-3">
            <x-lucide-alert-triangle class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" />
            <div>
                <h4 class="text-xs font-bold text-rose-400">Error</h4>
                <p class="text-[11px] text-slate-300 mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if (count($myClasses) === 0)
        <!-- Access Locked / Not a Wali Kelas -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-12 text-center max-w-2xl mx-auto space-y-4 shadow-xl">
            <div class="w-16 h-16 bg-rose-500/10 border border-rose-500/20 rounded-full flex items-center justify-center mx-auto text-rose-400">
                <x-lucide-shield-alert class="w-8 h-8" />
            </div>
            <div class="space-y-2">
                <h3 class="text-base font-extrabold text-white uppercase tracking-wider">Akses Kelola Rapor Terkunci</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Anda tidak terdaftar sebagai **Wali Kelas** (Guru Umum atau Guru Tahfidz) untuk kelas manapun pada semester aktif ini. Menu ini hanya dapat diakses oleh guru yang ditugaskan menjadi Wali Kelas.
                </p>
            </div>
        </div>
    @else
        <!-- Selection Bar -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Kelas -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas Perwalian</label>
                    <select wire:model.live="kelasId" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                        @foreach ($myClasses as $c)
                            <option value="{{ $c->id }}">Kelas {{ $c->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Siswa -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pilih Siswa</label>
                    <select wire:model.live="siswaId" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                        @foreach ($students as $siswa)
                            <option value="{{ $siswa->id }}">{{ $siswa->user->nama }} (NIS: {{ $siswa->nis }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal Terbit -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Terbit Rapor</label>
                    <input wire:model.live="tanggalTerbit" type="date" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
                </div>
            </div>
        </div>

        @if ($siswaId && $activeSemester)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Panel: Rapor Settings & Comments -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Rapor</h3>
                            @if ($existingRapor)
                                <span class="px-2.5 py-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-lg text-[9px] font-bold uppercase tracking-wider">Sudah Terbit</span>
                            @else
                                <span class="px-2.5 py-1 bg-amber-500/10 border border-amber-500/20 text-amber-400 rounded-lg text-[9px] font-bold uppercase tracking-wider">Belum Terbit</span>
                            @endif
                        </div>

                        <!-- Info details -->
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between text-slate-400">
                                <span>Semester:</span>
                                <span class="text-white font-bold">Semester {{ ucfirst($activeSemester->semester) }}</span>
                            </div>
                            <div class="flex justify-between text-slate-400">
                                <span>Tahun Ajaran:</span>
                                <span class="text-white font-bold">{{ $activeSemester->tahunAjaran->nama ?? '-' }}</span>
                            </div>
                            @if ($existingRapor)
                                <div class="flex justify-between text-slate-400">
                                    <span>Terbit Pertama:</span>
                                    <span class="text-white font-bold">{{ date('d-m-Y', strtotime($existingRapor->created_at)) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Catatan Wali Kelas Form -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catatan Wali Kelas</label>
                            <textarea wire:model="catatanWaliKelas" rows="5" 
                                class="w-full px-3 py-2 bg-slate-950/60 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 resize-none placeholder-slate-600" 
                                placeholder="Tulis umpan balik, motivasi, atau evaluasi perkembangan belajar ananda selama semester ini..."></textarea>
                            @error('catatanWaliKelas')
                                <span class="text-rose-400 text-[10px] block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Publish Button -->
                        <div class="pt-2 border-t border-slate-800">
                            <button wire:click="publishRapor" 
                                class="w-full py-3 px-6 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition duration-200 shadow-lg shadow-indigo-600/10 flex items-center justify-center gap-2">
                                <x-lucide-send class="w-4 h-4" />
                                <span>{{ $existingRapor ? 'Perbarui Rapor' : 'Terbitkan Rapor Resmi' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Grade Calculation Preview -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
                        <div>
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Preview Nilai Rapor</h3>
                            <p class="text-[10px] text-slate-500">Nilai di bawah ini dihitung berdasarkan rumus pembobotan komponen nilai pengampu mata pelajaran.</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-800 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                        <th class="pb-3 w-10 text-center">No</th>
                                        <th class="pb-3 w-48">Mata Pelajaran</th>
                                        <th class="pb-3 text-center">Pengetahuan</th>
                                        <th class="pb-3 text-center">Keterampilan</th>
                                        <th class="pb-3 text-center">Keagamaan</th>
                                        <th class="pb-3 text-center">Sikap</th>
                                        <th class="pb-3 text-center w-24 bg-slate-950/20 font-extrabold text-indigo-400">Nilai Akhir</th>
                                        <th class="pb-3 text-center w-16 bg-slate-950/40">Predikat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-850 text-xs">
                                    @forelse ($this->calculatedPreviewGrades as $index => $grade)
                                        <tr class="hover:bg-slate-950/20">
                                            <td class="py-3 text-center text-slate-500 font-bold">{{ $index + 1 }}</td>
                                            <td class="py-3 font-bold text-white">{{ $grade['nama_mapel'] }}</td>
                                            <td class="py-3 text-center text-slate-300 font-semibold">{{ $grade['nilai_pengetahuan'] ?? '-' }}</td>
                                            <td class="py-3 text-center text-slate-300 font-semibold">{{ $grade['nilai_keterampilan'] ?? '-' }}</td>
                                            <td class="py-3 text-center text-slate-300 font-semibold">{{ $grade['nilai_keagamaan'] ?? '-' }}</td>
                                            <td class="py-3 text-center text-slate-300 font-semibold">{{ $grade['nilai_sikap'] ?? '-' }}</td>
                                            <td class="py-3 text-center bg-slate-950/20 text-indigo-400 font-extrabold text-sm">{{ $grade['nilai_akhir'] }}</td>
                                            <td class="py-3 text-center bg-slate-950/40 font-extrabold">
                                                @php
                                                    $predClass = '';
                                                    if ($grade['predikat'] === 'A') $predClass = 'text-green-400';
                                                    elseif ($grade['predikat'] === 'B') $predClass = 'text-blue-400';
                                                    elseif ($grade['predikat'] === 'C') $predClass = 'text-amber-400';
                                                    elseif ($grade['predikat'] === 'D') $predClass = 'text-yellow-500';
                                                    else $predClass = 'text-rose-500';
                                                @endphp
                                                <span class="{{ $predClass }}">{{ $grade['predikat'] }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="py-12 text-center text-slate-500 font-semibold">
                                                Belum ada nilai atau mata pelajaran yang terisi untuk siswa ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 bg-slate-950/40 border border-slate-800/80 rounded-2xl text-[10px] text-slate-500 space-y-1.5 leading-relaxed">
                            <p class="font-bold text-slate-400">💡 Informasi Penting Penerbitan Rapor:</p>
                            <p>1. **Snapshot Data**: Penerbitan rapor akan menyimpan snapshot nilai akhir siswa saat ini. Perubahan nilai harian setelah rapor terbit tidak akan secara otomatis memperbarui rapor resmi kecuali Wali Kelas mengklik **"Perbarui Rapor"**.</p>
                            <p>2. **Notifikasi Otomatis**: Murid akan secara otomatis menerima notifikasi in-app ketika rapor resmi diterbitkan.</p>
                            <p>3. **Akses Murid**: Murid yang memiliki tagihan blocking yang belum lunas tidak akan dapat melihat rapor nilai ini di portal mereka sampai status keuangannya diselesaikan.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
