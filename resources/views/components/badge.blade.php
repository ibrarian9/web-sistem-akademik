@props([
    'variant' => 'emerald',
    'size' => 'sm',
    'dot' => false,
    'icon' => null,
])

@php
    $variants = [
        'emerald' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
        'success' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
        'rose' => 'bg-rose-100 text-rose-900 border-rose-300',
        'danger' => 'bg-rose-100 text-rose-900 border-rose-300',
        'amber' => 'bg-amber-100 text-amber-900 border-amber-300',
        'warning' => 'bg-amber-100 text-amber-900 border-amber-300',
        'sky' => 'bg-sky-100 text-sky-900 border-sky-300',
        'info' => 'bg-sky-100 text-sky-900 border-sky-300',
        'purple' => 'bg-purple-100 text-purple-900 border-purple-300',
        'stone' => 'bg-stone-100 text-stone-800 border-stone-300',
        'neutral' => 'bg-stone-100 text-stone-800 border-stone-300',
    ];

    $dotColors = [
        'emerald' => 'bg-emerald-500',
        'success' => 'bg-emerald-500',
        'rose' => 'bg-rose-500',
        'danger' => 'bg-rose-500',
        'amber' => 'bg-amber-500',
        'warning' => 'bg-amber-500',
        'sky' => 'bg-sky-500',
        'info' => 'bg-sky-500',
        'purple' => 'bg-purple-500',
        'stone' => 'bg-stone-400',
        'neutral' => 'bg-stone-400',
    ];

    $sizes = [
        'xs' => 'px-2 py-0.5 text-[10px] gap-1 rounded-md',
        'sm' => 'px-2.5 py-1 text-[11px] gap-1.5 rounded-lg',
        'md' => 'px-3 py-1.5 text-xs gap-2 rounded-xl',
    ];

    $iconSizes = [
        'xs' => 'w-3 h-3',
        'sm' => 'w-3.5 h-3.5',
        'md' => 'w-4 h-4',
    ];

    $variantClass = $variants[$variant] ?? $variants['emerald'];
    $dotColorClass = $dotColors[$variant] ?? $dotColors['emerald'];
    $sizeClass = $sizes[$size] ?? $sizes['sm'];
    $iconSizeClass = $iconSizes[$size] ?? $iconSizes['sm'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-bold border $variantClass $sizeClass"]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColorClass }} shrink-0"></span>
    @endif

    @if ($icon)
        <x-dynamic-component :component="'lucide-' . $icon" class="{{ $iconSizeClass }} shrink-0" />
    @endif

    <span>{{ $slot }}</span>
</span>
