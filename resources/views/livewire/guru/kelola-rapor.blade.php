<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight flex items-center gap-2">
                <x-lucide-book-open class="w-6 h-6 text-indigo-600" />
                <span>Terbitkan &amp; Cetak Rapor {{ $tipeRapor === 'tahfizh' ? 'Tahfizh Al-Qur\'an' : 'Akademik Umum' }}</span>
            </h2>
            <p class="text-xs text-stone-500">Kalkulasi nilai akhir, tulis catatan wali kelas, dan terbitkan rapor hasil belajar siswa.</p>
        </div>

        <div class="flex items-center gap-2">
            @if ($guruJenis === 'keduanya' || auth()->user()->role?->nama !== 'guru')
                <!-- Dual-role teacher toggle -->
                <div class="p-1 bg-stone-100 border border-stone-200 rounded-xl flex items-center gap-1">
                    <button type="button" wire:click="$set('tipeRapor', 'umum')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $tipeRapor === 'umum' ? 'bg-indigo-600 text-white shadow-sm' : 'text-stone-600 hover:text-stone-800' }}">
                        Rapor Umum
                    </button>
                    <button type="button" wire:click="$set('tipeRapor', 'tahfizh')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $tipeRapor === 'tahfizh' ? 'bg-emerald-600 text-white shadow-sm' : 'text-stone-600 hover:text-stone-800' }}">
                        Rapor Tahfizh
                    </button>
                </div>
            @elseif ($guruJenis === 'tahfizh')
                <span class="px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm">
                    <x-lucide-sparkles class="w-4 h-4 text-emerald-600" />
                    <span>Akses Khusus: Rapor Tahfizh Al-Qur'an</span>
                </span>
            @else
                <span class="px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm">
                    <x-lucide-award class="w-4 h-4 text-indigo-600" />
                    <span>Akses Khusus: Rapor Umum / Akademik</span>
                </span>
            @endif
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3">
            <x-lucide-check-circle class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
            <div>
                <h4 class="text-xs font-bold text-emerald-800">Berhasil</h4>
                <p class="text-[11px] text-emerald-700 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-3">
            <x-lucide-alert-triangle class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" />
            <div>
                <h4 class="text-xs font-bold text-rose-800">Error</h4>
                <p class="text-[11px] text-rose-700 mt-0.5">{{ session('error') }}</p>
            </div>
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
                    Anda belum terdaftar sebagai pengampu kelas atau Wali Kelas pada semester aktif ini. Menu penerbitan rapor hanya dapat diakses oleh guru pengampu/wali kelas terdaftar.
                </p>
            </div>
        </div>
    @else
        <!-- Selection Bar -->
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Kelas -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-stone-600 uppercase tracking-wider flex items-center gap-1.5">
                        <x-lucide-school class="w-3.5 h-3.5 text-indigo-600" />
                        <span>Kelas Perwalian</span>
                    </label>
                    <select wire:model.live="kelasId" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs font-bold focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                        @foreach ($myClasses as $c)
                            <option value="{{ $c->id }}">Kelas {{ $c->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Siswa -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-stone-600 uppercase tracking-wider flex items-center gap-1.5">
                        <x-lucide-user class="w-3.5 h-3.5 text-indigo-600" />
                        <span>Pilih Siswa</span>
                    </label>
                    <select wire:model.live="siswaId" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs font-bold focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                        @foreach ($students as $siswa)
                            <option value="{{ $siswa->id }}">{{ $siswa->user->nama }} (NIS: {{ $siswa->nis }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal Terbit -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-stone-600 uppercase tracking-wider flex items-center gap-1.5">
                        <x-lucide-calendar class="w-3.5 h-3.5 text-indigo-600" />
                        <span>Tanggal Terbit Rapor</span>
                    </label>
                    <input wire:model.live="tanggalTerbit" type="date" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs font-bold focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
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
                                <x-lucide-info class="w-4 h-4 text-indigo-600" />
                                <span>Status Rapor</span>
                            </h3>
                            @if ($existingRapor)
                                <span class="px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-[10px] font-bold uppercase tracking-wider flex items-center gap-1">
                                    <x-lucide-check-circle class="w-3 h-3 text-emerald-600" />
                                    <span>Sudah Terbit</span>
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg text-[10px] font-bold uppercase tracking-wider flex items-center gap-1">
                                    <x-lucide-clock class="w-3 h-3 text-amber-600" />
                                    <span>Belum Terbit</span>
                                </span>
                            @endif
                        </div>

                        <!-- Info details -->
                        <div class="space-y-2.5 text-xs">
                            <div class="flex justify-between text-stone-500">
                                <span class="flex items-center gap-1"><x-lucide-file-text class="w-3.5 h-3.5 text-stone-400" /> Jenis Rapor:</span>
                                <span class="text-stone-900 font-bold uppercase">{{ $guruJenis === 'tahfizh' ? 'Rapor Tahfizh' : 'Rapor Umum' }}</span>
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

                        <!-- Catatan Wali Kelas Form -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-stone-700 uppercase tracking-wider flex items-center gap-1.5">
                                <x-lucide-pen-tool class="w-3.5 h-3.5 text-indigo-600" />
                                <span>Catatan {{ $guruJenis === 'tahfizh' ? 'Guru Tahfizh' : 'Wali Kelas' }}</span>
                            </label>
                            <textarea wire:model="catatanWaliKelas" rows="4" 
                                class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 resize-none placeholder-stone-400" 
                                placeholder="Tulis catatan hafalan Al-Qur'an, motivasi, atau evaluasi hasil belajar ananda..."></textarea>
                            @error('catatanWaliKelas')
                                <span class="text-rose-600 text-[10px] block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="pt-2 border-t border-stone-100 space-y-2">
                            <button wire:click="publishRapor" 
                                class="w-full py-3 px-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition duration-200 shadow-md flex items-center justify-center gap-2">
                                <x-lucide-send class="w-4 h-4" />
                                <span>{{ $existingRapor ? 'Perbarui Rapor Resmi' : 'Terbitkan Rapor Resmi' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Grade Calculation Preview -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider flex items-center gap-1.5">
                                    <x-lucide-eye class="w-4 h-4 text-indigo-600" />
                                    <span>Preview Data Rapor {{ $guruJenis === 'tahfizh' ? 'Tahfizh' : 'Umum' }}</span>
                                </h3>
                                <p class="text-[10px] text-stone-500">Nilai dihitung berdasarkan komponen penilaian guru mata pelajaran.</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-stone-200 text-[10px] font-bold text-stone-600 uppercase tracking-wider">
                                        <th class="pb-3 w-10 text-center">No</th>
                                        <th class="pb-3">Mata Pelajaran</th>
                                        <th class="pb-3 text-center">Pengetahuan</th>
                                        <th class="pb-3 text-center">Keterampilan</th>
                                        <th class="pb-3 text-center">Keagamaan</th>
                                        <th class="pb-3 text-center">Sikap</th>
                                        <th class="pb-3 text-center w-24 bg-stone-50 font-extrabold text-indigo-700">Nilai Akhir</th>
                                        <th class="pb-3 text-center w-16 bg-stone-100">Predikat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-100 text-xs">
                                    @forelse ($this->calculatedPreviewGrades as $index => $grade)
                                        <tr class="hover:bg-stone-50/50">
                                            <td class="py-3 text-center text-stone-500 font-bold">{{ $index + 1 }}</td>
                                            <td class="py-3 font-bold text-stone-800">
                                                <span>{{ $grade['nama_mapel'] }}</span>
                                                <span class="text-[9px] text-stone-400 block font-normal uppercase">Kategori: {{ $grade['jenis_mapel'] }}</span>
                                            </td>
                                            <td class="py-3 text-center text-stone-700 font-semibold">{{ $grade['nilai_pengetahuan'] ?? '-' }}</td>
                                            <td class="py-3 text-center text-stone-700 font-semibold">{{ $grade['nilai_keterampilan'] ?? '-' }}</td>
                                            <td class="py-3 text-center text-stone-700 font-semibold">{{ $grade['nilai_keagamaan'] ?? '-' }}</td>
                                            <td class="py-3 text-center text-stone-700 font-semibold">{{ $grade['nilai_sikap'] ?? '-' }}</td>
                                            <td class="py-3 text-center bg-stone-50 text-indigo-700 font-extrabold text-sm">{{ $grade['nilai_akhir'] }}</td>
                                            <td class="py-3 text-center bg-stone-100 font-extrabold">
                                                @php
                                                    $predClass = '';
                                                    if ($grade['predikat'] === 'A') $predClass = 'text-emerald-600';
                                                    elseif ($grade['predikat'] === 'B') $predClass = 'text-blue-600';
                                                    elseif ($grade['predikat'] === 'C') $predClass = 'text-amber-600';
                                                    elseif ($grade['predikat'] === 'D') $predClass = 'text-yellow-600';
                                                    else $predClass = 'text-rose-600';
                                                @endphp
                                                <span class="{{ $predClass }}">{{ $grade['predikat'] }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="py-12 text-center text-stone-400 font-semibold text-xs">
                                                <x-lucide-file-x class="w-8 h-8 text-stone-300 mx-auto mb-2" />
                                                <span>Belum ada nilai terisi untuk kelompok mata pelajaran {{ $guruJenis === 'tahfizh' ? 'Tahfizh' : 'Umum' }}.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl text-[11px] text-stone-600 space-y-1.5 leading-relaxed">
                            <p class="font-bold text-stone-800 flex items-center gap-1.5">
                                <x-lucide-lightbulb class="w-4 h-4 text-amber-500" />
                                <span>Informasi Penerbitan Rapor {{ $guruJenis === 'tahfizh' ? 'Tahfizh' : 'Umum' }}:</span>
                            </p>
                            <p>1. **Penerbitan Khusus**: Guru Tahfizh hanya dapat mengelola &amp; menerbitkan Rapor Tahfizh Al-Qur'an, sedangkan Guru Umum mengelola Rapor Akademik Umum.</p>
                            <p>2. **Notifikasi Otomatis**: Murid menerima notifikasi in-app ketika rapor resmi diterbitkan.</p>
                            <p>3. **Integrasi Status Keuangan**: Rapor murid yang menunggak SPP per tanggal 10 otomatis terkunci di portal murid sampai tunggakan diselesaikan.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
