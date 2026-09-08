<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <x-page-header 
        title="Presensi & Penilaian Ekstrakurikuler" 
        subtitle="Catat presensi kehadiran santri dan berikan penilaian berkala pada setiap sesi pertemuan ekstrakurikuler."
        badge="PEMBINA EKSTRAKURIKULER"
        badgeVariant="emerald"
        icon="award"
    />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pembina Ekstrakurikuler"
        :steps="[
            ['title' => 'Pilih Ekstrakurikuler', 'desc' => 'Pilih kegiatan ekstrakurikuler binaan Anda pada daftar di bawah.'],
            ['title' => 'Kelola Sesi Pertemuan', 'desc' => 'Klik Tambah Sesi Kegiatan untuk membuat pertemuan baru sesuai jadwal.'],
            ['title' => 'Presensi & Nilai Berkala', 'desc' => 'Tandai status kehadiran (Hadir/Sakit/Izin/Alpa) dan berikan nilai berkala (0-100) per setiap sesi.'],
            ['title' => 'Rekap Akhir Rapor', 'desc' => 'Buka tab Rekap Rapor untuk melihat kehadiran kumulatif, rata-rata nilai, dan menetapkan predikat akhir.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Selector / Tabs of Active Extracurriculars -->
    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-stone-200 pb-3">
            <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                <x-lucide-award class="w-4 h-4 text-emerald-600" />
                <span>Pilih Kegiatan Ekstrakurikuler</span>
            </h3>
            <span class="text-xs text-stone-500 font-semibold">{{ $myEkskuls->count() }} Kegiatan Binaan Saya</span>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar">
            @forelse ($myEkskuls as $ekskul)
                @php
                    $isSelected = ($selectedEkskulId === $ekskul->id);
                @endphp
                <button 
                    type="button" 
                    wire:click="selectEkskul({{ $ekskul->id }})"
                    class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 border cursor-pointer
                    {{ $isSelected 
                        ? 'bg-emerald-700 text-white border-emerald-700 shadow-sm' 
                        : 'bg-stone-50 hover:bg-stone-100 text-stone-700 border-stone-200' }}"
                >
                    <span>{{ $ekskul->nama }}</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black {{ $isSelected ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">Binaan</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isSelected ? 'bg-emerald-800 text-emerald-100' : 'bg-stone-200 text-stone-600' }}">
                        {{ $ekskul->siswaEkskul->count() }} Santri
                    </span>
                </button>
            @empty
                <div class="text-xs text-stone-500 p-2 italic">
                    Anda belum ditetapkan sebagai pembina pada kegiatan ekstrakurikuler mana pun oleh Tata Usaha.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Active Ekskul Detail & Content Tabs -->
    @if ($selectedEkskul)
        <!-- Ekskul Meta Bar -->
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold border border-emerald-200 shrink-0">
                    <x-lucide-award class="w-6 h-6" />
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-black text-stone-900">{{ $selectedEkskul->nama }}</h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                            {{ $roster->count() }} Santri Terdaftar
                        </span>
                    </div>
                    <div class="text-xs text-stone-500 flex items-center gap-3 mt-1 font-medium">
                        @if ($selectedEkskul->hari)
                            <span class="flex items-center gap-1 text-stone-700 font-bold">
                                <x-lucide-calendar class="w-3.5 h-3.5 text-emerald-600" />
                                <span>{{ ucfirst($selectedEkskul->hari) }}, {{ $selectedEkskul->jam_mulai }} - {{ $selectedEkskul->jam_selesai }} WIB</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <x-lucide-map-pin class="w-3.5 h-3.5 text-stone-400" />
                                <span>{{ $selectedEkskul->tempat ?: 'Tempat belum diatur' }}</span>
                            </span>
                        @else
                            <span class="text-amber-600 italic">Jadwal belum diatur oleh Admin</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tab Mode Switcher -->
            <div class="flex items-center gap-1.5 bg-stone-100 p-1.5 rounded-xl border border-stone-200">
                <button type="button" wire:click="$set('viewTab', 'sesi')"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $viewTab === 'sesi' ? 'bg-emerald-700 text-white shadow-xs' : 'text-stone-600 hover:bg-stone-200' }}">
                    <x-lucide-calendar-check class="w-3.5 h-3.5" />
                    <span>Presensi & Nilai Sesi</span>
                </button>
                <button type="button" wire:click="$set('viewTab', 'roster')"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $viewTab === 'roster' ? 'bg-emerald-700 text-white shadow-xs' : 'text-stone-600 hover:bg-stone-200' }}">
                    <x-lucide-award class="w-3.5 h-3.5" />
                    <span>Rekap Rapor Akhir</span>
                </button>
            </div>
        </div>

        <!-- ================= TAB 1: PRESENSI & NILAI BERKALA PER SESI ================= -->
        @if ($viewTab === 'sesi')
            <div class="space-y-4">
                <!-- Session Selector Toolbar -->
                <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:max-w-xl">
                        <label class="text-xs font-bold text-stone-600 shrink-0">Pilih Pertemuan:</label>
                        <select wire:model.live="selectedKegiatanId" class="w-full bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-extrabold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                            @forelse ($kegiatans as $keg)
                                <option value="{{ $keg->id }}">
                                    {{ $keg->nama_kegiatan }} &bull; {{ $keg->tanggal->translatedFormat('d M Y') }}
                                </option>
                            @empty
                                <option value="">Belum ada sesi kegiatan</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="openCreateKegiatan" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs">
                            <x-lucide-plus class="w-4 h-4" />
                            <span>Tambah Sesi Kegiatan</span>
                        </button>
                    </div>
                </div>

                @if ($currentKegiatan)
                    <!-- Active Session Card & Attendance Table -->
                    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-stone-200">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-extrabold text-stone-900 text-sm">{{ $currentKegiatan->nama_kegiatan }}</h4>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-stone-100 text-stone-700 border border-stone-200">
                                        {{ $currentKegiatan->tanggal->translatedFormat('l, d F Y') }}
                                    </span>
                                </div>
                                <p class="text-xs text-stone-500 mt-0.5">{{ $currentKegiatan->keterangan ?: 'Tidak ada keterangan sesi kegiatan.' }}</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="setSemuaHadir" class="px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold border border-stone-300 transition">
                                    Set Semua Hadir
                                </button>
                                <button type="button" wire:click="savePresensiDanNilaiSesi" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs">
                                    <x-lucide-save class="w-4 h-4" />
                                    <span>Simpan Presensi & Nilai</span>
                                </button>
                                <button type="button" wire:click="deleteKegiatan({{ $currentKegiatan->id }})" wire:confirm="Hapus sesi pertemuan ini beserta presensinya?" class="p-2 rounded-xl text-rose-600 hover:bg-rose-50 transition" title="Hapus Sesi">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Table Presensi & Nilai Sesi -->
                        <div class="border border-stone-200 rounded-xl overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-emerald-800 text-white font-extrabold uppercase text-[11px] tracking-wider">
                                    <tr>
                                        <th class="p-3 w-40">NIS & Kelas</th>
                                        <th class="p-3 min-w-[180px]">Nama Santri</th>
                                        <th class="p-3 w-56 text-center">Kehadiran Sesi</th>
                                        <th class="p-3 w-28 text-center">Nilai Sesi (0-100)</th>
                                        <th class="p-3 min-w-[200px]">Catatan Kegiatan / Keaktifan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-200 bg-white">
                                    @forelse ($roster as $m)
                                        <tr class="hover:bg-stone-50 transition">
                                            <td class="p-3 border-r border-stone-200">
                                                <div class="font-bold text-stone-800">{{ $m->siswa->nis ?? '-' }}</div>
                                                <div class="text-[10px] text-stone-500">{{ $m->siswa->kelas->nama_kelas ?? 'Tanpa Kelas' }}</div>
                                            </td>
                                            <td class="p-3 border-r border-stone-200">
                                                <div class="font-extrabold text-stone-900 text-xs">{{ $m->siswa->user->nama ?? '-' }}</div>
                                            </td>
                                            <td class="p-3 border-r border-stone-200 text-center">
                                                <div class="inline-flex items-center gap-1 bg-stone-100 p-1 rounded-xl border border-stone-200 text-[11px] font-bold">
                                                    @foreach (['Hadir' => 'H', 'Sakit' => 'S', 'Izin' => 'I', 'Alpa' => 'A'] as $statusKey => $label)
                                                        @php
                                                            $isCur = ($presensiStatus[$m->siswa_id] ?? 'Hadir') === $statusKey;
                                                            $colorClass = match($statusKey) {
                                                                'Hadir' => $isCur ? 'bg-emerald-600 text-white' : 'text-stone-700 hover:bg-stone-200',
                                                                'Sakit' => $isCur ? 'bg-amber-500 text-white' : 'text-stone-700 hover:bg-stone-200',
                                                                'Izin' => $isCur ? 'bg-blue-500 text-white' : 'text-stone-700 hover:bg-stone-200',
                                                                'Alpa' => $isCur ? 'bg-rose-600 text-white' : 'text-stone-700 hover:bg-stone-200',
                                                            };
                                                        @endphp
                                                        <label class="px-2 py-1 rounded-lg cursor-pointer transition {{ $colorClass }}">
                                                            <input type="radio" wire:model.defer="presensiStatus.{{ $m->siswa_id }}" value="{{ $statusKey }}" class="sr-only">
                                                            <span>{{ $label }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="p-3 border-r border-stone-200 text-center">
                                                <input type="number" step="0.5" min="0" max="100" 
                                                    wire:model.defer="nilaiBerkala.{{ $m->siswa_id }}" 
                                                    placeholder="85"
                                                    class="w-20 text-center font-extrabold text-stone-900 rounded-lg border border-stone-300 px-2 py-1.5 focus:ring-2 focus:ring-emerald-600">
                                            </td>
                                            <td class="p-3">
                                                <input type="text" 
                                                    wire:model.defer="catatanBerkala.{{ $m->siswa_id }}" 
                                                    placeholder="Catatan keaktifan pada sesi ini..."
                                                    class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs text-stone-800 focus:ring-2 focus:ring-emerald-600">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-8 text-center text-stone-400">
                                                Belum ada santri terdaftar pada ekstrakurikuler ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-end pt-2">
                            <button type="button" wire:click="savePresensiDanNilaiSesi" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs">
                                <x-lucide-save class="w-4 h-4" />
                                <span>Simpan Seluruh Presensi & Nilai Sesi</span>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="bg-white border border-stone-200 rounded-2xl p-12 text-center shadow-xs">
                        <x-lucide-calendar class="w-12 h-12 text-stone-300 mx-auto mb-2" />
                        <h4 class="font-extrabold text-stone-800 text-sm">Belum Ada Sesi Kegiatan</h4>
                        <p class="text-xs text-stone-500 mt-1 mb-4">Mulai mencatat presensi dan penilaian dengan membuat sesi pertemuan baru.</p>
                        <button type="button" wire:click="openCreateKegiatan" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold inline-flex items-center gap-2 shadow-xs">
                            <x-lucide-plus class="w-4 h-4" />
                            <span>Buat Sesi Pertemuan Pertama</span>
                        </button>
                    </div>
                @endif
            </div>
        @endif

        <!-- ================= TAB 2: REKAP AKHIR RAPOR ================= -->
        @if ($viewTab === 'roster')
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-stone-200">
                    <div>
                        <h4 class="font-extrabold text-stone-900 text-sm">Rekapitulasi Kehadiran & Nilai Rapor Akhir</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Sistem secara otomatis menghitung kehadiran kumulatif dan rata-rata nilai sesi berkala untuk bahan input rapor.</p>
                    </div>
                    <span class="text-xs font-extrabold text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-xl">
                        Total Pertemuan Terlaksana: {{ $kegiatans->count() }} Sesi
                    </span>
                </div>

                <div class="border border-stone-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-emerald-800 text-white font-extrabold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="p-3 w-36">NIS & Kelas</th>
                                <th class="p-3 min-w-[180px]">Nama Santri</th>
                                <th class="p-3 w-32 text-center">Total Hadir</th>
                                <th class="p-3 w-32 text-center">Rata-rata Nilai</th>
                                <th class="p-3 w-36 text-center">Predikat Rapor</th>
                                <th class="p-3 min-w-[220px]">Catatan Deskripsi Rapor</th>
                                <th class="p-3 w-28 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @forelse ($roster as $m)
                                @php
                                    $summary = $rosterSummary[$m->id] ?? ['hadir_count' => 0, 'total_sesi' => 0, 'avg_score' => null];
                                @endphp
                                <tr class="hover:bg-stone-50 transition">
                                    <td class="p-3 border-r border-stone-200">
                                        <div class="font-bold text-stone-800">{{ $m->siswa->nis ?? '-' }}</div>
                                        <div class="text-[10px] text-stone-500">{{ $m->siswa->kelas->nama_kelas ?? 'Tanpa Kelas' }}</div>
                                    </td>
                                    <td class="p-3 border-r border-stone-200">
                                        <div class="font-extrabold text-stone-900 text-xs">{{ $m->siswa->user->nama ?? '-' }}</div>
                                    </td>
                                    <td class="p-3 border-r border-stone-200 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-stone-100 text-stone-800">
                                            {{ $summary['hadir_count'] }} / {{ $summary['total_sesi'] }} Sesi
                                        </span>
                                    </td>
                                    <td class="p-3 border-r border-stone-200 text-center font-mono font-extrabold text-stone-900">
                                        {{ $summary['avg_score'] !== null ? $summary['avg_score'] : '-' }}
                                    </td>
                                    <td class="p-3 border-r border-stone-200 text-center">
                                        <select wire:model.defer="predikatInputs.{{ $m->id }}" class="w-24 text-center font-extrabold text-stone-900 rounded-lg border border-stone-300 px-2 py-1.5 focus:ring-2 focus:ring-emerald-600">
                                            <option value="A">A (Sangat Baik)</option>
                                            <option value="B">B (Baik)</option>
                                            <option value="C">C (Cukup)</option>
                                            <option value="D">D (Kurang)</option>
                                        </select>
                                    </td>
                                    <td class="p-3 border-r border-stone-200">
                                        <input type="text" wire:model.defer="catatanInputs.{{ $m->id }}" placeholder="Catatan capaian rapor..." class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs text-stone-800 focus:ring-2 focus:ring-emerald-600">
                                    </td>
                                    <td class="p-3 text-center">
                                        <button type="button" wire:click="saveScore({{ $m->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-2xs">
                                            Simpan
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-stone-400">
                                        Belum ada santri terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    <!-- ================= MODAL TAMBAH SESI KEGIATAN ================= -->
    @if ($isKegiatanFormOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-stone-200 overflow-hidden">
                <div class="p-5 bg-stone-50 border-b border-stone-200 flex items-center justify-between">
                    <h3 class="font-extrabold text-stone-900 text-base">Tambah Sesi Pertemuan Kegiatan</h3>
                    <button wire:click="$set('isKegiatanFormOpen', false)" class="text-stone-400 hover:text-stone-600">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>
                <form wire:submit.prevent="saveKegiatan" class="p-6 space-y-4 text-xs">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Tanggal Pelaksanaan *</label>
                        <input type="date" wire:model="kegiatanTanggal" class="w-full rounded-xl border border-stone-300 px-3.5 py-2 text-sm font-semibold text-stone-900 focus:ring-2 focus:ring-emerald-600">
                        @error('kegiatanTanggal') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Nama Sesi / Materi Kegiatan *</label>
                        <input type="text" wire:model="kegiatanNama" placeholder="Contoh: Pertemuan 1 - Pengenalan Teknik Dasar" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm font-semibold text-stone-900 focus:ring-2 focus:ring-emerald-600">
                        @error('kegiatanNama') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Keterangan Tambahan</label>
                        <textarea wire:model="kegiatanKeterangan" rows="3" placeholder="Tuliskan catatan materi yang diajarkan atau penugasan..." class="w-full rounded-xl border border-stone-300 px-3.5 py-2 text-sm font-semibold text-stone-900 focus:ring-2 focus:ring-emerald-600"></textarea>
                    </div>
                    <div class="pt-3 border-t border-stone-200 flex items-center justify-end gap-2">
                        <button type="button" wire:click="$set('isKegiatanFormOpen', false)" class="px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 hover:bg-stone-100 text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs">
                            Simpan Sesi Kegiatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
