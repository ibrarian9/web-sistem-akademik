<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold text-stone-900 tracking-tight">Ekstrakurikuler Saya</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    Pengembangan Diri
                </span>
            </div>
            <p class="text-xs text-stone-500 mt-1">Kelola keanggotaan dan lihat penilaian kegiatan ekstrakurikuler Anda.</p>
        </div>
        <div class="flex items-center gap-3 px-4 py-2.5 bg-white border border-stone-200 rounded-2xl shadow-sm">
            <div class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-700 font-bold text-xs">
                <x-lucide-star class="w-4 h-4" />
            </div>
            <div>
                <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Status Keikutsertaan</span>
                <span class="text-xs font-bold text-stone-800">
                    {{ count($enrolledEkskuls) }} Kegiatan Aktif
                </span>
            </div>
        </div>
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk & Informasi Ekstrakurikuler Siswa"
        :steps="[
            ['title' => 'Ekskul Aktif', 'desc' => 'Lihat daftar kegiatan ekstrakurikuler yang sedang diikuti beserta nama pembina.'],
            ['title' => 'Predikat & Evaluasi', 'desc' => 'Setiap akhir semester, Guru Pembina memberi predikat (A/B/C/D) dan catatan evaluasi.'],
            ['title' => 'Katalog Ekskul', 'desc' => 'Jelajahi katalog ekstrakurikuler sekolah untuk melihat pilihan minat dan bakat yang tersedia.']
        ]"
    />

    <!-- Section 1: Ekstrakurikuler Yang Diikuti -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                Ekstrakurikuler yang Saya Diikuti
            </h3>
            <span class="text-xs text-stone-500 font-medium">Semester {{ ucfirst($activeSemester->semester ?? 'Aktif') }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse ($enrolledEkskuls as $item)
                <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-4 relative overflow-hidden group hover:border-amber-400 transition duration-300">
                    <div class="flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <h4 class="text-base font-bold text-stone-900 group-hover:text-amber-700 transition">{{ $item->ekstrakurikuler->nama }}</h4>
                            <div class="flex items-center gap-2 text-xs text-stone-600 font-medium">
                                <x-lucide-user class="w-3.5 h-3.5 text-stone-400 shrink-0" />
                                <span>Pembina: <strong class="text-stone-800">{{ $item->ekstrakurikuler->pembina->user->nama ?? 'Guru Pembina' }}</strong></span>
                            </div>
                        </div>

                        <!-- Predikat Badge -->
                        <div class="px-3 py-1.5 rounded-xl border text-center shrink-0
                            {{ $item->predikat === 'A' ? 'bg-emerald-50 border-emerald-300 text-emerald-800' : '' }}
                            {{ $item->predikat === 'B' ? 'bg-indigo-50 border-indigo-300 text-indigo-800' : '' }}
                            {{ $item->predikat === 'C' ? 'bg-amber-50 border-amber-300 text-amber-800' : '' }}
                            {{ $item->predikat === 'D' ? 'bg-rose-50 border-rose-300 text-rose-800' : '' }}
                        ">
                            <span class="text-[9px] font-bold uppercase block tracking-wider text-stone-500">Predikat</span>
                            <span class="text-lg font-black">{{ $item->predikat ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <p class="text-xs text-stone-700 font-medium leading-relaxed bg-stone-50 p-3 rounded-xl border border-stone-200">
                        {{ $item->ekstrakurikuler->deskripsi ?: 'Tidak ada deskripsi khusus.' }}
                    </p>

                    <!-- Teacher Evaluation Note -->
                    <div class="pt-2 border-t border-stone-200 flex items-start gap-2.5 text-xs text-stone-800">
                        <x-lucide-message-square class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" />
                        <div>
                            <span class="text-[10px] font-bold text-indigo-800 uppercase tracking-wider block">Catatan Guru Pembina</span>
                            <p class="text-stone-800 text-xs italic font-semibold">"{{ $item->catatan ?: 'Belum ada catatan evaluasi khusus.' }}"</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 bg-white border border-stone-200 rounded-2xl p-8 text-center space-y-3 shadow-sm">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto border border-amber-200">
                        <x-lucide-star class="w-6 h-6" />
                    </div>
                    <div class="space-y-1 max-w-sm mx-auto">
                        <h4 class="text-sm font-bold text-stone-900">Belum Terdaftar di Ekstrakurikuler</h4>
                        <p class="text-xs text-stone-500 font-medium">Anda belum terdaftar dalam kegiatan ekstrakurikuler semester ini. Silakan hubungi Guru Pembina atau Wali Kelas Anda untuk pendaftaran.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section 2: Katalog Ekstrakurikuler Sekolah -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-200 pb-4">
            <div>
                <h3 class="text-sm font-bold text-stone-900 uppercase tracking-wider">Katalog Ekstrakurikuler Sekolah</h3>
                <p class="text-xs text-stone-500">Daftar seluruh kegiatan ekstrakurikuler yang diselenggarakan oleh sekolah.</p>
            </div>

            <!-- Search Catalog -->
            <div class="w-full sm:w-64 relative">
                <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="text" wire:model.live="searchCatalog" placeholder="Cari nama ekskul..." 
                       class="w-full bg-stone-50 border border-stone-300 rounded-xl pl-9 pr-3 py-1.5 text-xs text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
            </div>
        </div>

        <!-- Catalog Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($catalogEkskuls as $cat)
                @php
                    $isEnrolled = in_array($cat->id, $enrolledIds);
                @endphp
                <div class="bg-stone-50 border border-stone-200 rounded-xl p-4 space-y-3 flex flex-col justify-between hover:border-stone-300 transition">
                    <div class="space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="text-xs font-bold text-stone-900">{{ $cat->nama }}</h4>
                            @if ($isEnrolled)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
                                    Aktif Diikuti
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-stone-200 text-stone-700 shrink-0">
                                    Tersedia
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed line-clamp-3 font-medium">
                            {{ $cat->deskripsi ?: 'Kegiatan pembinaan minat dan bakat siswa.' }}
                        </p>
                    </div>

                    <div class="pt-3 border-t border-stone-200 flex items-center justify-between text-xs text-stone-600 font-medium">
                        <span class="flex items-center gap-1 text-stone-700">
                            <x-lucide-user-check class="w-3.5 h-3.5 text-amber-600" />
                            {{ $cat->pembina->user->nama ?? 'Pembina' }}
                        </span>
                        <span class="flex items-center gap-1 text-stone-500">
                            <x-lucide-users class="w-3.5 h-3.5" />
                            {{ $cat->siswaEkskul->count() }} Anggota
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-stone-500 text-xs font-medium">
                    Tidak ditemukan kegiatan ekstrakurikuler dengan kata kunci tersebut.
                </div>
            @endforelse
        </div>
    </div>
</div>
