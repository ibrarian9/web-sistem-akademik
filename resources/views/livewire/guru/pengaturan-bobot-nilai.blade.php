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
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Setoran Tahfizh</span>
            </a>
        @endif

        @if($isUmum || $isKeduanya)
            <a href="{{ route('guru.penilaian-p5') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                <span>Penilaian P5</span>
            </a>
        @endif

        <a href="{{ route('guru.bobot-nilai') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-700 text-white shadow-sm flex items-center gap-2.5 whitespace-nowrap">
            <x-lucide-sliders class="w-4 h-4 text-emerald-100" />
            <span>Bobot Nilai</span>
        </a>

        <a href="{{ route('guru.kelola-rapor') }}" wire:navigate class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Rapor Murid</span>
        </a>
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Konfigurasi Bobot Penilaian Guru"
        :steps="[
            ['title' => 'Pilih Rombel & Mapel', 'desc' => 'Pilih kelas dan mata pelajaran yang Anda ampu untuk menyesuaikan porsi bobot.'],
            ['title' => 'Atur Persentase Bobot', 'desc' => 'Tentukan persentase bobot tiap komponen (UH, UTS, UAS, Tahfizh). Total persentase harus 100%.'],
            ['title' => 'Simpan Bobot', 'desc' => 'Klik Simpan Bobot Penilaian untuk memberlakukan rumus kalkulasi pada Rapor Akhir.']
        ]"
    />

    <!-- Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                KONFIGURASI PENILAIAN
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Pengaturan Bobot Penilaian Guru</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Tentukan sendiri porsi persentase bobot tiap komponen nilai (UH, UTS, UAS, dll.) untuk kelas &amp; mata pelajaran yang Anda ampu.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
            <x-lucide-check-circle class="w-4 h-4 text-emerald-600 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="p-4 bg-amber-50 border border-amber-300 text-amber-900 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
            <x-lucide-alert-triangle class="w-4 h-4 text-amber-600 shrink-0" />
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
        <!-- Class & Subject Selection -->
        <div class="max-w-md space-y-1.5">
            <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Pilih Penugasan Mengajar (Kelas &amp; Mapel)</label>
            <select wire:model.live="selectedGmkId" class="w-full bg-stone-50 border border-stone-300 text-stone-900 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-xs">
                @forelse ($assignments as $a)
                    <option value="{{ $a['id'] }}">
                        Kelas {{ $a['kelas']['nama_kelas'] ?? '-' }} — {{ $a['mapel']['nama_mapel'] ?? '-' }} ({{ strtoupper($a['mapel']['jenis'] ?? 'UMUM') }})
                    </option>
                @empty
                    <option value="">-- Belum ada penugasan mengajar --</option>
                @endforelse
            </select>
        </div>

        @if ($selectedGmkId)
            <form wire:submit.prevent="saveBobot" class="space-y-6 pt-4 border-t border-stone-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">Persentase Bobot Per Komponen Nilai</h3>
                            @if (!empty($selectedAssignment['mapel']))
                                @php
                                    $isTahfidzMapel = $selectedAssignment['mapel']['is_tahfidz'] ?? (strtolower($selectedAssignment['mapel']['kategori'] ?? '') === 'tahfidz');
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $isTahfidzMapel ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-blue-100 text-blue-800 border border-blue-300' }}">
                                    {{ $isTahfidzMapel ? 'Mapel Tahfizh' : 'Mapel Umum' }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-stone-600 font-medium">Nilai akhir murid dihitung berdasarkan persentase bobot yang Anda tetapkan khusus untuk mata pelajaran ini.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($komponens as $k)
                        <div class="bg-stone-50 border border-stone-200 rounded-xl p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold text-stone-900">{{ $k['nama'] }}</span>
                                <span class="px-2 py-0.5 bg-stone-200 text-stone-700 rounded text-[10px] font-mono font-bold">{{ strtoupper($k['kategori'] ?? 'NILAI') }}</span>
                            </div>
                            <div class="relative">
                                <input type="number" step="0.1" min="0" max="100" wire:model="bobotInputs.{{ $k['id'] }}" 
                                    class="w-full bg-white border border-stone-300 text-stone-900 rounded-xl px-3 py-2 text-xs font-black focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 pr-8 shadow-xs" placeholder="0" />
                                <span class="absolute right-3 top-2 text-xs font-bold text-stone-400">%</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-4 border-t border-stone-200">
                    <div class="text-xs text-stone-600 flex items-center gap-2">
                        <span class="font-bold">Total Akumulasi Bobot:</span>
                        @php $totalCurrent = array_sum(array_map('floatval', $bobotInputs)); @endphp
                        <span class="px-3 py-1 rounded-xl text-xs font-black font-mono border {{ abs($totalCurrent - 100) < 0.01 ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-amber-50 text-amber-800 border-amber-300' }}">
                            {{ $totalCurrent }}% {{ abs($totalCurrent - 100) < 0.01 ? '(Sempurna 100%)' : '(Disarankan 100%)' }}
                        </span>
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2">
                        <x-lucide-save class="w-4 h-4" />
                        <span>Simpan Bobot Penilaian</span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
