@props([
    'id' => null,
    'maxWidth' => '2xl',
    'title' => null,
    'subtitle' => null,
    'icon' => null,
])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        'full' => 'max-w-full m-4',
        default => 'max-w-2xl',
    };
@endphp

<div
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="{{ $id ? $id . '-title' : 'modal-title' }}"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-stone-900/60 backdrop-blur-xs transition-opacity"
        @click="show = false"
    ></div>

    {{-- Dialog wrapper --}}
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full {{ $maxWidthClass }} border border-stone-200 my-8"
        >
            {{-- Header --}}
            @if ($title || isset($header))
                <div class="px-6 py-4 border-b border-stone-100 flex items-center justify-between gap-4 bg-stone-50/70">
                    <div class="flex items-center gap-3 min-w-0">
                        @if ($icon)
                            <div class="w-9 h-9 rounded-xl bg-emerald-100/70 text-emerald-800 border border-emerald-200 flex items-center justify-center shrink-0">
                                <x-dynamic-component :component="'lucide-' . $icon" class="w-5 h-5" />
                            </div>
                        @endif
                        <div>
                            @if ($title)
                                <h3 id="{{ $id ? $id . '-title' : 'modal-title' }}" class="text-sm font-bold text-stone-900 tracking-tight leading-snug">
                                    {{ $title }}
                                </h3>
                            @endif
                            @if ($subtitle)
                                <p class="text-xs text-stone-500 font-medium leading-normal mt-0.5">{{ $subtitle }}</p>
                            @endif
                            @if (isset($header))
                                {{ $header }}
                            @endif
                        </div>
                    </div>

                    <button
                        type="button"
                        class="text-stone-400 hover:text-stone-600 hover:bg-stone-200/60 p-1.5 rounded-lg transition"
                        @click="show = false"
                        aria-label="Tutup modal"
                    >
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>
            @endif

            {{-- Body --}}
            <div class="p-6">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @if (isset($footer))
                <div class="px-6 py-4 border-t border-stone-100 bg-stone-50/70 flex flex-wrap items-center justify-end gap-2.5">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
