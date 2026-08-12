<div class="space-y-6 font-sans">
    @php
        $jGuru = strtolower(auth()->user()->guru?->jenis_guru ?? 'umum');
        if ($jGuru === 'tahfidz') $jGuru = 'tahfizh';
        $isTahfizh = $jGuru === 'tahfizh';
        $isUmum = $jGuru === 'umum';
        $isKeduanya = $jGuru === 'keduanya' || auth()->user()->role?->nama !== 'guru';
    @endphp

    <!-- Quick Module Switcher Header (Light Theme) -->
    <div class="flex items-center gap-2 bg-white border border-stone-200 p-2 rounded-2xl overflow-x-auto shadow-xs">
        @if($isUmum || $isKeduanya)
            <a href="{{ route('guru.kurikulum-merdeka') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 01-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Setup Bab &amp; TP</span>
            </a>
            <a href="{{ route('guru.input-sumatif') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Nilai Sumatif</span>
            </a>
        @endif

        @if($isTahfizh || $isKeduanya)
            <a href="{{ route('guru.input-tahfidz') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Setoran Tahfizh</span>
            </a>
        @endif

        @if($isUmum || $isKeduanya)
            <a href="{{ route('guru.penilaian-p5') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold bg-cyan-700 text-white shadow-sm flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-cyan-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                <span>Penilaian P5</span>
            </a>
        @endif

        <a href="{{ route('guru.kelola-rapor') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Lihat Rapor Murid</span>
        </a>
    </div>


    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Penilaian Kokurikuler Projek Profil Pelajar Pancasila (P5)"
        :steps="[
            ['title' => 'Pilih Kelas & Proyek', 'desc' => 'Pilih kelas perwalian dan judul Proyek P5 yang sedang berlangsung pada semester aktif.'],
            ['title' => 'Rating 1-Klik Segmented', 'desc' => 'Klik tombol opsi rating BB (Belum Berkembang), MB (Mulai), BSH (Sesuai Harapan), atau SB (Sangat Berkembang).'],
            ['title' => 'Simpan Penilaian P5', 'desc' => 'Klik Simpan Penilaian P5 di sudut kanan atas untuk memperbarui seluruh matriks capaian kokurikuler.']
        ]"
        notes="Setiap sub-dimensi P5 yang dinilai akan otomatis terangkum dalam narasi lembar Rapor Kokurikuler P5 siswa."
    />

    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm space-y-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1 bg-cyan-100 border border-cyan-300 text-cyan-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
                    Projek Profil Pelajar Pancasila (P5)
                </span>
                <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight mt-1">Penilaian Kokurikuler P5</h1>
                <p class="text-stone-600 text-xs font-medium">Klik 1-kali tombol rating (BB, MB, BSH, SB) untuk menilai capaian kualitatif siswa.</p>
            </div>
            <button wire:click="saveP5Scores" class="bg-cyan-700 hover:bg-cyan-800 text-white font-bold px-6 py-2.5 rounded-xl text-xs flex items-center gap-2 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Penilaian P5
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Kelas</label>
                <select wire:model.live="kelas_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-4 py-2.5 text-xs font-bold focus:ring-2 focus:ring-cyan-500">
                    @foreach($kelases as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Proyek P5</label>
                <select wire:model.live="proyek_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-4 py-2.5 text-xs font-bold focus:ring-2 focus:ring-cyan-500">
                    @foreach($proyeks as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_proyek }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Semester</label>
                <select wire:model.live="semester_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-4 py-2.5 text-xs font-bold focus:ring-2 focus:ring-cyan-500">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->tahunAjaran->nama ?? '' }} - {{ ucfirst($sem->semester) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 p-4 rounded-xl text-xs font-bold">
            {{ session('message') }}
        </div>
    @endif

    <!-- Matrix Table (High Contrast Light Theme) -->
    <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs text-stone-800">
                <thead class="bg-stone-100 text-stone-700 font-extrabold uppercase tracking-wider border-b border-stone-200">
                    <tr>
                        <th class="p-3.5 sticky left-0 bg-stone-100 z-10 w-12 text-center border-r border-stone-200">No</th>
                        <th class="p-3.5 sticky left-12 bg-stone-100 z-10 min-w-[200px] border-r border-stone-200">Nama Siswa</th>
                        @foreach($dimensis as $dim)
                            @foreach($dim->subdimensi as $sub)
                                <th class="p-3 text-center border-r border-stone-200 min-w-[170px]">
                                    <span class="block text-cyan-800 font-black truncate max-w-[170px]">{{ $dim->nama_dimensi }}</span>
                                    <span class="text-[10px] text-stone-500 font-semibold block truncate max-w-[170px]">{{ $sub->nama_subdimensi }}</span>
                                </th>
                            @endforeach
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
                    @forelse($siswas as $index => $s)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-3.5 text-center sticky left-0 bg-white font-bold text-stone-500 border-r border-stone-200">{{ $index + 1 }}</td>
                            <td class="p-3.5 sticky left-12 bg-white font-bold text-stone-900 border-r border-stone-200">
                                {{ $s->user->nama ?? $s->nama_panggilan }}
                            </td>
                            @foreach($dimensis as $dim)
                                @foreach($dim->subdimensi as $sub)
                                    @php
                                        $currentVal = $nilaiP5Matrix[$s->id][$sub->id][1] ?? '';
                                    @endphp
                                    <td class="p-3 text-center border-r border-stone-200">
                                        <!-- Segmented Rating Control (High Contrast Light Theme) -->
                                        <div class="inline-flex p-1 bg-stone-100 border border-stone-300 rounded-xl gap-1">
                                            <button 
                                                type="button"
                                                wire:click="$set('nilaiP5Matrix.{{ $s->id }}.{{ $sub->id }}.1', 1)"
                                                class="px-2 py-1 text-[10px] font-black rounded-lg transition {{ (string)$currentVal === '1' ? 'bg-rose-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200' }}"
                                                title="1: Belum Berkembang (BB)"
                                            >BB</button>
                                            <button 
                                                type="button"
                                                wire:click="$set('nilaiP5Matrix.{{ $s->id }}.{{ $sub->id }}.1', 2)"
                                                class="px-2 py-1 text-[10px] font-black rounded-lg transition {{ (string)$currentVal === '2' ? 'bg-amber-500 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200' }}"
                                                title="2: Mulai Berkembang (MB)"
                                            >MB</button>
                                            <button 
                                                type="button"
                                                wire:click="$set('nilaiP5Matrix.{{ $s->id }}.{{ $sub->id }}.1', 3)"
                                                class="px-2 py-1 text-[10px] font-black rounded-lg transition {{ (string)$currentVal === '3' ? 'bg-emerald-600 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200' }}"
                                                title="3: Berkembang Sesuai Harapan (BSH)"
                                            >BSH</button>
                                            <button 
                                                type="button"
                                                wire:click="$set('nilaiP5Matrix.{{ $s->id }}.{{ $sub->id }}.1', 4)"
                                                class="px-2 py-1 text-[10px] font-black rounded-lg transition {{ (string)$currentVal === '4' ? 'bg-cyan-700 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200' }}"
                                                title="4: Sangat Berkembang (SB)"
                                            >SB</button>
                                        </div>
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center text-stone-500 font-medium italic">
                                Tidak ada siswa terdaftar pada kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
