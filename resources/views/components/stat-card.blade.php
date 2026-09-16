@props([
    'title' => '',
    'value' => '0',
    'subtitle' => null,
    'icon' => null,
    'variant' => 'white', // 'white', 'soft-rose', 'soft-emerald', 'soft-amber', 'soft-indigo', 'soft-sky', 'soft-teal', 'soft-purple', 'emerald', 'rose', 'sky', 'amber', 'purple', 'teal'
    'trend' => null,
    'badge' => null,
    'badgeVariant' => null,
    'progress' => null,
])

@php
    $isDarkGradient = in_array($variant, ['emerald', 'rose', 'sky', 'amber', 'purple', 'teal']);
    $isSoft = str_starts_with($variant, 'soft-');

    $cardClasses = match ($variant) {
        // Dark Gradients
        'emerald' => 'bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-xs',
        'rose' => 'bg-gradient-to-br from-rose-600 to-rose-800 text-white shadow-xs',
        'sky' => 'bg-gradient-to-br from-sky-600 to-indigo-800 text-white shadow-xs',
        'amber' => 'bg-gradient-to-br from-amber-500 to-amber-700 text-white shadow-xs',
        'purple' => 'bg-gradient-to-br from-purple-600 to-indigo-800 text-white shadow-xs',
        'teal' => 'bg-gradient-to-br from-teal-600 to-cyan-800 text-white shadow-xs',

        // Soft / Tinted Variants (Modern, High Legibility & Informative)
        'soft-rose' => 'bg-gradient-to-br from-rose-50/90 via-white to-rose-100/40 border border-rose-200/90 text-rose-950 shadow-xs hover:border-rose-300 hover:shadow-sm',
        'soft-emerald' => 'bg-gradient-to-br from-emerald-50/90 via-white to-teal-100/40 border border-emerald-200/90 text-emerald-950 shadow-xs hover:border-emerald-300 hover:shadow-sm',
        'soft-amber' => 'bg-gradient-to-br from-amber-50/90 via-white to-amber-100/40 border border-amber-200/90 text-amber-950 shadow-xs hover:border-amber-300 hover:shadow-sm',
        'soft-indigo' => 'bg-gradient-to-br from-indigo-50/90 via-white to-sky-100/40 border border-indigo-200/90 text-indigo-950 shadow-xs hover:border-indigo-300 hover:shadow-sm',
        'soft-sky' => 'bg-gradient-to-br from-sky-50/90 via-white to-sky-100/40 border border-sky-200/90 text-sky-950 shadow-xs hover:border-sky-300 hover:shadow-sm',
        'soft-teal' => 'bg-gradient-to-br from-teal-50/90 via-white to-cyan-100/40 border border-teal-200/90 text-teal-950 shadow-xs hover:border-teal-300 hover:shadow-sm',
        'soft-purple' => 'bg-gradient-to-br from-purple-50/90 via-white to-purple-100/40 border border-purple-200/90 text-purple-950 shadow-xs hover:border-purple-300 hover:shadow-sm',

        default => 'bg-white border border-stone-200 text-stone-900 shadow-xs hover:border-stone-300',
    };

    $titleColor = match ($variant) {
        'soft-rose' => 'text-rose-700',
        'soft-emerald' => 'text-emerald-700',
        'soft-amber' => 'text-amber-800',
        'soft-indigo' => 'text-indigo-700',
        'soft-sky' => 'text-sky-700',
        'soft-teal' => 'text-teal-700',
        'soft-purple' => 'text-purple-700',
        default => ($isDarkGradient ? 'text-white/80' : 'text-stone-500'),
    };

    $valueColor = match ($variant) {
        'soft-rose' => 'text-rose-950',
        'soft-emerald' => 'text-emerald-950',
        'soft-amber' => 'text-amber-950',
        'soft-indigo' => 'text-indigo-950',
        'soft-sky' => 'text-sky-950',
        'soft-teal' => 'text-teal-950',
        'soft-purple' => 'text-purple-950',
        default => ($isDarkGradient ? 'text-white' : 'text-stone-900'),
    };

    $subtitleColor = match ($variant) {
        'soft-rose' => 'text-rose-600/90',
        'soft-emerald' => 'text-emerald-600/90',
        'soft-amber' => 'text-amber-700/90',
        'soft-indigo' => 'text-indigo-600/90',
        'soft-sky' => 'text-sky-600/90',
        'soft-teal' => 'text-teal-600/90',
        'soft-purple' => 'text-purple-600/90',
        default => ($isDarkGradient ? 'text-white/70' : 'text-stone-400'),
    };

    $iconBg = match ($variant) {
        'soft-rose' => 'bg-rose-500 text-white shadow-xs',
        'soft-emerald' => 'bg-emerald-600 text-white shadow-xs',
        'soft-amber' => 'bg-amber-500 text-white shadow-xs',
        'soft-indigo' => 'bg-indigo-600 text-white shadow-xs',
        'soft-sky' => 'bg-sky-600 text-white shadow-xs',
        'soft-teal' => 'bg-teal-600 text-white shadow-xs',
        'soft-purple' => 'bg-purple-600 text-white shadow-xs',
        default => ($isDarkGradient ? 'bg-white/10 text-white' : 'bg-stone-50 text-stone-600 border border-stone-200'),
    };

    $badgeClass = match ($variant) {
        'soft-rose' => 'bg-rose-100 text-rose-800 border border-rose-200/80',
        'soft-emerald' => 'bg-emerald-100 text-emerald-800 border border-emerald-200/80',
        'soft-amber' => 'bg-amber-100 text-amber-800 border border-amber-200/80',
        'soft-indigo' => 'bg-indigo-100 text-indigo-800 border border-indigo-200/80',
        'soft-sky' => 'bg-sky-100 text-sky-800 border border-sky-200/80',
        'soft-teal' => 'bg-teal-100 text-teal-800 border border-teal-200/80',
        'soft-purple' => 'bg-purple-100 text-purple-800 border border-purple-200/80',
        default => ($isDarkGradient ? 'bg-white/20 text-white border border-white/25' : 'bg-stone-100 text-stone-700 border border-stone-200'),
    };

    $progressBarClass = match ($variant) {
        'soft-rose' => 'bg-rose-500',
        'soft-emerald' => 'bg-emerald-500',
        'soft-amber' => 'bg-amber-500',
        'soft-indigo' => 'bg-indigo-600',
        'soft-sky' => 'bg-sky-500',
        'soft-teal' => 'bg-teal-500',
        'soft-purple' => 'bg-purple-600',
        default => ($isDarkGradient ? 'bg-white' : 'bg-emerald-600'),
    };
