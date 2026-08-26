<div class="space-y-6 font-sans pb-24 md:pb-16">
    <!-- Quick Module Switcher Navigation -->
    <x-guru-module-switcher active="p5" />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Penilaian Kokurikuler Projek Profil Pelajar Pancasila (P5)"
        :steps="[
            ['title' => 'Pilih Kelas & Proyek', 'desc' => 'Pilih kelas perwalian dan judul Proyek P5 yang sedang berlangsung pada semester aktif.'],
            ['title' => 'Rating 1-Klik Segmented', 'desc' => 'Klik tombol opsi rating BB (Belum Berkembang), MB (Mulai Berkembang), BSH (Berkembang Sesuai Harapan), atau SB (Sangat Berkembang).'],
            ['title' => 'Simpan Penilaian P5', 'desc' => 'Klik Simpan Penilaian P5 di kanan atas atau tombol simpan melayang untuk memperbarui seluruh matriks capaian kokurikuler.']
        ]"
        notes="Setiap sub-dimensi P5 yang dinilai akan otomatis terangkum dalam narasi lembar Rapor Kokurikuler P5 siswa."
    />

    <!-- Header Section -->
    <x-page-header 
        title="Penilaian Kokurikuler Projek P5" 
        subtitle="Klik 1-kali tombol rating (BB, MB, BSH, SB) untuk menilai capaian kualitatif profil pelajar Pancasila santri."
        badge="PROJEK P5"
        badgeVariant="cyan"
        icon="sparkles"
    >
        <x-slot:actions>
            <x-button variant="primary" size="md" icon="check" wire:click="saveP5Scores" loadingTarget="saveP5Scores" class="w-full sm:w-auto">
                Simpan Penilaian P5
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Filter Bar Card -->
    <div class="bg-white border border-stone-200 p-4 sm:p-6 rounded-2xl shadow-xs space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Kelas</label>
                <select wire:model.live="kelas_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl text-stone-900 px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-cyan-500 focus:bg-white shadow-2xs">
                    @foreach($kelases as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Proyek P5</label>
                <select wire:model.live="proyek_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl text-stone-900 px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-cyan-500 focus:bg-white shadow-2xs">
                    @foreach($proyeks as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_proyek }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Semester</label>
                <select wire:model.live="semester_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl text-stone-900 px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-cyan-500 focus:bg-white shadow-2xs">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->tahunAjaran->nama ?? '' }} - {{ ucfirst($sem->semester) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Filter Livewire Loading Bar Indicator -->
        <div wire:loading.delay wire:target="kelas_id, proyek_id, semester_id" class="w-full">
            <x-loading-state type="bar" target="kelas_id, proyek_id, semester_id" />
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 p-4 rounded-2xl text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <span class="px-2.5 py-0.5 bg-emerald-200 text-emerald-900 rounded-lg font-black text-[10px]">Tersimpan</span>
        </div>
    @endif

    <!-- Matrix Table Container -->
    <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-xs">
        <!-- Table Header Bar -->
        <div class="p-3.5 sm:p-4 bg-stone-50 border-b border-stone-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <span class="text-xs font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-600"></span>
                Matriks Penilaian Capaian Dimensi & Sub-Dimensi P5
            </span>
            <span class="text-[11px] text-stone-500 font-semibold">Total {{ count($siswas) }} Siswa Terdaftar</span>
        </div>

        <!-- Mobile Horizontal Scroll Hint -->
        <div class="flex items-center gap-1.5 px-4 py-2 bg-cyan-50/70 border-b border-cyan-100 text-[11px] font-bold text-cyan-800 md:hidden">
            <svg class="w-3.5 h-3.5 text-cyan-600 animate-pulse shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            <span>Geser tabel ke samping untuk menilai seluruh sub-dimensi P5</span>
        </div>

        <!-- Horizontal Scroll Table Wrapper -->
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-separate border-spacing-0 text-xs text-stone-800">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider select-none">
                    <tr>
                        <!-- No Column: Sticky on desktop, hidden on mobile -->
                        <th class="hidden md:table-cell w-12 min-w-[48px] p-3 text-center sticky left-0 bg-emerald-800 text-white font-extrabold text-xs z-20 border-b border-r border-emerald-700">
                            No
                        </th>
                        
                        <!-- Nama Siswa: Sticky on left for both mobile & desktop -->
                        <th class="sticky left-0 md:left-12 z-20 min-w-[125px] max-w-[140px] md:min-w-[200px] md:max-w-none p-2.5 md:p-3 bg-emerald-800 text-white font-extrabold text-xs border-b border-r border-emerald-700 shadow-[3px_0_5px_-2px_rgba(0,0,0,0.25)] md:shadow-none">
                            Nama Siswa
                        </th>

                        <!-- Dynamic Dimensions & Subdimensions -->
                        @foreach($dimensis as $dim)
                            @foreach($dim->subdimensi as $sub)
                                <th class="min-w-[150px] sm:min-w-[175px] p-2.5 text-center bg-emerald-800 text-white border-b border-r border-emerald-700" title="{{ $dim->nama_dimensi }} - {{ $sub->nama_subdimensi }}">
                                    <span class="block text-white font-black text-xs truncate max-w-[150px] sm:max-w-[175px] mx-auto">{{ $dim->nama_dimensi }}</span>
                                    <span class="text-[10px] text-emerald-200 font-semibold block truncate max-w-[150px] sm:max-w-[175px] mx-auto mt-0.5">{{ $sub->nama_subdimensi }}</span>
                                </th>
                            @endforeach
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($siswas as $index => $s)
                        <tr class="hover:bg-stone-50 transition group">
                            <!-- No (Desktop Only) -->
                            <td class="hidden md:table-cell w-12 min-w-[48px] p-3 text-center sticky left-0 bg-white group-hover:bg-stone-50 font-bold text-stone-500 border-b border-r border-stone-200 text-xs z-10 transition">
                                {{ $index + 1 }}
                            </td>

                            <!-- Nama Siswa (Sticky Left with Shadow on Mobile) -->
                            <td class="p-2.5 md:p-3 sticky left-0 md:left-12 bg-white group-hover:bg-stone-50 font-bold text-stone-900 border-b border-r-2 md:border-r border-stone-200 text-xs z-10 shadow-[3px_0_6px_-2px_rgba(0,0,0,0.12)] md:shadow-none min-w-[125px] max-w-[140px] md:min-w-[200px] md:max-w-none transition">
                                <div class="line-clamp-2 text-xs font-extrabold text-stone-900 leading-tight">
                                    {{ $s->user->nama ?? $s->nama_panggilan }}
                                </div>
                                <span class="block text-[10px] text-stone-500 font-mono font-normal mt-0.5 truncate">NISN: {{ $s->nisn }}</span>
                            </td>

                            <!-- Dynamic Dimension Segmented Rating Controls -->
                            @foreach($dimensis as $dim)
                                @foreach($dim->subdimensi as $sub)
                                    @php
                                        $currentVal = $nilaiP5Matrix[$s->id][$sub->id][1] ?? '';
                                    @endphp
                                    <td class="p-2.5 text-center border-b border-r border-stone-200">
                                        <!-- Segmented Rating Control -->
                                        <div class="inline-flex p-1 bg-stone-100 border border-stone-300 rounded-xl gap-1 shadow-2xs">
                                            <button 
                                                type="button"
                                                wire:click="$set('nilaiP5Matrix.{{ $s->id }}.{{ $sub->id }}.1', 1)"
                                                class="px-2 py-1 text-[10px] font-black rounded-lg transition cursor-pointer {{ (string)$currentVal === '1' ? 'bg-rose-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200' }}"
                                                title="1: Belum Berkembang (BB)"
                                            >BB</button>
                                            <button 
                                                type="button"
                                                wire:click="$set('nilaiP5Matrix.{{ $s->id }}.{{ $sub->id }}.1', 2)"
                                                class="px-2 py-1 text-[10px] font-black rounded-lg transition cursor-pointer {{ (string)$currentVal === '2' ? 'bg-amber-500 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200' }}"
                                                title="2: Mulai Berkembang (MB)"
                                            >MB</button>
                                            <button 
                                                type="button"
                                                wire:click="$set('nilaiP5Matrix.{{ $s->id }}.{{ $sub->id }}.1', 3)"
                                                class="px-2 py-1 text-[10px] font-black rounded-lg transition cursor-pointer {{ (string)$currentVal === '3' ? 'bg-emerald-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200' }}"
                                                title="3: Berkembang Sesuai Harapan (BSH)"
                                            >BSH</button>
                                            <button 
                                                type="button"
                                                wire:click="$set('nilaiP5Matrix.{{ $s->id }}.{{ $sub->id }}.1', 4)"
                                                class="px-2 py-1 text-[10px] font-black rounded-lg transition cursor-pointer {{ (string)$currentVal === '4' ? 'bg-cyan-700 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200' }}"
                                                title="4: Sangat Berkembang (SB)"
                                            >SB</button>
                                        </div>
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @empty
                        <x-table.empty :colspan="2 + $dimensis->sum(fn($d) => $d->subdimensi->count())" title="Belum ada data siswa" message="Pilih Proyek dan Rombel Kelas untuk memulai penilaian P5." />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Floating Mobile Bottom Action Bar -->
    @if(count($siswas) > 0)
        <div class="fixed bottom-4 left-4 right-4 z-30 md:hidden">
            <button 
                type="button" 
                wire:click="saveP5Scores" 
                class="w-full py-3 px-4 bg-cyan-700 active:bg-cyan-800 text-white rounded-2xl font-bold text-xs shadow-xl flex items-center justify-center gap-2 border border-cyan-600/80 cursor-pointer"
            >
                <svg class="w-4 h-4 text-cyan-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Simpan Penilaian P5 ({{ count($siswas) }} Siswa)</span>
            </button>
        </div>
    @endif
</div>
