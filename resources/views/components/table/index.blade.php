@props([
    'loadingTarget' => null,
    'striped' => false,
    'overflow' => true,
])

@php
    $cleanTarget = $loadingTarget ? implode(',', array_map('trim', explode(',', $loadingTarget))) : null;
@endphp

<div class="relative bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-xs min-h-[120px]">
    @if ($cleanTarget)
        <!-- Livewire Top Indeterminate Progress Line -->
        <div wire:loading.delay.block wire:target="{{ $cleanTarget }}" style="display: none;" class="absolute top-0 left-0 right-0 h-0.5 bg-emerald-100 overflow-hidden z-30 pointer-events-none">
            <div class="w-full h-full bg-emerald-600 animate-pulse"></div>
        </div>

        <!-- Sleek Translucent Overlay with Centered Indicator Card -->
        <div wire:loading.delay.flex wire:target="{{ $cleanTarget }}" style="display: none;" class="absolute inset-0 bg-white/75 backdrop-blur-[2px] z-20 items-center justify-center transition-all">
            <div class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-white border border-stone-200 text-stone-800 rounded-2xl shadow-xl shadow-stone-900/10">
                <svg class="animate-spin w-4 h-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-85" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-bold text-stone-700 tracking-tight">Memperbarui data...</span>
            </div>
        </div>
    @endif

    <div @if($overflow) class="overflow-x-auto custom-scrollbar" @endif>
        <table {{ $attributes->merge(['class' => 'w-full text-left border-separate border-spacing-0 text-xs text-stone-800']) }}>
            {{ $slot }}
        </table>
    </div>
</div>
