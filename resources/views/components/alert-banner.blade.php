@props(['type' => 'info', 'message' => ''])

@php
    $classes = match ($type) {
        'error' => 'bg-rose-500/10 border-rose-500/20 text-rose-400',
        'warning' => 'bg-amber-500/10 border-amber-500/20 text-amber-400',
        'success' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
        default => 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400',
    };
@endphp

<div class="p-4 rounded-xl border flex items-start gap-3 shadow-md shadow-slate-950/20 {{ $classes }}">
    <div class="shrink-0 mt-0.5">
        @switch($type)
            @case('error') <x-lucide-x-circle class="w-5 h-5" /> @break
            @case('warning') <x-lucide-alert-triangle class="w-5 h-5" /> @break
            @case('success') <x-lucide-check-circle class="w-5 h-5" /> @break
            @default <x-lucide-info class="w-5 h-5" />
        @endswitch
    </div>
    <div class="text-sm font-medium leading-relaxed">
        {{ $slot->isEmpty() ? $message : $slot }}
    </div>
</div>
