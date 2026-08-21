<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Evaluasi Capaian &amp; Pengembangan Diri Guru" 
        subtitle="Buka link Google Drive formulir/berkas bukti yang diunggah guru, berikan penilaian skor, predikat, dan catatan feedback."
        badge="EVALUASI CAPAIAN GURU"
        badgeVariant="emerald"
        icon="award"
    />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Evaluasi & Validasi Capaian Guru"
        :steps="[
            ['title' => 'Tinjau Berkas Google Drive', 'desc' => 'Klik tombol Buka Google Drive pada baris pengajuan untuk memeriksa kelengkapan portofolio atau sertifikat guru.'],
            ['title' => 'Input Skor & Predikat', 'desc' => 'Beri skor penilaian (0-100), tentukan predikat (Sangat Baik/Baik/Cukup), dan berikan catatan evaluasi konstruktif.'],
            ['title' => 'Transparansi Hasil', 'desc' => 'Hasil penilaian otomatis terbit dan dapat dilihat secara langsung oleh guru yang bersangkutan pada portal guru.']
        ]"
    />

    <!-- Alert Notifications -->
    @if (session()->has('success'))
        <x-alert-banner type="success" :message="session('success')" />
    @endif

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Berkas Diajukan</span>
                <div class="p-2 bg-blue-50 text-blue-700 rounded-xl border border-blue-200">
                    <x-lucide-file-text class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">{{ number_format($totalPengajuan) }} <span class="text-xs font-bold text-stone-400">Pengajuan</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Dari seluruh Ustadz/Ustadzah</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Belum Dinilai</span>
                <div class="p-2 bg-amber-50 text-amber-700 rounded-xl border border-amber-200">
                    <x-lucide-clock class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600">{{ number_format($belumDinilai) }} <span class="text-xs font-bold text-stone-400">Berkas</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Membutuhkan review Super Admin</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Sudah Evaluasi</span>
                <div class="p-2 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200">
                    <x-lucide-check-circle class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-700">{{ number_format($sudahDinilai) }} <span class="text-xs font-bold text-stone-400">Berkas</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Telah memiliki nilai &amp; feedback</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Rata-Rata Nilai</span>
                <div class="p-2 bg-purple-50 text-purple-700 rounded-xl border border-purple-200">
                    <x-lucide-award class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">{{ $avgSkor ?: '-' }}</div>
            <div class="text-[11px] text-stone-500 font-medium">Skor kumulatif pengembangan</div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Controls: Search & Filters -->
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full flex-1">
                <div class="w-full sm:max-w-xs">
                    <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama guru / judul..." />
                </div>

                <select wire:model.live="filterGuru" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Guru</option>
                    @foreach ($gurus as $g)
                        <option value="{{ $g->id }}">{{ $g->user->nama ?? '-' }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterKategori" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Kategori</option>
                    <option value="pengembangan_diri">Pengembangan Diri</option>
                    <option value="capaian_kinerja">Capaian Kinerja</option>
                    <option value="pelatihan">Pelatihan / Workshop</option>
                    <option value="sertifikasi">Sertifikasi</option>
                </select>

                <select wire:model.live="filterStatus" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Status</option>
                    <option value="diajukan">Belum Dinilai</option>
                    <option value="dinilai">Sudah Dinilai</option>
                </select>
            </div>
        </div>

        <!-- Table Submissions -->
        <x-table loadingTarget="filterGuru, filterKategori, filterStatus, search">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="min-w-[180px]">Guru Pengampu</x-table.th>
                    <x-table.th class="min-w-[220px]">Judul Capaian &amp; Kategori</x-table.th>
                    <x-table.th align="center" class="w-48">Berkas Form / GDrive</x-table.th>
                    <x-table.th align="center" class="w-44">Status &amp; Penilaian</x-table.th>
                    <x-table.th align="center" class="w-40">Aksi Evaluasi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($capaianList as $item)
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ $item->guru->user->nama ?? '-' }}</div>
                            <div class="text-[10px] text-stone-500 font-semibold">NIY: {{ $item->guru->niy ?: ($item->guru->nip ?: '-') }}</div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs leading-snug">{{ $item->judul }}</div>
                            <div class="mt-1 flex items-center gap-2">
                                <x-badge variant="stone" size="xs">
                                    {{ str_replace('_', ' ', $item->kategori) }}
                                </x-badge>
                            </div>
                            @if ($item->deskripsi)
                                <div class="text-[11px] text-stone-500 mt-1 line-clamp-1 italic">{{ $item->deskripsi }}</div>
                            @endif
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($item->link_gdrive)
                                <a href="{{ $item->link_gdrive }}" target="_blank" rel="noopener noreferrer" 
                                   class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-xl font-bold text-xs inline-flex items-center gap-1.5 transition shadow-2xs">
                                    <x-lucide-external-link class="w-3.5 h-3.5" />
                                    <span>Buka Google Drive</span>
                                </a>
                            @else
                                <span class="text-stone-400 italic text-[11px]">Tidak ada link</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($item->status_penilaian === 'dinilai')
                                <div class="space-y-1">
                                    <x-badge variant="emerald" size="xs">Dinilai</x-badge>
                                    <div class="text-xs font-black text-purple-700">
                                        Skor: {{ $item->skor_nilai }} ({{ $item->predikat }})
                                    </div>
                                </div>
                            @else
                                <x-badge variant="amber" size="xs">Belum Dinilai</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <x-button type="button" variant="outline" size="xs" icon="eye" wire:click="openDetailModal({{ $item->id }})">
                                    Detail
                                </x-button>

                                <x-button type="button" variant="primary" size="xs" icon="edit-3" wire:click="openEvaluateModal({{ $item->id }})">
                                    {{ $item->status_penilaian === 'dinilai' ? 'Nilai' : 'Beri Nilai' }}
                                </x-button>

                                <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="delete({{ $item->id }})" data-confirm="Apakah Anda yakin ingin menghapus data pengajuan ini?">
                                    Hapus
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="5" title="Tidak ada data pengajuan capaian guru ditemukan" message="Pengajuan evaluasi guru akan muncul di sini setelah guru mengunggah berkas." />
                @endforelse
            </tbody>
        </x-table>

        @if ($capaianList->hasPages())
            <div class="pt-2">
                {{ $capaianList->links() }}
            </div>
        @endif
    </div>

    <!-- Floating Detail Modal -->
    <x-floating-card 
        :show="($showDetailModal && $detailCapaian) ? true : false"
        title="Detail Capaian &amp; Portofolio Guru"
        :subtitle="$detailCapaian ? ('Informasi lengkap pengajuan oleh: ' . ($detailCapaian->guru?->user?->nama ?? 'Guru')) : ''"
        badge="DETAIL CAPAIAN"
        badgeVariant="emerald"
        icon="award"
        maxWidth="max-w-2xl"
        closeAction="closeDetailModal"
    >
        @if ($detailCapaian)
            <div class="space-y-5 text-xs">
                <!-- Header Profil Guru & Status -->
                <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-800 text-white font-black text-sm flex items-center justify-center shadow-xs">
                            {{ strtoupper(substr($detailCapaian->guru?->user?->nama ?? 'G', 0, 2)) }}
                        </div>
                        <div>
                            <div class="font-black text-stone-900 text-sm">{{ $detailCapaian->guru?->user?->nama ?? '-' }}</div>
                            <div class="text-[11px] text-stone-500 font-semibold flex items-center gap-2 mt-0.5">
                                <span>NIY: {{ $detailCapaian->guru?->niy ?: ($detailCapaian->guru?->nip ?: '-') }}</span>
                                <span>•</span>
                                <span>{{ $detailCapaian->guru?->user?->email ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 self-end sm:self-center">
                        @if ($detailCapaian->status_penilaian === 'dinilai')
                            <x-badge variant="emerald" size="sm">SUDAH DINILAI</x-badge>
                        @else
                            <x-badge variant="amber" size="sm">MENUNGGU PENILAIAN</x-badge>
                        @endif
                    </div>
                </div>

                <!-- Informasi Pokok Pengajuan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3.5 bg-white border border-stone-200 rounded-xl space-y-1">
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Kategori Pengembangan</span>
                        <div class="font-extrabold text-stone-800 text-xs">
                            {{ ucwords(str_replace('_', ' ', $detailCapaian->kategori)) }}
                        </div>
                    </div>

                    <div class="p-3.5 bg-white border border-stone-200 rounded-xl space-y-1">
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Tahun Ajaran &amp; Semester</span>
                        <div class="font-extrabold text-stone-800 text-xs">
                            {{ $detailCapaian->tahunAjaran?->tahun_ajaran ?? '-' }} (Semester {{ $detailCapaian->semester?->semester ?? '-' }})
                        </div>
                    </div>
                </div>

                <!-- Judul & Deskripsi Pengajuan -->
                <div class="p-4 bg-white border border-stone-200 rounded-2xl space-y-2">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Judul Capaian / Kegiatan</span>
                    <h3 class="font-black text-stone-900 text-sm leading-snug">{{ $detailCapaian->judul }}</h3>
                    
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block pt-2">Uraian / Deskripsi Lengkap</span>
                    <p class="text-xs text-stone-700 leading-relaxed font-medium bg-stone-50 p-3 rounded-xl border border-stone-100 whitespace-pre-line">
                        {{ $detailCapaian->deskripsi ?: 'Tidak ada uraian deskripsi tambahan yang dicantumkan.' }}
                    </p>
                </div>

                <!-- Link Berkas Google Drive -->
                <div class="p-4 bg-blue-50/60 border border-blue-200 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="space-y-0.5">
                        <span class="text-[10px] font-bold text-blue-800 uppercase tracking-wider block flex items-center gap-1.5">
                            <x-lucide-file-text class="w-3.5 h-3.5" /> Berkas Portofolio / Formulir Bukti
                        </span>
                        <div class="text-[11px] text-stone-600">
                            {{ $detailCapaian->link_gdrive ? 'Tautan dokumen Google Drive tersedia untuk ditinjau.' : 'Guru belum menyertakan tautan Google Drive.' }}
                        </div>
                    </div>

                    @if ($detailCapaian->link_gdrive)
                        <a href="{{ $detailCapaian->link_gdrive }}" target="_blank" rel="noopener noreferrer" 
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs inline-flex items-center gap-1.5 shadow-xs transition shrink-0">
                            <x-lucide-external-link class="w-3.5 h-3.5" />
                            <span>Buka Google Drive</span>
                        </a>
                    @endif
                </div>

                <!-- Bagian Hasil Evaluasi & Catatan (Jika Sudah Dinilai) -->
                @if ($detailCapaian->status_penilaian === 'dinilai')
                    <div class="p-4 bg-emerald-50/80 border border-emerald-300 rounded-2xl space-y-3">
                        <div class="flex items-center justify-between border-b border-emerald-200 pb-2.5">
                            <span class="text-xs font-black text-emerald-950 uppercase tracking-wider flex items-center gap-1.5">
                                <x-lucide-award class="w-4 h-4 text-emerald-700" />
                                Hasil Penilaian &amp; Validasi
                            </span>
                            <span class="text-[11px] text-emerald-800 font-semibold">
                                Dinilai: {{ \Carbon\Carbon::parse($detailCapaian->tanggal_penilaian)->translatedFormat('d F Y') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3 bg-white border border-emerald-200 rounded-xl">
                                <span class="text-[10px] font-bold text-stone-400 uppercase block mb-1">Skor Penilaian</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl font-black text-emerald-800">{{ $detailCapaian->skor_nilai }}</span>
                                    <span class="text-xs font-bold text-stone-400">/ 100</span>
                                </div>
                            </div>

                            <div class="p-3 bg-white border border-emerald-200 rounded-xl">
                                <span class="text-[10px] font-bold text-stone-400 uppercase block mb-1">Predikat Evaluasi</span>
                                <div class="text-sm font-black text-emerald-900 uppercase">
                                    {{ $detailCapaian->predikat }}
                                </div>
                            </div>
                        </div>

                        @if ($detailCapaian->catatan_evaluasi)
                            <div class="p-3 bg-white border border-emerald-200 rounded-xl space-y-1">
                                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Catatan / Arahan Evaluator:</span>
                                <p class="text-xs text-stone-800 leading-relaxed font-medium whitespace-pre-line">
                                    {{ $detailCapaian->catatan_evaluasi }}
                                </p>
                            </div>
                        @endif

                        <div class="text-[10px] text-emerald-800 font-semibold flex items-center gap-1.5 pt-1">
                            <x-lucide-user-check class="w-3.5 h-3.5 text-emerald-600" />
                            <span>Evaluator: <strong>{{ $detailCapaian->penilai?->nama ?? 'Super Admin' }}</strong></span>
                        </div>
                    </div>
                @endif

                <!-- Modal Actions -->
                <div class="flex items-center justify-between pt-3 border-t border-stone-200">
                    <x-button type="button" variant="secondary" size="md" wire:click="closeDetailModal">
                        Tutup
                    </x-button>

                    <x-button type="button" variant="primary" size="md" icon="edit-3" wire:click="openEvaluateFromDetail">
                        {{ $detailCapaian->status_penilaian === 'dinilai' ? 'Ubah Penilaian' : 'Beri Nilai Sekarang' }}
                    </x-button>
                </div>
            </div>
        @endif
    </x-floating-card>

    <!-- Floating Evaluation Modal -->
    <x-floating-card 
        :show="($showEvaluateModal && $selectedCapaian) ? true : false"
        title="Penilaian Capaian Guru"
        :subtitle="$selectedCapaian ? ('Evaluasi pengajuan: ' . ($selectedCapaian->guru?->user?->nama ?? 'Guru Pengampu')) : ''"
        badge="EVALUASI"
        badgeVariant="emerald"
        icon="award"
        maxWidth="max-w-lg"
        closeAction="closeModal"
    >
        @if ($selectedCapaian)
            <!-- Info Box Pengajuan -->
            <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-xl space-y-2 text-xs mb-4">
                <div class="font-extrabold text-stone-900">{{ $selectedCapaian->judul }}</div>
                @if ($selectedCapaian->link_gdrive)
                    <a href="{{ $selectedCapaian->link_gdrive }}" target="_blank" rel="noopener noreferrer" 
                       class="inline-flex items-center gap-1.5 text-blue-600 hover:underline font-extrabold text-xs">
                        <x-lucide-external-link class="w-3.5 h-3.5" /> Buka Google Drive Formulir/Berkas Guru
                    </a>
                @endif
            </div>

            <form wire:submit.prevent="saveEvaluation" class="space-y-4 text-xs font-medium">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-stone-700 mb-1 uppercase">Skor Nilai (0-100) <span class="text-rose-600">*</span></label>
                        <input type="number" step="0.1" min="0" max="100" wire:model="skor_nilai" placeholder="Contoh: 88.5" 
                               class="w-full py-2.5 px-3.5 bg-white border border-stone-300 rounded-xl font-bold focus:ring-2 focus:ring-emerald-600 text-xs shadow-2xs" required />
                        @error('skor_nilai') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1 uppercase">Predikat <span class="text-rose-600">*</span></label>
                        <select wire:model="predikat" class="w-full py-2.5 px-3.5 bg-white border border-stone-300 rounded-xl font-bold focus:ring-2 focus:ring-emerald-600 text-xs shadow-2xs" required>
                            <option value="Sangat Baik">Sangat Baik</option>
                            <option value="Baik">Baik</option>
                            <option value="Cukup">Cukup</option>
                            <option value="Perlu Bimbingan">Perlu Bimbingan</option>
                        </select>
                        @error('predikat') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1 uppercase">Tanggal Penilaian <span class="text-rose-600">*</span></label>
                    <input type="date" wire:model="tanggal_penilaian" class="w-full py-2.5 px-3.5 bg-white border border-stone-300 rounded-xl font-bold focus:ring-2 focus:ring-emerald-600 text-xs shadow-2xs" required />
                    @error('tanggal_penilaian') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1 uppercase">Catatan Feedback / Evaluasi</label>
                    <textarea wire:model="catatan_evaluasi" rows="3" placeholder="Masukan dan arahan konstruktif untuk guru..." 
                              class="w-full py-2.5 px-3.5 bg-white border border-stone-300 rounded-xl font-medium focus:ring-2 focus:ring-emerald-600 text-xs shadow-2xs resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                    <x-button type="button" variant="secondary" size="md" wire:click="closeModal">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="saveEvaluation">
                        Simpan Penilaian
                    </x-button>
                </div>
            </form>
        @endif
    </x-floating-card>
</div>
