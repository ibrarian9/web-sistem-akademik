@props([
    'title' => '',
    'subtitle' => null,
    'badge' => null,
    'badgeVariant' => 'emerald',
    'icon' => null,
])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-stone-200 p-6 rounded-2xl shadow-xs">
    <div class="space-y-1">
        @if ($badge)
            <div class="flex items-center gap-2 mb-1">
                <x-badge :variant="$badgeVariant" size="xs">{{ $badge }}</x-badge>
            </div>
        @endif

        <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight flex items-center gap-2.5">
            @if ($icon)
                <x-dynamic-component :component="'lucide-' . $icon" class="w-6 h-6 text-emerald-600 shrink-0" />
            @endif
            <span>{{ $title }}</span>
        </h1>

        @if ($subtitle)
            <p class="text-xs text-stone-600 font-medium">{{ $subtitle }}</p>
        @endif
    </div>

    @if (isset($actions) || $slot->isNotEmpty())
        <div class="flex items-center gap-2 shrink-0 flex-wrap">
            {{ $actions ?? $slot }}
        </div>
    @endif
</div>
