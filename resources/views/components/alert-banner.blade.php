@props(['type' => 'info', 'message' => ''])

@php
    $msgText = $slot->isEmpty() ? $message : $slot;
    $classes = match ($type) {
        'error' => 'bg-rose-50 border-rose-200 text-rose-800',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
        'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
        default => 'bg-sky-50 border-sky-200 text-sky-800',
    };
@endphp

<div role="alert" data-alert-message="{{ strip_tags($msgText) }}" data-alert-type="{{ $type }}" class="p-4 border rounded-2xl flex items-start gap-3 shadow-xs {{ $classes }}">
    <div class="shrink-0 mt-0.5">
        @switch($type)
            @case('error') <x-lucide-x-circle class="w-5 h-5 text-rose-600" /> @break
            @case('warning') <x-lucide-alert-triangle class="w-5 h-5 text-amber-600" /> @break
            @case('success') <x-lucide-check-circle class="w-5 h-5 text-emerald-600" /> @break
            @default <x-lucide-info class="w-5 h-5 text-sky-600" />
        @endswitch
    </div>
    <div class="text-xs font-bold leading-relaxed">
        {{ $msgText }}
    </div>
</div>
