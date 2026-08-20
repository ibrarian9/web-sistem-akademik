@props([
    'title' => '',
    'value' => '0',
    'subtitle' => null,
    'icon' => null,
    'variant' => 'white', // 'white', 'emerald', 'rose', 'sky', 'amber', 'purple', 'teal'
    'trend' => null,
])

@php
    $cardClasses = match ($variant) {
        'emerald' => 'bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-xs',
        'rose' => 'bg-gradient-to-br from-rose-600 to-rose-800 text-white shadow-xs',
        'sky' => 'bg-gradient-to-br from-sky-600 to-indigo-800 text-white shadow-xs',
        'amber' => 'bg-gradient-to-br from-amber-500 to-amber-700 text-white shadow-xs',
        'purple' => 'bg-gradient-to-br from-purple-600 to-indigo-800 text-white shadow-xs',
        'teal' => 'bg-gradient-to-br from-teal-600 to-cyan-800 text-white shadow-xs',
        default => 'bg-white border border-stone-200 text-stone-900 shadow-xs',
    };

    $isGradient = $variant !== 'white';
    $titleColor = $isGradient ? 'text-white/80' : 'text-stone-500';
    $valueColor = $isGradient ? 'text-white' : 'text-stone-900';
    $subtitleColor = $isGradient ? 'text-white/70' : 'text-stone-400';
    $iconBg = $isGradient ? 'bg-white/10 text-white' : 'bg-stone-50 text-stone-600 border border-stone-200';
@endphp

<div class="{{ $cardClasses }} rounded-2xl p-5 space-y-2 transition duration-200">
    <div class="flex items-center justify-between gap-3">
        <span class="text-xs font-bold uppercase tracking-wider block {{ $titleColor }}">{{ $title }}</span>
        @if ($icon)
            <div class="p-2.5 rounded-xl {{ $iconBg }} shrink-0">
                <x-dynamic-component :component="'lucide-' . $icon" class="w-5 h-5" />
            </div>
        @endif
    </div>

    <div class="space-y-0.5">
        <h3 class="text-2xl font-black tracking-tight {{ $valueColor }}">{{ $value }}</h3>
        @if ($subtitle)
            <p class="text-[11px] font-semibold {{ $subtitleColor }}">{{ $subtitle }}</p>
        @endif
    </div>

    @if ($trend)
        <div class="pt-1 text-[11px] font-bold {{ $isGradient ? 'text-white' : 'text-emerald-700' }}">
            {{ $trend }}
        </div>
    @endif
</div>
