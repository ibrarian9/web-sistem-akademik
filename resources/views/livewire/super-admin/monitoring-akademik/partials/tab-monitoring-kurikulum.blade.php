<!-- ==================== TAB 2: MONITORING BAB & TP ==================== -->
@if ($activeTab === 'kurikulum')
    <div class="space-y-6">
        <!-- Filter & Search Bar -->
        <div class="bg-white border border-stone-200 rounded-2xl shadow-xs p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="w-full md:w-1/3">
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Pilih Mata Pelajaran</label>
                <select wire:model.live="kurikulumMapelId" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                    @foreach ($mapels as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-1/2">
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Cari Bab atau Tujuan Pembelajaran (TP)</label>
                <div class="relative">
                    <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3.5 top-3" />
                    <input type="text" wire:model.live.debounce.300ms="searchBabTp" placeholder="Ketik kata kunci nama materi atau tujuan pembelajaran..." class="w-full rounded-xl border border-stone-200 bg-stone-50 pl-10 pr-4 py-2.5 text-xs font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>
        </div>

        <!-- Guru Pengampu & Summary Header -->
        @if ($kurikulumData['mapel'])
            <div class="bg-emerald-50/70 border border-emerald-200 rounded-2xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-600 text-white">
                            KURIKULUM MERDEKA
                        </span>
                        <h3 class="text-base font-extrabold text-stone-900">{{ $kurikulumData['mapel']->nama_mapel }}</h3>
                    </div>
                    <p class="text-xs text-stone-600">
                        Total: <strong class="text-emerald-800">{{ $kurikulumData['babs']->count() }} Bab (Lingkup Materi)</strong> 
                        &bull; <strong class="text-emerald-800">{{ $kurikulumData['babs']->sum(fn($b) => $b->tujuanPembelajaran->count()) }} Total TP</strong>
                    </p>
                </div>

                <!-- Assigned Teachers Pill -->
                <div class="text-xs text-stone-600 flex flex-wrap items-center gap-2">
                    <span class="font-bold text-stone-500">Guru Pengampu:</span>
                    @forelse ($kurikulumData['guruList']->pluck('guru.user.nama')->unique() as $guruName)
                        <span class="px-3 py-1 bg-white border border-stone-200 rounded-xl font-bold text-stone-800 shadow-2xs">
                            {{ $guruName }}
                        </span>
                    @empty
                        <span class="italic text-stone-400">Belum diplot ke kelas</span>
                    @endforelse
                </div>
            </div>

            <!-- Template Narasi Info -->
            @if ($kurikulumData['template'])
                <div class="bg-stone-50 border border-stone-200 rounded-2xl p-4 text-xs text-stone-600 space-y-1">
                    <div class="font-bold text-stone-700 flex items-center gap-1.5">
                        <x-lucide-info class="w-4 h-4 text-emerald-600" />
                        <span>Format Template Narasi Rapor Otomatis:</span>
                    </div>
                    <p>&bull; Capaian Tertinggi: <span class="font-semibold text-emerald-800">"Ananda {{ $kurikulumData['template']->frasa_tertinggi }} [Deskripsi TP]"</span></p>
                    <p>&bull; Capaian Terendah: <span class="font-semibold text-amber-800">"namun {{ $kurikulumData['template']->frasa_terendah }} [Deskripsi TP]"</span></p>
                </div>
            @endif

            <!-- List of Babs and TPs -->
            <div class="space-y-4">
                @forelse ($kurikulumData['babs'] as $bab)
                    <div class="bg-white border border-stone-200 rounded-2xl shadow-xs overflow-hidden">
                        <div class="p-4 bg-stone-50/80 border-b border-stone-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                    {{ $bab->urutan }}
                                </span>
                                <div>
                                    <h4 class="text-sm font-extrabold text-stone-900">{{ $bab->nama_lingkup_materi }}</h4>
                                    <span class="text-[10px] text-stone-400 font-semibold uppercase">Bab / Lingkup Materi {{ $bab->urutan }}</span>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-extrabold">
                                {{ $bab->tujuanPembelajaran->count() }} TP Disusun
                            </span>
                        </div>

                        <div class="p-4 divide-y divide-stone-100">
                            @forelse ($bab->tujuanPembelajaran as $idx => $tp)
                                <div class="py-3 first:pt-0 last:pb-0 flex items-start gap-3">
                                    <span class="px-2 py-0.5 bg-stone-100 border border-stone-200 rounded-md text-[10px] font-black text-stone-600 shrink-0 mt-0.5">
                                        TP {{ $tp->urutan ?? ($idx + 1) }}
                                    </span>
                                    <p class="text-xs font-semibold text-stone-800 leading-relaxed">
                                        {{ $tp->deskripsi_tp }}
                                    </p>
                                </div>
                            @empty
                                <div class="py-4 text-center text-xs text-stone-400 italic">
                                    Belum ada rincian Tujuan Pembelajaran (TP) yang dimasukkan untuk Bab ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="bg-white border border-stone-200 rounded-3xl p-12 text-center space-y-2">
                        <x-lucide-layers class="w-10 h-10 text-stone-300 mx-auto" />
                        <h4 class="text-sm font-bold text-stone-700">Belum Ada Bab & TP yang Disusun</h4>
                        <p class="text-xs text-stone-400">Guru pengampu mata pelajaran ini belum membuat Lingkup Materi pada portal Guru.</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
@endif