@endphp

<div {{ $attributes->merge(['class' => "$cardClasses rounded-2xl p-4 sm:p-5 space-y-2.5 transition-all duration-200 relative overflow-hidden"]) }}>
    <div class="flex items-center justify-between gap-2">
        <span class="text-[11px] sm:text-xs font-black uppercase tracking-wider block {{ $titleColor }} truncate">{{ $title }}</span>
        @if ($badge)
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider shrink-0 {{ $badgeClass }}">
                {{ $badge }}
            </span>
        @endif
    </div>

    <div class="flex items-center justify-between gap-3 pt-0.5">
        <div class="space-y-0.5 min-w-0 flex-1">
            <h3 class="text-xl sm:text-2xl font-black tracking-tight {{ $valueColor }} truncate">{{ $value }}</h3>
            @if ($subtitle)
                <p class="text-[11px] font-semibold {{ $subtitleColor }} line-clamp-1 sm:line-clamp-2 leading-relaxed">{{ $subtitle }}</p>
            @endif
        </div>
        @if ($icon)
            <div class="p-2.5 rounded-xl {{ $iconBg }} shrink-0">
                <x-dynamic-component :component="'lucide-' . $icon" class="w-5 h-5" />
            </div>
        @endif
    </div>

    @if ($progress !== null)
        <div class="pt-1 space-y-1">
            <div class="w-full bg-stone-200/70 rounded-full h-2 overflow-hidden">
                <div class="{{ $progressBarClass }} h-2 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, floatval($progress))) }}%"></div>
            </div>
        </div>
    @endif

    @if ($trend)
        <div class="pt-1 text-[11px] font-bold {{ $isDarkGradient ? 'text-white' : 'text-emerald-700' }}">
            {{ $trend }}
        </div>
    @endif

    {{ $slot ?? '' }}
</div>
