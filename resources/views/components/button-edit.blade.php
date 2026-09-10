@props([
    'title' => 'Edit Data',
    'size' => 'sm',
])

@php
    $sizeClasses = match ($size) {
        'xs' => 'p-1 text-xs',
        'sm' => 'p-1.5 text-xs',
        'md' => 'px-3 py-1.5 text-xs gap-1.5',
        default => 'p-1.5 text-xs',
    };
@endphp

<button
    type="button"
    title="{{ $title }}"
    {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center rounded-lg text-amber-700 bg-amber-50/80 hover:bg-amber-100/80 border border-amber-200/80 transition-all font-semibold active:scale-95 focus:outline-none focus:ring-2 focus:ring-amber-500/20 ' . $sizeClasses
    ]) }}
>
    <x-lucide-pencil class="w-3.5 h-3.5 shrink-0" />
    @if ($size === 'md' && $slot->isNotEmpty())
        <span>{{ $slot }}</span>
    @endif
</button>
