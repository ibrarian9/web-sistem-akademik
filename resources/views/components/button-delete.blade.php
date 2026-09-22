@props([
    'title' => 'Hapus Data',
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
        'class' => 'inline-flex items-center justify-center rounded-lg text-rose-700 bg-rose-50/80 hover:bg-rose-100/80 border border-rose-200/80 transition-all font-semibold active:scale-95 focus:outline-none focus:ring-2 focus:ring-rose-500/20 min-h-[40px] min-w-[40px] sm:min-h-0 sm:min-w-0 ' . $sizeClasses
    ]) }}
>
    <x-lucide-trash-2 class="w-3.5 h-3.5 shrink-0" />
    @if ($size === 'md' && $slot->isNotEmpty())
        <span>{{ $slot }}</span>
    @endif
</button>
