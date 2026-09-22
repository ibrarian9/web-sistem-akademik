@props([
    'title' => '',
    'subtitle' => null,
    'badge' => null,
    'badgeVariant' => 'emerald',
    'icon' => null,
    'breadcrumbs' => [],
])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-stone-200 p-6 rounded-2xl shadow-xs">
    <div class="space-y-1">
        @if (!empty($breadcrumbs))
            <x-breadcrumb :items="$breadcrumbs" />
        @endif

        <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight flex items-center gap-2.5 flex-wrap">
            @if ($icon)
                <x-dynamic-component :component="'lucide-' . $icon" class="w-6 h-6 text-emerald-600 shrink-0" />
            @endif
            <span>{{ $title }}</span>
            @if ($badge)
                <x-badge :variant="$badgeVariant" size="xs">{{ $badge }}</x-badge>
            @endif
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
