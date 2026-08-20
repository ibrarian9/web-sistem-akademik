<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-purple-100 border border-purple-300 text-purple-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
                SD Tahfizh F3 Super Admin
            </span>
            <h2 class="text-xl font-extrabold text-stone-900 tracking-tight mt-1">Evaluasi Capaian &amp; Pengembangan Diri Guru</h2>
            <p class="text-xs text-stone-500 font-medium">Buka link Google Drive formulir/berkas bukti yang diunggah guru, berikan penilaian skor, predikat, dan catatan feedback.</p>
        </div>
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

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Berkas Diajukan</span>
                <div class="p-2 bg-blue-50 text-blue-700 rounded-xl border border-blue-200">
                    <x-lucide-file-text class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">{{ number_format($totalPengajuan) }} <span class="text-xs font-bold text-stone-400">Pengajuan</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Dari seluruh Ustadz/Ustadzah</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Belum Dinilai</span>
                <div class="p-2 bg-amber-50 text-amber-700 rounded-xl border border-amber-200">
                    <x-lucide-clock class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600">{{ number_format($belumDinilai) }} <span class="text-xs font-bold text-stone-400">Berkas</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Membutuhkan review Super Admin</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Sudah Evaluasi</span>
                <div class="p-2 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200">
                    <x-lucide-check-circle class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-700">{{ number_format($sudahDinilai) }} <span class="text-xs font-bold text-stone-400">Berkas</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Telah memiliki nilai &amp; feedback</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
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

    <!-- Controls: Search & Filters -->
    <div class="bg-white border border-stone-200 p-4 rounded-2xl shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto flex-1">
            <div class="relative w-full sm:w-64">
                <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3.5 top-3" />
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama guru / judul..." 
                       class="w-full pl-10 pr-4 py-2 bg-stone-50 border border-stone-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-purple-500 focus:bg-white transition" />
            </div>

            <select wire:model.live="filterGuru" class="w-full sm:w-48 py-2 px-3 bg-stone-50 border border-stone-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-purple-500 focus:bg-white transition">
                <option value="">Semua Guru</option>
                @foreach ($gurus as $g)
                    <option value="{{ $g->id }}">{{ $g->user->nama ?? '-' }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterKategori" class="w-full sm:w-44 py-2 px-3 bg-stone-50 border border-stone-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-purple-500 focus:bg-white transition">
                <option value="">Semua Kategori</option>
                <option value="pengembangan_diri">Pengembangan Diri</option>
                <option value="capaian_kinerja">Capaian Kinerja</option>
                <option value="pelatihan">Pelatihan / Workshop</option>
                <option value="sertifikasi">Sertifikasi</option>
            </select>

            <select wire:model.live="filterStatus" class="w-full sm:w-40 py-2 px-3 bg-stone-50 border border-stone-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-purple-500 focus:bg-white transition">
                <option value="">Semua Status</option>
                <option value="diajukan">Belum Dinilai</option>
                <option value="dinilai">Sudah Dinilai</option>
            </select>
        </div>
    </div>

    <!-- Table Submissions -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 font-extrabold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4">Guru Pengampu</th>
                        <th class="py-3.5 px-4">Judul Capaian &amp; Kategori</th>
                        <th class="py-3.5 px-4 text-center">Berkas Form Google Drive</th>
                        <th class="py-3.5 px-4 text-center">Status &amp; Hasil Penilaian</th>
                        <th class="py-3.5 px-4 text-center">Aksi Evaluasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 font-medium">
                    @forelse ($capaianList as $item)
                        <tr class="hover:bg-stone-50/80 transition duration-150">
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-stone-900 text-xs">{{ $item->guru->user->nama ?? '-' }}</div>
                                <div class="text-[10px] text-stone-500 font-semibold">NIP: {{ $item->guru->nip ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-stone-900 leading-snug">{{ $item->judul }}</div>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-stone-100 border border-stone-200 text-stone-700 rounded-md text-[10px] font-bold uppercase">
                                        {{ str_replace('_', ' ', $item->kategori) }}
                                    </span>
                                </div>
                                @if ($item->deskripsi)
                                    <div class="text-[11px] text-stone-500 mt-1 line-clamp-1 italic">{{ $item->deskripsi }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($item->link_gdrive)
                                    <a href="{{ $item->link_gdrive }}" target="_blank" rel="noopener noreferrer" 
                                       class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-xl font-bold text-[11px] inline-flex items-center gap-1.5 transition">
                                        <x-lucide-external-link class="w-3.5 h-3.5" />
                                        Buka Google Drive
                                    </a>
                                @else
                                    <span class="text-stone-400 italic text-[11px]">Tidak ada link</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($item->status_penilaian === 'dinilai')
                                    <div class="space-y-1">
                                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-full font-black text-[10px] uppercase inline-block">
                                            Dinilai
                                        </span>
                                        <div class="text-xs font-black text-purple-700">
                                            Skor: {{ $item->skor_nilai }} ({{ $item->predikat }})
                                        </div>
                                    </div>
                                @else
                                    <span class="px-2.5 py-0.5 bg-amber-100 text-amber-900 border border-amber-300 rounded-full font-black text-[10px] uppercase inline-block">
                                        Belum Dinilai
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" wire:click="openEvaluateModal({{ $item->id }})" 
                                            class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold text-[11px] inline-flex items-center gap-1 shadow-sm transition">
                                        <x-lucide-edit-3 class="w-3.5 h-3.5" />
                                        {{ $item->status_penilaian === 'dinilai' ? 'Edit Nilai' : 'Beri Nilai' }}
                                    </button>

                                    <button type="button" wire:click="delete({{ $item->id }})" 
                                            data-confirm="Apakah Anda yakin ingin menghapus data pengajuan ini?"
                                            class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl font-bold transition cursor-pointer">
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-stone-400 font-medium">
                                <x-lucide-file-x class="w-8 h-8 mx-auto mb-2 opacity-50" />
                                Tidak ada data pengajuan capaian guru ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($capaianList->hasPages())
            <div class="p-4 border-t border-stone-200 bg-stone-50">
                {{ $capaianList->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Evaluation Super Admin -->
    @if ($showEvaluateModal && $selectedCapaian)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-stone-200 p-6 space-y-5 animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                    <div>
                        <span class="px-2.5 py-0.5 bg-purple-100 text-purple-800 rounded-full text-[10px] font-extrabold uppercase">
                            Evaluasi Super Admin
                        </span>
                        <h3 class="text-base font-black text-stone-900 mt-1">Penilaian Capaian Guru</h3>
                        <p class="text-xs text-stone-500 font-semibold">{{ $selectedCapaian->guru->user->nama ?? 'Guru Pengampu' }}</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="text-stone-400 hover:text-stone-600 p-1">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <!-- Info Box Pengajuan -->
                <div class="p-3 bg-stone-50 border border-stone-200 rounded-2xl space-y-2 text-xs">
                    <div class="font-bold text-stone-900">{{ $selectedCapaian->judul }}</div>
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
                            <label class="block font-bold text-stone-700 mb-1">Skor Nilai (0-100) <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.1" min="0" max="100" wire:model="skor_nilai" placeholder="Contoh: 88.5" 
                                   class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-bold focus:ring-2 focus:ring-purple-500 text-sm" />
                            @error('skor_nilai') <span class="text-[11px] text-rose-500 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-stone-700 mb-1">Predikat <span class="text-rose-500">*</span></label>
                            <select wire:model="predikat" class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-bold focus:ring-2 focus:ring-purple-500">
                                <option value="Sangat Baik">Sangat Baik</option>
                                <option value="Baik">Baik</option>
                                <option value="Cukup">Cukup</option>
                                <option value="Perlu Bimbingan">Perlu Bimbingan</option>
                            </select>
                            @error('predikat') <span class="text-[11px] text-rose-500 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Tanggal Penilaian <span class="text-rose-500">*</span></label>
                        <input type="date" wire:model="tanggal_penilaian" class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-bold focus:ring-2 focus:ring-purple-500" />
                        @error('tanggal_penilaian') <span class="text-[11px] text-rose-500 font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Catatan Feedback / Evaluasi</label>
                        <textarea wire:model="catatan_evaluasi" rows="3" placeholder="Masukan dan arahan dari Super Admin untuk guru..." 
                                  class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-medium focus:ring-2 focus:ring-purple-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                        <button type="button" wire:click="closeModal" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-black shadow-md transition">
                            Simpan Penilaian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
