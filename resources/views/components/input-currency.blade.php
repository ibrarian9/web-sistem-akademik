@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'placeholder' => '0',
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'prefix' => 'Rp',
])

@php
    $wireModel = $attributes->wire('model');
    $modelName = $wireModel ? $wireModel->value() : null;
    $inputId = $id ?: ($name ?: ($modelName ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $modelName) : 'currency_' . uniqid()));
    $errorBag = $errors ?? session('errors') ?? new \Illuminate\Support\ViewErrorBag();
    $hasError = $error || ($modelName && $errorBag->has($modelName)) || ($name && $errorBag->has($name));
    $errorMessage = $error ?: ($modelName ? $errorBag->first($modelName) : ($name ? $errorBag->first($name) : null));
@endphp

<div 
    class="space-y-1.5 w-full"
    x-data="{
        rawValue: null,
        displayValue: '',
        formatNumber(val) {
            if (val === null || val === undefined || val === '') return '';
            let s = val.toString().trim();
            if (!s) return '';
            let isNegative = s.startsWith('-');
            s = s.replace(/^-/, '');

            let integerPart = '';
            let decimalPart = '';

            if (s.includes(',')) {
                let parts = s.split(',');
                integerPart = parts[0].replace(/[^0-9]/g, '');
                decimalPart = parts[1] !== undefined ? ',' + parts[1].replace(/[^0-9]/g, '') : '';
            } else if (/^\d+\.\d+$/.test(s)) {
                let parts = s.split('.');
                integerPart = parts[0].replace(/[^0-9]/g, '');
                decimalPart = parts[1] !== undefined ? ',' + parts[1] : '';
            } else {
                integerPart = s.replace(/[^0-9]/g, '');
            }

            if (!integerPart && !decimalPart) return '';
            let formattedInt = integerPart ? integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '0';
            return (isNegative ? '-' : '') + formattedInt + decimalPart;
        },
        syncFromRaw() {
            if (this.rawValue === null || this.rawValue === undefined || this.rawValue === '') {
                this.displayValue = '';
                return;
            }
            this.displayValue = this.formatNumber(this.rawValue);
        },
        onInput(e) {
            let inputEl = e.target;
            let oldVal = inputEl.value || '';
            let cursorPos = inputEl.selectionStart || 0;
            let charsBefore = oldVal.slice(0, cursorPos);
            let digitsBefore = charsBefore.replace(/[^0-9,]/g, '').length;

            let isNegative = oldVal.trim().startsWith('-');
            let cleanStr = oldVal.replace(/[^0-9,]/g, '');

            let parts = cleanStr.split(',');
            let intDigits = parts[0] || '';
            let decDigits = parts.length > 1 ? parts.slice(1).join('') : null;

            if (intDigits === '' && decDigits === null) {
                this.rawValue = null;
                this.displayValue = '';
                inputEl.value = '';
                return;
            }

            let formattedInt = intDigits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            let formatted = (isNegative ? '-' : '') + (formattedInt || (decDigits !== null ? '0' : '')) + (decDigits !== null ? ',' + decDigits : '');

            let numVal = null;
            if (decDigits !== null) {
                numVal = parseFloat((isNegative ? '-' : '') + (intDigits || '0') + '.' + decDigits);
            } else if (intDigits !== '') {
                numVal = parseInt((isNegative ? '-' : '') + intDigits, 10);
            }

            this.rawValue = numVal;
            this.displayValue = formatted;
            inputEl.value = formatted;

            let newCursorPos = 0;
            let count = 0;
            for (let i = 0; i < formatted.length; i++) {
                if (/[0-9,]/.test(formatted[i])) {
                    count++;
                }
                if (count === digitsBefore) {
                    newCursorPos = i + 1;
                    break;
                }
            }
            if (digitsBefore === 0) newCursorPos = 0;
            if (count < digitsBefore) newCursorPos = formatted.length;

            inputEl.setSelectionRange(newCursorPos, newCursorPos);
        },
        init() {
            this.syncFromRaw();
            this.$watch('rawValue', (newVal) => {
                if (newVal === null || newVal === undefined || newVal === '') {
                    if (this.displayValue !== '') {
                        this.displayValue = '';
                    }
                    return;
                }
                let formatted = this.formatNumber(newVal);
                if (this.displayValue !== formatted) {
                    this.displayValue = formatted;
                }
            });
        }
    }"
    x-modelable="rawValue"
    {{ $attributes->whereStartsWith(['wire:model', 'x-model']) }}
>
    @if ($label)
        <label for="{{ $inputId }}" class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500 font-bold">*</span>
            @endif
        </label>
    @endif

    <div class="relative flex items-center rounded-xl shadow-2xs">
        @if ($prefix)
            <!-- Prefix Badge -->
            <span class="inline-flex items-center px-3.5 py-2.5 rounded-l-xl border border-r-0 text-xs font-black select-none
                {{ $hasError ? 'bg-rose-100 border-rose-300 text-rose-800' : 'bg-emerald-50 border-stone-300 text-emerald-800' }}">
                {{ $prefix }}
            </span>
        @endif

        <!-- Formatted Currency Input -->
        <input
            id="{{ $inputId }}"
            type="text"
            inputmode="numeric"
            :value="displayValue"
            @input="onInput($event)"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->whereDoesntStartWith(['wire:model', 'x-model', 'class', 'id', 'name', 'placeholder', 'required', 'disabled', 'prefix'])->merge([
                'class' => 'w-full py-2.5 pr-4 pl-3 bg-white border text-stone-900 placeholder-stone-400 text-xs font-black text-right focus:outline-none transition duration-150 ' . 
                    ($prefix ? 'rounded-r-xl ' : 'rounded-xl ') .
                    ($hasError 
                        ? 'border-rose-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 text-rose-900 bg-rose-50/30 ' 
                        : 'border-stone-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 text-emerald-900 ') . 
                    ($disabled ? 'opacity-50 cursor-not-allowed bg-stone-50 ' : '')
            ]) }}
        />

        @if($name)
            <input type="hidden" name="{{ $name }}" :value="rawValue !== null ? rawValue : ''" />
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
