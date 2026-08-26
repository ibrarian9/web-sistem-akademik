@props([
    'variant' => 'primary',
    'size' => 'sm',
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconRight' => null,
    'loadingTarget' => null,
    'wireNavigate' => true,
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold transition rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-1 select-none disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer';

    $variants = [
        'primary' => 'bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white shadow-xs focus:ring-emerald-600',
        'secondary' => 'bg-stone-100 hover:bg-stone-200 active:bg-stone-300 text-stone-700 border border-stone-300 shadow-2xs focus:ring-stone-400',
        'danger' => 'bg-rose-50 hover:bg-rose-100 active:bg-rose-200 text-rose-700 border border-rose-200 shadow-2xs focus:ring-rose-400',
        'danger-outline' => 'bg-transparent hover:bg-rose-50 active:bg-rose-100 text-rose-700 border border-rose-300 shadow-2xs focus:ring-rose-400',
        'danger-solid' => 'bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white shadow-xs focus:ring-rose-600',
        'danger-ghost' => 'bg-transparent hover:bg-rose-50 text-rose-700 focus:ring-rose-400',
        'warning' => 'bg-amber-100 hover:bg-amber-200 active:bg-amber-300 text-amber-900 border border-amber-300 shadow-2xs focus:ring-amber-400',
        'outline' => 'bg-transparent hover:bg-emerald-50 text-emerald-800 border border-emerald-600 shadow-2xs focus:ring-emerald-600',
        'ghost' => 'bg-transparent hover:bg-stone-100 text-stone-600 hover:text-stone-900 focus:ring-stone-300',
    ];

    $sizes = [
        'xs' => 'px-2.5 py-1 text-[11px] gap-1.5',
        'sm' => 'px-3.5 py-2 text-xs gap-2',
        'md' => 'px-4 py-2.5 text-xs gap-2.5',
        'lg' => 'px-5 py-3 text-sm gap-3',
    ];

    $iconSizes = [
        'xs' => 'w-3.5 h-3.5',
        'sm' => 'w-4 h-4',
        'md' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
    ];

    $variantClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['sm'];
    $iconSizeClass = $iconSizes[$size] ?? $iconSizes['sm'];
    $hasSlot = !empty(trim((string)$slot));
    $shouldWireNavigate = $wireNavigate && !$attributes->has('download') && $attributes->get('target') !== '_blank';
@endphp

@if ($href && !$disabled)
    <a href="{{ $href }}"
       @if($shouldWireNavigate) wire:navigate @endif
       {{ $attributes->merge(['class' => "$baseClasses $variantClass $sizeClass"]) }}>
        @if ($icon)
            <x-dynamic-component :component="'lucide-' . $icon" class="{{ $iconSizeClass }} shrink-0" />
        @endif

        @if ($hasSlot)
            <span>{{ $slot }}</span>
        @endif

        @if ($iconRight)
            <x-dynamic-component :component="'lucide-' . $iconRight" class="{{ $iconSizeClass }} shrink-0" />
        @endif
    </a>
@elseif ($href && $disabled)
    <button type="button"
            disabled
            {{ $attributes->merge(['class' => "$baseClasses $variantClass $sizeClass opacity-50 cursor-not-allowed select-none"]) }}
            title="Tidak ada data untuk diekspor">
        @if ($icon)
            <x-dynamic-component :component="'lucide-' . $icon" class="{{ $iconSizeClass }} shrink-0" />
        @endif

        @if ($hasSlot)
            <span>{{ $slot }}</span>
        @endif

        @if ($iconRight)
            <x-dynamic-component :component="'lucide-' . $iconRight" class="{{ $iconSizeClass }} shrink-0" />
        @endif
    </button>
@else
    <button type="{{ $type }}"
            @if($disabled) disabled @endif
            @if($loadingTarget) wire:loading.attr="disabled" wire:target="{{ $loadingTarget }}" @endif
            {{ $attributes->merge(['class' => "$baseClasses $variantClass $sizeClass"]) }}>
        
        @if($loadingTarget)
            <svg wire:loading wire:target="{{ $loadingTarget }}" class="animate-spin {{ $iconSizeClass }} shrink-0 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        @endif

        @if ($icon)
            <span @if($loadingTarget) wire:loading.remove wire:target="{{ $loadingTarget }}" @endif class="inline-flex">
                <x-dynamic-component :component="'lucide-' . $icon" class="{{ $iconSizeClass }} shrink-0" />
            </span>
        @endif

        @if ($hasSlot)
            <span>{{ $slot }}</span>
        @endif

        @if ($iconRight)
            <span @if($loadingTarget) wire:loading.remove wire:target="{{ $loadingTarget }}" @endif class="inline-flex">
                <x-dynamic-component :component="'lucide-' . $iconRight" class="{{ $iconSizeClass }} shrink-0" />
            </span>
        @endif
    </button>
@endif
