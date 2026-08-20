@props([
    'loadingTarget' => null,
    'striped' => false,
    'overflow' => true,
])

<div class="relative bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-xs">
    @if ($loadingTarget)
        <!-- Livewire Top Loading Progress Bar -->
        <div wire:loading.delay wire:target="{{ $loadingTarget }}" class="absolute top-0 left-0 right-0 h-1 bg-emerald-100 overflow-hidden z-30">
            <div class="w-full h-full bg-emerald-600 animate-pulse"></div>
        </div>

        <!-- Subtle Translucent Overlay with Loading Text -->
        <div wire:loading.delay wire:target="{{ $loadingTarget }}" class="absolute inset-0 bg-white/60 backdrop-blur-2xs z-20 flex items-center justify-center transition-opacity">
            <div class="px-4 py-2 bg-stone-900/80 text-white rounded-xl text-xs font-bold shadow-lg flex items-center gap-2.5">
                <svg class="animate-spin w-4 h-4 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Memuat data...</span>
            </div>
        </div>
    @endif

    <div @if($overflow) class="overflow-x-auto custom-scrollbar" @endif>
        <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse text-xs text-stone-800']) }}>
            {{ $slot }}
        </table>
    </div>
</div>
