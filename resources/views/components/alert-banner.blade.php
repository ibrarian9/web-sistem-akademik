@props(['type' => 'info', 'message' => ''])

@php
    $classes = match ($type) {
        'error' => 'bg-red-50 border-red-200 text-red-700',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-700',
        'success' => 'bg-green-50 border-green-200 text-green-700',
        default => 'bg-blue-50 border-blue-200 text-blue-700',
    };
@endphp

<div class="p-4 rounded-xl border flex items-start gap-3 shadow-sm {{ $classes }}">
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
