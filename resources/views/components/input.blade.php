@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => null,
    'icon' => null,
    'iconRight' => null,
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
])

@php
    $inputId = $name ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $name) : 'input_' . uniqid();
    $hasError = $error || ($name && $errors->has($name));
    $errorMessage = $error ?: ($name ? $errors->first($name) : null);
@endphp

<div class="space-y-1.5 w-full">
    @if ($label)
        <label for="{{ $inputId }}" class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500 font-bold">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-xl shadow-2xs">
        @if ($icon)
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4" />
            </div>
        @endif

        <input
            id="{{ $inputId }}"
            type="{{ $type }}"
            @if($name) name="{{ $name }}" @endif
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge([
                'class' => 'w-full py-2.5 bg-white border rounded-xl text-stone-900 placeholder-stone-400 text-xs font-semibold focus:outline-none transition duration-150 ' . 
                    ($icon ? 'pl-10 ' : 'pl-3.5 ') . 
                    ($iconRight ? 'pr-10 ' : 'pr-3.5 ') . 
                    ($hasError 
                        ? 'border-rose-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 text-rose-900 bg-rose-50/30 ' 
                        : 'border-stone-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 ') . 
                    ($disabled ? 'opacity-50 cursor-not-allowed bg-stone-50 ' : '')
            ]) }}
        />

        @if ($iconRight)
            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-stone-400">
                <x-dynamic-component :component="'lucide-' . $iconRight" class="w-4 h-4" />
            </div>
        @endif
    </div>

    @if ($hint && !$hasError)
        <p class="text-[11px] text-stone-500 font-medium">{{ $hint }}</p>
    @endif

    @if ($hasError)
        <p class="text-[11px] text-rose-600 font-bold flex items-center gap-1 mt-1">
            <x-lucide-alert-circle class="w-3.5 h-3.5 shrink-0" />
            <span>{{ $errorMessage }}</span>
        </p>
    @endif
</div>
