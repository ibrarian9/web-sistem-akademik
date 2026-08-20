<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-purple-100 border border-purple-300 text-purple-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
                SD Tahfizh F3 Professional Development
            </span>
            <h2 class="text-xl font-extrabold text-stone-900 tracking-tight mt-1">Capaian &amp; Pengembangan Diri Saya</h2>
            <p class="text-xs text-stone-500 font-medium">Unggah link Google Drive formulir/berkas bukti pengembangan diri dan pantau hasil evaluasi dari Super Admin.</p>
        </div>

        <button type="button" wire:click="openCreate" 
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-extrabold text-xs inline-flex items-center gap-2 shadow-sm transition">
            <x-lucide-plus-circle class="w-4 h-4" />
            Upload Capaian / Link Drive
        </button>
    </div>

    <!-- Alert Notifications -->
    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-bold">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-bold">
                <x-lucide-alert-circle class="w-4 h-4 text-rose-600" />
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-900">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
    @endif

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Diajukan</span>
                <div class="p-2 bg-blue-50 text-blue-700 rounded-xl border border-blue-200">
                    <x-lucide-file-text class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">{{ number_format($summary['total']) }} <span class="text-xs font-bold text-stone-400">Berkas</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Capaian &amp; pengembangan diri</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Sudah Evaluasi</span>
                <div class="p-2 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200">
                    <x-lucide-check-circle class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">{{ number_format($summary['dinilai']) }} <span class="text-xs font-bold text-stone-400">Berkas</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Telah dinilai Super Admin</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
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
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4 hover:border-purple-300 transition duration-200">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-stone-100 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 bg-stone-100 border border-stone-200 text-stone-800 rounded-md text-[10px] font-extrabold uppercase">
                                {{ str_replace('_', ' ', $item->kategori) }}
                            </span>
                            @if ($item->status_penilaian === 'dinilai')
                                <span class="px-2.5 py-0.5 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-[10px] font-black uppercase">
                                    Dinilai Super Admin
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 bg-amber-100 border border-amber-300 text-amber-900 rounded-full text-[10px] font-black uppercase">
                                    Menunggu Evaluasi
                                </span>
                            @endif
                        </div>
                        <h3 class="text-base font-extrabold text-stone-900 tracking-tight mt-1.5">{{ $item->judul }}</h3>
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($item->link_gdrive)
                            <a href="{{ $item->link_gdrive }}" target="_blank" rel="noopener noreferrer"
                               class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-xl font-bold text-xs inline-flex items-center gap-1.5 transition">
                                <x-lucide-external-link class="w-3.5 h-3.5" />
                                Buka Google Drive
                            </a>
                        @endif

                        @if ($item->status_penilaian !== 'dinilai')
                            <button type="button" wire:click="openEdit({{ $item->id }})" 
                                    class="p-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-lg text-xs font-bold transition">
                                <x-lucide-edit-3 class="w-4 h-4" />
                            </button>
                            <button type="button" wire:click="delete({{ $item->id }})" 
                                    data-confirm="Apakah Anda yakin ingin menghapus data pengajuan ini?"
                                    class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold transition cursor-pointer">
                                <x-lucide-trash-2 class="w-4 h-4" />
                            </button>
                        @endif
                    </div>
                </div>

                @if ($item->deskripsi)
                    <p class="text-xs text-stone-600 leading-relaxed font-medium bg-stone-50/70 p-3 rounded-xl border border-stone-100">
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

    <!-- Modal Form Submit / Edit -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-stone-200 p-6 space-y-5 animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                    <div>
                        <h3 class="text-base font-black text-stone-900">
                            {{ $capaianId ? 'Edit Capaian / Link Drive' : 'Unggah Capaian / Link Google Drive' }}
                        </h3>
                        <p class="text-xs text-stone-500 font-semibold">Formulir bukti fisik atau dokumen pendukung pengembangan diri.</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="text-stone-400 hover:text-stone-600 p-1">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4 text-xs font-medium">
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Judul Capaian / Kegiatan <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="judul" placeholder="Contoh: Sertifikasi Pelatihan Kurikulum Merdeka 2025" 
                               class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-bold focus:ring-2 focus:ring-purple-500 text-xs" />
                        @error('judul') <span class="text-[11px] text-rose-500 font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                        <select wire:model="kategori" class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-bold focus:ring-2 focus:ring-purple-500">
                            <option value="pengembangan_diri">Pengembangan Diri</option>
                            <option value="capaian_kinerja">Capaian Kinerja Pembelajaran</option>
                            <option value="pelatihan">Pelatihan / Workshop / Bintek</option>
                            <option value="sertifikasi">Sertifikasi &amp; Kompetensi</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Link Google Drive Formulir / Berkas Bukti</label>
                        <input type="url" wire:model="link_gdrive" placeholder="https://drive.google.com/file/d/..." 
                               class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-medium focus:ring-2 focus:ring-purple-500" />
                        <span class="text-[10px] text-stone-400 mt-1 block">Pastikan akses Google Drive diset menjadi "Siapa saja yang memiliki link dapat melihat".</span>
                        @error('link_gdrive') <span class="text-[11px] text-rose-500 font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Deskripsi / Penjelasan Singkat</label>
                        <textarea wire:model="deskripsi" rows="3" placeholder="Tuliskan ringkasan kegiatan atau uraian capaian..." 
                                  class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-medium focus:ring-2 focus:ring-purple-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                        <button type="button" wire:click="closeModal" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black shadow-md transition">
                            Simpan &amp; Ajukan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
