@props(['title', 'value', 'subtext' => null, 'icon' => null, 'color' => 'indigo'])

@php
    $iconClasses = match ($color) {
        'emerald' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-emerald-500/5',
        'rose' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20 shadow-rose-500/5',
        'amber' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20 shadow-amber-500/5',
        default => 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 shadow-indigo-500/5',
    };
@endphp

<div class="p-6 bg-slate-900/60 border border-slate-800 rounded-2xl shadow-lg flex items-center justify-between group hover:border-slate-700 transition duration-300">
    <div class="space-y-1">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $title }}</span>
        <h3 class="text-2xl font-bold text-white tracking-tight">{{ $value }}</h3>
        @if ($subtext)
            <p class="text-xs text-slate-400">{{ $subtext }}</p>
        @endif
    </div>

    @if ($icon)
        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-300 {{ $iconClasses }}">
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
                @default <x-lucide-info class="w-5 h-5" />
            @endswitch
        </div>
    @endif
</div>
