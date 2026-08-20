@props([
    'href' => null,
    'icon' => null,
    'danger' => false,
    'wireNavigate' => true,
])

@php
    $baseClasses = 'w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold transition text-left select-none cursor-pointer ';
    $colorClasses = $danger 
        ? 'text-rose-700 hover:bg-rose-50 hover:text-rose-800' 
        : 'text-stone-700 hover:bg-stone-100 hover:text-stone-900';
@endphp

@if ($href)
    <a href="{{ $href }}"
       @if($wireNavigate) wire:navigate @endif
       {{ $attributes->merge(['class' => $baseClasses . $colorClasses]) }}>
        @if ($icon)
            <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4 shrink-0 {{ $danger ? 'text-rose-500' : 'text-stone-400' }}" />
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button type="button" {{ $attributes->merge(['class' => $baseClasses . $colorClasses]) }}>
        @if ($icon)
            <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4 shrink-0 {{ $danger ? 'text-rose-500' : 'text-stone-400' }}" />
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif
