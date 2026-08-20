@props([
    'align' => 'left',
    'variant' => 'emerald',
])

@php
    $alignClasses = match($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };

    $variantClasses = match($variant) {
        'stone' => 'bg-stone-50 text-stone-700 border-b border-stone-200 font-extrabold',
        'dark' => 'bg-stone-900 text-white border-b border-stone-800 font-extrabold',
        default => 'bg-emerald-800 text-white border-r border-emerald-700/60 font-extrabold',
    };
@endphp

<th {{ $attributes->merge(['class' => "p-3 uppercase tracking-wider text-xs $alignClasses $variantClasses"]) }}>
    {{ $slot }}
</th>
