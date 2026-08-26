@props([
    'active' => 'bab-tp',
])

@php
    $jGuru = strtolower(auth()->user()->guru?->jenis_guru ?? 'umum');
    if ($jGuru === 'tahfidz') $jGuru = 'tahfizh';
    $isTahfizh = $jGuru === 'tahfizh';
    $isUmum = $jGuru === 'umum';
    $isKeduanya = $jGuru === 'keduanya' || auth()->user()->role?->nama !== 'guru';

    $activeClass = 'bg-emerald-600 text-white shadow-xs border-emerald-700 font-black';
    $inactiveClass = 'bg-stone-50 hover:bg-stone-100 text-stone-700 hover:text-stone-900 border-stone-200 hover:border-stone-300 font-bold';
@endphp

<!-- Responsive Quick Module Switcher Navigation -->
<div class="bg-white border border-stone-200 p-2 sm:p-2.5 rounded-2xl shadow-xs">
    <div class="grid grid-cols-2 sm:grid-cols-3 md:flex md:flex-wrap md:items-center gap-2">
        @if($isUmum || $isKeduanya)
            <!-- 1. Setup Bab & TP -->
            <a href="{{ route('guru.kurikulum-merdeka') }}" 
               wire:navigate 
               class="px-3.5 py-2.5 rounded-xl text-xs border transition flex items-center justify-center sm:justify-start gap-2 text-center sm:text-left {{ $active === 'bab-tp' ? $activeClass : $inactiveClass }}">
                <svg class="w-4 h-4 shrink-0 {{ $active === 'bab-tp' ? 'text-emerald-100' : 'text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 01-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span class="truncate">Setup Bab & TP</span>
            </a>

            <!-- 2. Nilai Sumatif -->
            <a href="{{ route('guru.input-sumatif') }}" 
               wire:navigate 
               class="px-3.5 py-2.5 rounded-xl text-xs border transition flex items-center justify-center sm:justify-start gap-2 text-center sm:text-left {{ $active === 'sumatif' ? $activeClass : $inactiveClass }}">
                <svg class="w-4 h-4 shrink-0 {{ $active === 'sumatif' ? 'text-emerald-100' : 'text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span class="truncate">Nilai Sumatif</span>
            </a>
        @endif

        @if($isTahfizh || $isKeduanya)
            <!-- 3. Setoran Tahfizh -->
            <a href="{{ route('guru.input-tahfidz') }}" 
               wire:navigate 
               class="px-3.5 py-2.5 rounded-xl text-xs border transition flex items-center justify-center sm:justify-start gap-2 text-center sm:text-left {{ $active === 'tahfidz' ? $activeClass : $inactiveClass }}">
                <svg class="w-4 h-4 shrink-0 {{ $active === 'tahfidz' ? 'text-emerald-100' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="truncate">Setoran Tahfizh</span>
            </a>
        @endif

        @if($isUmum || $isKeduanya)
            <!-- 4. Penilaian P5 -->
            <a href="{{ route('guru.penilaian-p5') }}" 
               wire:navigate 
               class="px-3.5 py-2.5 rounded-xl text-xs border transition flex items-center justify-center sm:justify-start gap-2 text-center sm:text-left {{ $active === 'p5' ? $activeClass : $inactiveClass }}">
                <svg class="w-4 h-4 shrink-0 {{ $active === 'p5' ? 'text-emerald-100' : 'text-cyan-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <span class="truncate">Penilaian P5</span>
            </a>

            <!-- 5. Bobot Nilai -->
            @if(Route::has('guru.bobot-nilai'))
                <a href="{{ route('guru.bobot-nilai') }}" 
                   wire:navigate 
                   class="px-3.5 py-2.5 rounded-xl text-xs border transition flex items-center justify-center sm:justify-start gap-2 text-center sm:text-left {{ $active === 'bobot' ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ $active === 'bobot' ? 'text-emerald-100' : 'text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span class="truncate">Bobot Nilai</span>
                </a>
            @endif
        @endif

        <!-- 6. Lihat Rapor Murid -->
        <a href="{{ route('guru.kelola-rapor') }}" 
           wire:navigate 
           class="px-3.5 py-2.5 rounded-xl text-xs border transition flex items-center justify-center sm:justify-start gap-2 text-center sm:text-left {{ $active === 'rapor' ? $activeClass : $inactiveClass }}">
            <svg class="w-4 h-4 shrink-0 {{ $active === 'rapor' ? 'text-emerald-100' : 'text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span class="truncate">Rapor Murid</span>
        </a>
    </div>
</div>
