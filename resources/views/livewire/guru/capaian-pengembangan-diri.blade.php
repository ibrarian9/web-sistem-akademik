<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <x-page-header 
        title="Capaian & Pengembangan Diri Saya" 
        subtitle="Unggah link Google Drive formulir/berkas bukti pengembangan diri dan pantau hasil evaluasi dari Super Admin / Kepala Sekolah."
        badge="PENGEMBANGAN DIRI GURU"
        badgeVariant="emerald"
        icon="award"
    >
        <x-slot:actions>
            <x-button type="button" variant="primary" size="md" icon="plus-circle" wire:click="openCreate">
                Upload Capaian / Link Drive
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Alert Notifications -->
    @if (session()->has('success'))
        <x-alert-banner type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="danger" :message="session('error')" />
    @endif

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Diajukan</span>
                <div class="p-2 bg-blue-50 text-blue-700 rounded-xl border border-blue-200">
                    <x-lucide-file-text class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">{{ number_format($summary['total']) }} <span class="text-xs font-bold text-stone-400">Berkas</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Capaian & pengembangan diri</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Sudah Evaluasi</span>
                <div class="p-2 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200">
                    <x-lucide-check-circle class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">{{ number_format($summary['dinilai']) }} <span class="text-xs font-bold text-stone-400">Berkas</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Telah dinilai Super Admin</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Rata-Rata Nilai</span>
                <div class="p-2 bg-purple-50 text-purple-700 rounded-xl border border-purple-200">
                    <x-lucide-award class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">{{ $summary['rata_skor'] ?: '-' }}</div>
            <div class="text-[11px] text-stone-500 font-medium">Skor kumulatif pengembangan</div>
        </div>
    </div>

    <!-- Submissions List -->
    <div class="space-y-4">
        @forelse ($capaianList as $item)
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4 hover:border-purple-300 transition duration-200">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-stone-100 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <x-badge variant="stone" size="xs">
                                {{ ucwords(str_replace('_', ' ', $item->kategori)) }}
                            </x-badge>
                            @if ($item->status_penilaian === 'dinilai')
                                <x-badge variant="emerald" size="xs">
                                    Dinilai Super Admin
                                </x-badge>
                            @else
                                <x-badge variant="amber" size="xs">
                                    Menunggu Evaluasi
                                </x-badge>
                            @endif
                        </div>
                        <h3 class="text-base font-extrabold text-stone-900 tracking-tight mt-1.5">{{ $item->judul }}</h3>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-button type="button" variant="outline" size="xs" icon="eye" wire:click="openDetailModal({{ $item->id }})">
                            Lihat Detail
                        </x-button>

                        @if ($item->link_gdrive)
                            <a href="{{ $item->link_gdrive }}" target="_blank" rel="noopener noreferrer"
                               class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-xl font-bold text-xs inline-flex items-center gap-1.5 transition">
                                <x-lucide-external-link class="w-3.5 h-3.5" />
                                Buka GDrive
                            </a>
                        @endif

                        @if ($item->status_penilaian !== 'dinilai')
                            <x-button type="button" variant="secondary" size="xs" icon="edit-3" wire:click="openEdit({{ $item->id }})">
                                Edit
                            </x-button>
                            <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="delete({{ $item->id }})" 
                                    data-confirm="Apakah Anda yakin ingin menghapus data pengajuan ini?">
                                Hapus
                            </x-button>
                        @endif
                    </div>
                </div>

                @if ($item->deskripsi)
                    <p class="text-xs text-stone-600 leading-relaxed font-medium bg-stone-50/70 p-3 rounded-xl border border-stone-100 line-clamp-2">
                        {{ $item->deskripsi }}
                    </p>
                @endif

                <!-- Evaluation Section from Super Admin -->
                @if ($item->status_penilaian === 'dinilai')
                    <div class="p-4 bg-emerald-50/60 border border-emerald-200 rounded-2xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-900 flex items-center gap-1.5">
                                <x-lucide-award class="w-4 h-4 text-emerald-600" />
                                Hasil Penilaian Super Admin:
                            </span>
                            <div class="flex items-center gap-2">
                                @if ($item->skor_nilai)
                                    <span class="px-2.5 py-1 bg-white border border-emerald-300 text-emerald-900 rounded-xl font-black text-xs">
                                        Skor: {{ $item->skor_nilai }}
                                    </span>
                                @endif
                                @if ($item->predikat)
                                    <span class="px-2.5 py-1 bg-emerald-600 text-white rounded-xl font-black text-xs uppercase">
                                        {{ $item->predikat }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if ($item->catatan_evaluasi)
                            <div class="text-xs text-stone-700 font-medium">
                                <strong class="text-stone-900">Catatan Evaluasi:</strong> {{ $item->catatan_evaluasi }}
                            </div>
                        @endif

                        <div class="text-[10px] text-stone-400 font-semibold pt-1">
                            Dinilai oleh {{ $item->penilai->nama ?? 'Super Admin' }} pada {{ \Carbon\Carbon::parse($item->tanggal_penilaian)->translatedFormat('d M Y') }}
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white border border-stone-200 rounded-3xl p-10 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-200 text-purple-700 flex items-center justify-center mx-auto">
                    <x-lucide-file-plus class="w-6 h-6" />
                </div>
                <h3 class="text-sm font-extrabold text-stone-900 uppercase">Belum Ada Capaian Diajukan</h3>
                <p class="text-xs text-stone-500 max-w-sm mx-auto">Silakan klik tombol "Upload Capaian / Link Drive" untuk mengunggah link Google Drive formulir atau bukti kegiatan pengembangan diri Anda.</p>
            </div>
        @endforelse
    </div>

    <!-- Floating Detail Modal for Guru -->
    <x-floating-card 
        :show="($showDetailModal && $detailCapaian) ? true : false"
        title="Detail Pengajuan & Hasil Evaluasi"
        subtitle="Rincian berkas portofolio pengembangan diri dan evaluasi validator."
        badge="DETAIL CAPAIAN"
        badgeVariant="emerald"
        icon="award"
        maxWidth="max-w-2xl"
        closeAction="closeDetailModal"
    >
        @if ($detailCapaian)
            <div class="space-y-5 text-xs">
                <!-- Status & Kategori -->
                <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Kategori Pengembangan</span>
                        <div class="font-black text-stone-900 text-sm mt-0.5">
                            {{ ucwords(str_replace('_', ' ', $detailCapaian->kategori)) }}
                        </div>
                    </div>

                    <div>
                        @if ($detailCapaian->status_penilaian === 'dinilai')
                            <x-badge variant="emerald" size="sm">SUDAH DINILAI</x-badge>
                        @else
                            <x-badge variant="amber" size="sm">MENUNGGU EVALUASI</x-badge>
                        @endif
                    </div>
                </div>

                <!-- Judul & Deskripsi -->
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
                            {{ $detailCapaian->link_gdrive ? 'Tautan Google Drive berkas bukti Anda.' : 'Anda belum mencantumkan tautan Google Drive.' }}
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

                <!-- Hasil Evaluasi Super Admin (Jika Sudah Dinilai) -->
                @if ($detailCapaian->status_penilaian === 'dinilai')
                    <div class="p-4 bg-emerald-50/80 border border-emerald-300 rounded-2xl space-y-3">
                        <div class="flex items-center justify-between border-b border-emerald-200 pb-2.5">
                            <span class="text-xs font-black text-emerald-950 uppercase tracking-wider flex items-center gap-1.5">
                                <x-lucide-award class="w-4 h-4 text-emerald-700" />
                                Hasil Penilaian Validator
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
                            <span>Dinilai oleh: <strong>{{ $detailCapaian->penilai?->nama ?? 'Super Admin' }}</strong></span>
                        </div>
                    </div>
                @endif

                <!-- Modal Actions -->
                <div class="flex items-center justify-end pt-3 border-t border-stone-200">
                    <x-button type="button" variant="secondary" size="md" wire:click="closeDetailModal">
                        Tutup
                    </x-button>
                </div>
            </div>
        @endif
    </x-floating-card>

    <!-- Modal Form Submit / Edit -->
    <x-floating-card 
        :show="$showModal"
        :title="$capaianId ? 'Edit Capaian / Link Drive' : 'Unggah Capaian / Link Google Drive'"
        subtitle="Formulir bukti fisik atau dokumen pendukung pengembangan diri."
        badge="PENGEMBANGAN DIRI"
        badgeVariant="emerald"
        icon="award"
        maxWidth="max-w-lg"
        closeAction="closeModal"
    >
        <form wire:submit.prevent="save" class="space-y-4 text-xs font-medium">
            <div>
                <label class="block font-bold text-stone-700 mb-1">Judul Capaian / Kegiatan <span class="text-rose-500">*</span></label>
                <input type="text" wire:model="judul" placeholder="Contoh: Sertifikasi Pelatihan Kurikulum Merdeka 2025" 
                       class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white text-xs shadow-xs" />
                @error('judul') <span class="text-[10px] text-rose-500 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold text-stone-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                <select wire:model="kategori" class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white text-xs shadow-xs">
                    <option value="pengembangan_diri">Pengembangan Diri</option>
                    <option value="capaian_kinerja">Capaian Kinerja Pembelajaran</option>
                    <option value="pelatihan">Pelatihan / Workshop / Bintek</option>
                    <option value="sertifikasi">Sertifikasi & Kompetensi</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-stone-700 mb-1">Link Google Drive Formulir / Berkas Bukti</label>
                <input type="url" wire:model="link_gdrive" placeholder="https://drive.google.com/file/d/..." 
                       class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white text-xs shadow-xs" />
                <span class="text-[10px] text-stone-400 mt-1 block">Pastikan akses Google Drive diset menjadi "Siapa saja yang memiliki link dapat melihat".</span>
                @error('link_gdrive') <span class="text-[10px] text-rose-500 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold text-stone-700 mb-1">Deskripsi / Penjelasan Singkat</label>
                <textarea wire:model="deskripsi" rows="3" placeholder="Tuliskan ringkasan kegiatan atau uraian capaian..." 
                          class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white text-xs shadow-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                <x-button type="button" variant="secondary" size="md" wire:click="closeModal">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="check" loadingTarget="save">
                    Simpan & Ajukan
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
