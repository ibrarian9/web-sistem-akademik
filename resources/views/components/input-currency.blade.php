@props([
    'label' => null,
    'name' => null,
    'wireModel' => null,
    'placeholder' => '0',
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
])

@php
    $modelName = $wireModel ?: $attributes->wire('model')->value() ?: $name;
    $inputId = $name ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $name) : 'currency_' . uniqid();
    $hasError = $error || ($modelName && $errors->has($modelName));
    $errorMessage = $error ?: ($modelName ? $errors->first($modelName) : null);
@endphp

<div class="space-y-1.5 w-full"
     x-data="{
        rawValue: @if($modelName) @entangle($modelName) @else 0 @endif,
        displayValue: '',
        formatCurrency(val) {
            if (val === null || val === undefined || val === '') return '';
            if (typeof val === 'number') return Math.round(val).toLocaleString('id-ID');
            let s = val.toString().trim();
            if (/^-?\\d+\\.\\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
            let clean = s.replace(/[^0-9]/g, '');
            return clean ? Number(clean).toLocaleString('id-ID') : '';
        },
        onInput(e) {
            let inputStr = e.target.value;
            let cleanDigits = inputStr.replace(/[^0-9]/g, '');
            let numericVal = cleanDigits ? parseInt(cleanDigits, 10) : 0;
            
            this.rawValue = numericVal;
            this.displayValue = cleanDigits ? Number(cleanDigits).toLocaleString('id-ID') : '';
            e.target.value = this.displayValue;
        },
        init() {
            if (this.rawValue !== null && this.rawValue !== undefined && this.rawValue !== '') {
                this.displayValue = this.formatCurrency(this.rawValue);
            }
            this.$watch('rawValue', (newVal) => {
                if (newVal === null || newVal === undefined || newVal === '') {
                    this.displayValue = '';
                    return;
                }
                let cleanCurrent = this.displayValue.replace(/[^0-9]/g, '');
                let numCurrent = cleanCurrent ? parseInt(cleanCurrent, 10) : 0;
                let s = newVal.toString().trim();
                let numNew = /^-?\\d+\\.\\d{1,2}$/.test(s) ? Math.round(parseFloat(s)) : (parseInt(s.replace(/[^0-9]/g, ''), 10) || 0);
                
                if (numCurrent !== numNew) {
                    this.displayValue = this.formatCurrency(newVal);
                }
            });
        }
     }">
    @if ($label)
        <label for="{{ $inputId }}" class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500 font-bold">*</span>
            @endif
        </label>
    @endif

    <div class="relative flex items-center rounded-xl shadow-2xs">
        <!-- Rp Prefix Badge -->
        <span class="inline-flex items-center px-3.5 py-2.5 rounded-l-xl border border-r-0 text-xs font-black select-none
            {{ $hasError ? 'bg-rose-100 border-rose-300 text-rose-800' : 'bg-emerald-50 border-stone-300 text-emerald-800' }}">
            Rp
        </span>

        <!-- Formatted Currency Input -->
        <input
            id="{{ $inputId }}"
            type="text"
            inputmode="numeric"
            x-model="displayValue"
            @input="onInput($event)"
            @if($name) name="{{ $name }}" @endif
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->whereDoesntStartWith('wire:model')->merge([
                'class' => 'w-full py-2.5 pr-4 pl-3 bg-white border rounded-r-xl text-stone-900 placeholder-stone-400 text-xs font-black text-right focus:outline-none transition duration-150 ' . 
                    ($hasError 
                        ? 'border-rose-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 text-rose-900 bg-rose-50/30 ' 
                        : 'border-stone-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 text-emerald-900 ') . 
                    ($disabled ? 'opacity-50 cursor-not-allowed bg-stone-50 ' : '')
            ]) }}
        />
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
