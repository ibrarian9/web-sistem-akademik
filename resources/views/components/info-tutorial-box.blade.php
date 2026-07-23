@props([
    'title' => 'Panduan & Petunjuk Penggunaan Halaman',
    'steps' => [],
    'notes' => null
])

<div x-data="{ open: true }" class="bg-emerald-50/90 border border-emerald-200/90 rounded-2xl p-4 shadow-sm mb-6 transition-all duration-200">
    <div class="flex items-center justify-between cursor-pointer select-none" @click="open = !open">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-sm shrink-0">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-emerald-950 uppercase tracking-wider flex items-center gap-1.5">
                    <span>{{ $title }}</span>
                </h4>
                <p class="text-xs text-emerald-800 font-medium mt-0.5">Klik untuk menyembunyikan / menampilkan petunjuk penggunaan.</p>
            </div>
        </div>
        <button type="button" class="text-emerald-700 hover:text-emerald-900 transition p-1.5 bg-white/80 hover:bg-white rounded-xl border border-emerald-200 shadow-xs flex items-center gap-1 text-xs font-semibold">
            <span x-text="open ? 'Sembunyikan' : 'Tampilkan'"></span>
            <svg class="w-4 h-4 transform transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>

    <div x-show="open" x-collapse class="mt-3.5 pt-3.5 border-t border-emerald-200/70 space-y-3">
        @if (!empty($steps))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($steps as $index => $step)
                    <div class="bg-white/90 border border-emerald-100 rounded-xl p-3 flex items-start gap-2.5 shadow-xs">
                        <span class="w-5 h-5 rounded-lg bg-emerald-600 text-white font-bold text-[11px] flex items-center justify-center shrink-0 shadow-xs">
                            {{ $index + 1 }}
                        </span>
                        <div class="space-y-0.5 min-w-0">
                            <div class="font-bold text-stone-900 text-xs tracking-tight">{{ $step['title'] ?? '' }}</div>
                            <div class="text-[11px] text-stone-600 leading-relaxed font-normal">{{ $step['desc'] ?? '' }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if (!empty($notes))
            <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-xs flex items-start gap-2.5 shadow-xs">
                <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="leading-relaxed">
                    <strong class="font-bold uppercase tracking-wider text-[11px] text-amber-900 block mb-0.5">Catatan / Tips Penting:</strong> 
                    <span class="text-amber-800 font-medium">{{ $notes }}</span>
                </div>
            </div>
        @endif
    </div>
</div>


