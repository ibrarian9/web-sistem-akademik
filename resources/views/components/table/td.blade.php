@props([
    'align' => 'left',
    'bordered' => true,
])

@php
    $alignClasses = match($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
@endphp

<td {{ $attributes->merge(['class' => 'p-3 text-xs ' . $alignClasses . ($bordered ? ' border-r border-stone-200' : '')]) }}>
    {{ $slot }}
</td>
