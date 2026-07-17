@props(['title', 'value', 'subtext' => null, 'icon' => null, 'color' => 'green'])

@php
    $iconClasses = match ($color) {
        'emerald', 'green' => 'bg-green-50 text-green-600 border border-green-200',
        'rose', 'red' => 'bg-red-50 text-red-600 border border-red-200',
        'amber', 'yellow' => 'bg-amber-50 text-amber-600 border border-amber-200',
        'blue' => 'bg-blue-50 text-blue-600 border border-blue-200',
        default => 'bg-green-50 text-green-600 border border-green-200',
    };

    $valueColor = match ($color) {
        'emerald', 'green' => 'text-green-700',
        'rose', 'red' => 'text-red-700',
        'amber', 'yellow' => 'text-amber-700',
        'blue' => 'text-blue-700',
        default => 'text-stone-800',
    };
@endphp

<div class="p-5 bg-white border border-stone-200 rounded-2xl shadow-sm flex items-center justify-between group hover:shadow-md hover:border-stone-300 transition duration-300">
    <div class="space-y-1">
        <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">{{ $title }}</span>
        <h3 class="text-2xl font-bold {{ $valueColor }} tracking-tight">{{ $value }}</h3>
        @if ($subtext)
            <p class="text-sm text-stone-500">{{ $subtext }}</p>
        @endif
    </div>

    @if ($icon)
        <div class="w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-300 {{ $iconClasses }}">
            @switch($icon)
                @case('users') <x-lucide-users class="w-5 h-5" /> @break
                @case('user-check') <x-lucide-user-check class="w-5 h-5" /> @break
                @case('wallet') <x-lucide-wallet class="w-5 h-5" /> @break
                @case('calendar') <x-lucide-calendar class="w-5 h-5" /> @break
                @case('trending-up') <x-lucide-trending-up class="w-5 h-5" /> @break
                @case('trending-down') <x-lucide-trending-down class="w-5 h-5" /> @break
                @case('activity') <x-lucide-activity class="w-5 h-5" /> @break
                @case('clock') <x-lucide-clock class="w-5 h-5" /> @break
                @case('award') <x-lucide-award class="w-5 h-5" /> @break
                @case('file-text') <x-lucide-file-text class="w-5 h-5" /> @break
                @case('alert-triangle') <x-lucide-alert-triangle class="w-5 h-5" /> @break
                @case('dollar-sign') <x-lucide-dollar-sign class="w-5 h-5" /> @break
                @case('check-circle') <x-lucide-check-circle class="w-5 h-5" /> @break
                @case('eye') <x-lucide-eye class="w-5 h-5" /> @break
                @case('percent') <x-lucide-percent class="w-5 h-5" /> @break
                @default <x-lucide-info class="w-5 h-5" />
            @endswitch
        </div>
    @endif
</div>
