@props([
    'title' => 'Grafik Visual',
    'subtitle' => null,
    'badge' => null,
    'badgeVariant' => 'emerald',
    'icon' => null,
    'canvasId' => 'chartCanvas',
    'height' => '260px',
])

<div class="bg-white border border-stone-200 rounded-3xl p-5 sm:p-6 shadow-xs flex flex-col justify-between transition hover:shadow-md">
    <!-- Header -->
    <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-stone-100">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                @if ($icon)
                    <div class="p-1.5 rounded-xl bg-emerald-50 text-emerald-700">
                        <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4" />
                    </div>
                @endif
                <h3 class="text-sm font-extrabold text-stone-900 tracking-tight">{{ $title }}</h3>
            </div>
            @if ($subtitle)
                <p class="text-xs text-stone-500 font-medium">{{ $subtitle }}</p>
            @endif
        </div>

        @if ($badge)
            <x-badge :variant="$badgeVariant" size="xs">
                {{ $badge }}
            </x-badge>
        @elseif (isset($actions))
            {{ $actions }}
        @endif
    </div>

    <!-- Chart Canvas Container -->
    <div class="relative w-full" style="height: {{ $height }};">
        <canvas id="{{ $canvasId }}" class="w-full h-full"></canvas>
    </div>

    @if (isset($footer))
        <div class="mt-4 pt-3 border-t border-stone-100 text-xs text-stone-500">
            {{ $footer }}
        </div>
    @endif
</div>
