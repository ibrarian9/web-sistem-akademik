@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white border border-stone-200/80 rounded-2xl shadow-xs overflow-hidden transition-all duration-200']) }}>
    @if ($title || isset($header) || isset($actions))
        <div class="px-5 py-4 border-b border-stone-100 flex flex-wrap items-center justify-between gap-3 bg-stone-50/50">
            <div class="flex items-center gap-2.5 min-w-0">
                @if ($icon)
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-700 shrink-0">
                        <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4" />
                    </div>
                @endif
                <div>
                    @if ($title)
                        <h3 class="font-bold text-stone-900 text-sm tracking-tight leading-snug">{{ $title }}</h3>
                    @endif
                    @if ($subtitle)
                        <p class="text-xs text-stone-500 font-medium leading-normal mt-0.5">{{ $subtitle }}</p>
                    @endif
                    @if (isset($header))
                        {{ $header }}
                    @endif
                </div>
            </div>

            @if (isset($actions))
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div @class(['p-5' => $padding])>
        {{ $slot }}
    </div>

    @if (isset($footer))
        <div class="px-5 py-3.5 border-t border-stone-100 bg-stone-50/40 flex items-center justify-between gap-3">
            {{ $footer }}
        </div>
    @endif
</div>
